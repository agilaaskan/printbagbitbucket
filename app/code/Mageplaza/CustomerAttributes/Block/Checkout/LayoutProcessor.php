<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomerAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerAttributes\Block\Checkout;

use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory;
use Magento\Swatches\Model\Swatch;
use Magento\Ui\Component\Form\AttributeMapper;
use Mageplaza\CustomerAttributes\Helper\Data;
use Zend_Serializer_Exception;

/**
 * Class LayoutProcessor
 * @package Mageplaza\CustomerAttributes\Block\Checkout
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Media
     */
    protected $swatchHelper;

    /**
     * @var CollectionFactory
     */
    protected $swatchCollection;

    /**
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param AttributeMapper $attributeMapper
     * @param AttributeMerger $merger
     * @param Data $dataHelper
     * @param Media $swatchHelper
     * @param CollectionFactory $swatchCollection
     */
    public function __construct(
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeMapper $attributeMapper,
        AttributeMerger $merger,
        Data $dataHelper,
        Media $swatchHelper,
        CollectionFactory $swatchCollection
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper               = $attributeMapper;
        $this->merger                        = $merger;
        $this->dataHelper                    = $dataHelper;
        $this->swatchHelper                  = $swatchHelper;
        $this->swatchCollection              = $swatchCollection;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     *
     * @return array
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function process($jsLayout)
    {
        $paymentMethodRenders = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children']['payments-list']['children'];
        if (is_array($paymentMethodRenders)) {
            foreach ($paymentMethodRenders as $name => $renderer) {
                if (isset($renderer['children']) && array_key_exists('form-fields', $renderer['children'])) {
                    $fields = $renderer['children']['form-fields']['children'];
                    $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                    ['children']['payment']['children']['payments-list']['children'][$name]['children']
                    ['form-fields']['children']
                            = $this->getAddressFieldset(
                                $fields,
                                $renderer['dataScopePrefix'] . '.custom_attributes',
                                'billing'
                            );
                }
            }
        }

        // Fix Payment Page
        $fields = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['afterMethods']['children'];
        foreach ($fields as $code => $field) {
            if ($field['component'] === 'Magento_Checkout/js/view/billing-address') {
                $fieldsBillingAddress = $field['children']['form-fields']['children'];

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children'][$code]['children']['form-fields']['children']
                    = $this->getAddressFieldset(
                        $fieldsBillingAddress,
                        'billingAddressshared.custom_attributes',
                        'billing'
                    );
            }
        }

        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'])) {
            $fields = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
                    = $this->getAddressFieldset($fields, 'shippingAddress.custom_attributes', 'shipping');
        }

        if ($this->dataHelper->isOscPage() &&
            isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['billingAddress']['children']['billing-address-fieldset']['children'])) {
            $fields = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['billingAddress']['children']['billing-address-fieldset']['children'];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['billingAddress']['children']['billing-address-fieldset']['children']
                    = $this->getAddressFieldset($fields, 'billingAddress.custom_attributes', 'billing');
        }

        return $jsLayout;
    }

    /**
     * Get address fieldset for shipping/billing address
     *
     * @param $fields
     * @param $type
     * @param $addressType
     *
     * @return array
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function getAddressFieldset($fields, $type, $addressType)
    {
        $formCode   = $this->dataHelper->isOscPage() ? 'onestepcheckout_index_index' : 'checkout_index_index';
        $attributes = $this->dataHelper->getAttributeWithFilters('customer_address', $formCode);

        $elements = [];
        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $elements[$attribute->getAttributeCode()] = $this->attributeMapper->map($attribute);
        }

        $fields = $this->merger->merge(
            $elements,
            'checkoutProvider',
            $type,
            $fields
        );

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $frontendInput = $attribute->getFrontendInput();

            $additionalData = $this->dataHelper->getAdditionalData($attribute);
            if (!empty($additionalData[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                $frontendInput .= '_' . $additionalData[Swatch::SWATCH_INPUT_TYPE_KEY];
            }

            $element = &$fields[$attribute->getAttributeCode()];

            if ($component = $this->dataHelper->getComponentByInputType($frontendInput, $addressType)) {
                $element['component'] = $component;
            }
            if ($elementTmpl = $this->dataHelper->getElementTmplByInputType($frontendInput)) {
                $element['config']['elementTmpl'] = $elementTmpl;
            }
            $element['disabled'] = !$attribute->getCustomerCanEdit();

            if ($default = $attribute->getDefaultValue()) {
                $element['default'] = $attribute->getDefaultValue();
            } else {
                $element['default'] = $frontendInput === 'select_visual' ? true : null;
            }

            switch ($frontendInput) {
                case 'select':
                    $element['options'] = $attribute->getSource()->getAllOptions();
                    array_unshift($element['options'], ['label' => __('Please select an option'), 'value' => '']);
                    break;
                case 'select_visual':
                case 'multiselect_visual':
                    $this->processVisualOption($element, $attribute);
                    break;
                case 'date':
                    $classes = $element['config']['additionalClasses'] ?? '';

                    $element['config']['additionalClasses'] = $classes . ' date';
                    $element['options']                     = [
                        'changeMonth' => true,
                        'changeYear'  => true,
                        'showOn'      => 'both',
                    ];
                    break;
            }
        }

        return $fields;
    }

    /**
     * @param array $element
     * @param Attribute $attribute
     *
     * @throws LocalizedException
     */
    private function processVisualOption(&$element, $attribute)
    {
        $element['options'] = $attribute->getSource()->getAllOptions();

        foreach ($element['options'] as &$option) {
            $optionId   = $attribute->getSource()->getOptionId($option['value']);
            $collection = $this->swatchCollection->create()->addFieldToFilter('option_id', $optionId);
            $item       = $collection->getFirstItem();

            switch ($item->getType()) {
                case 1:
                    $option['visual'] = '<div class="color" style="background-color: ' . $item->getValue() . '"></div>';
                    break;
                case 2:
                    $src = $this->swatchHelper->getSwatchAttributeImage('swatch_thumb', $item->getValue());

                    $option['visual'] = '<img class="image" src="' . $src . '">';
                    break;
                default:
                    $option['visual'] = '';
                    break;
            }
        }
    }
}

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

namespace Mageplaza\CustomerAttributes\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Swatches\Model\Swatch;
use Mageplaza\CustomerAttributes\Helper\Data;
use Zend_Serializer_Exception;

/**
 * Class CustomerEditTab
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml
 */
class CustomerEditTab extends Template
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var array
     */
    protected $entityTypes = ['customer', 'customer_address'];

    /**
     * @var array
     */
    protected $formCodes = [
        'customer_account_create',
        'customer_account_edit',
        'customer_register_address',
        'customer_address_edit'
    ];

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws Zend_Serializer_Exception
     */
    public function getConfig()
    {
        $config = [
            'dependency'    => $this->getAttributeDataDependency(),
            'contentType'   => $this->getAttributeDataContentType(),
            'tinymceConfig' => $this->dataHelper->getTinymceConfig()
        ];

        return Data::jsonEncode($config);
    }

    /**
     * @return array
     */
    protected function getAttributeDataDependency()
    {
        $data = [];
        foreach ($this->entityTypes as $entityType) {
            foreach ($this->formCodes as $formCode) {
                $attributes = $this->dataHelper->getAttributeWithFilters($entityType, $formCode, true);
                foreach ($attributes as $attribute) {
                    if ($attribute->getFieldDepend() || $attribute->getFrontendInput() == 'select') {
                        $data[] = $attribute->getData();
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @return array
     * @throws Zend_Serializer_Exception
     */
    protected function getAttributeDataContentType()
    {
        $data     = [];
        $attrCode = [];
        foreach ($this->entityTypes as $entityType) {
            foreach ($this->formCodes as $formCode) {
                $attributes = $this->dataHelper->getAttributeWithFilters($entityType, $formCode, true);
                foreach ($attributes as $attribute) {
                    if (!in_array(
                        $attribute->getAttributeCode(),
                        $attrCode
                    ) && $attribute->getFrontendInput() == 'textarea') {
                        $additionalData = $this->dataHelper->getAdditionalData($attribute);
                        if (!empty($additionalData[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                            $data[]     = $attribute->getData();
                            $attrCode[] = $attribute->getAttributeCode();
                        }
                    }
                }
            }
        }

        return $data;
    }
}

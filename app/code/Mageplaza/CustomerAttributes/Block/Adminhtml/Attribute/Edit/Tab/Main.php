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

namespace Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Customer\Model\Attribute;
use Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Eav\Helper\Data;
use Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Swatches\Model\Swatch;
use Mageplaza\CustomerAttributes\Helper\Data as DataHelper;
use Mageplaza\CustomerAttributes\Model\Config\Source\InputTypeFactory as CustomInputTypeFactory;

/**
 * Class Main
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab
 */
class Main extends AbstractMain
{
    /**
     * @var CustomInputTypeFactory
     */
    protected $customInputTypeFactory;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Send constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Data $eavData
     * @param YesnoFactory $yesnoFactory
     * @param InputtypeFactory $inputTypeFactory
     * @param PropertyLocker $propertyLocker
     * @param CustomInputTypeFactory $customInputTypeFactory
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $eavData,
        YesnoFactory $yesnoFactory,
        InputtypeFactory $inputTypeFactory,
        PropertyLocker $propertyLocker,
        CustomInputTypeFactory $customInputTypeFactory,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->customInputTypeFactory = $customInputTypeFactory;
        $this->dataHelper             = $dataHelper;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $eavData,
            $yesnoFactory,
            $inputTypeFactory,
            $propertyLocker,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var Attribute $attributeObject */
        $attributeObject = $this->getAttributeObject();

        /** @var Form $form */
        $form = $this->getForm();

        /** @var Fieldset $fieldset */
        $fieldset       = $form->getElement('base_fieldset');
        $fieldsToRemove = ['attribute_code', 'is_unique', 'frontend_class'];

        foreach ($fieldset->getElements() as $element) {
            /** @var AbstractElement $element */
            if (substr($element->getId(), 0, strlen('default_value')) == 'default_value') {
                $fieldsToRemove[] = $element->getId();
            }
        }
        foreach ($fieldsToRemove as $id) {
            $fieldset->removeField($id);
        }

        $inputTypes = $this->customInputTypeFactory->create()->toOptionArray();

        // remove multiline type when creating new attribute
        if (!$attributeObject->getId()) {
            unset($inputTypes[count($inputTypes) - 1]);
        }

        $frontendInputElem = $form->getElement('frontend_input');
        $frontendInputElem->setValues($inputTypes);
        $frontendInputElem->setLabel(__('Input Type'));

        if (!$attributeObject->getIsUserDefined() && $attributeObject->getFrontendInput() == 'date') {
            $form->getElement('is_required')->setDisabled(true);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _initFormValues()
    {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->getAttributeObject();
        $frontendInput   = $attributeObject->getFrontendInput();

        if (!empty($attributeObject->getData('additional_data'))) {
            $additionalData = DataHelper::jsonDecode($attributeObject->getData('additional_data'));
            if (isset($additionalData[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                $input = $frontendInput . '_' . $additionalData[Swatch::SWATCH_INPUT_TYPE_KEY];
                $attributeObject->setFrontendInput($input);
            }
        }

        parent::_initFormValues();

        // restore frontend input to get default value correctly later
        if (!empty($attributeObject->getData('additional_data'))) {
            $attributeObject->setFrontendInput($frontendInput);
        }

        return $this;
    }

    /**
     * @param string $html
     *
     * @return string
     */
    protected function _afterToHtml($html)
    {
        return $html;
    }
}

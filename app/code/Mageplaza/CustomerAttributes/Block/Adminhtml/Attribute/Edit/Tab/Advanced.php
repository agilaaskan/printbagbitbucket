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
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Customer\Model\Attribute;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\CustomerAttributes\Model\Config\Source\InputValidationFactory;

/**
 * Class Advanced
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab
 */
class Advanced extends Generic
{
    /**
     * @var YesnoFactory
     */
    protected $_yesnoFactory;

    /**
     * @var InputValidationFactory
     */
    protected $inputValidationFactory;

    /**
     * @var PropertyLocker
     */
    protected $propertyLocker;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param YesnoFactory $yesnoFactory
     * @param InputValidationFactory $inputValidationFactory
     * @param PropertyLocker $propertyLocker
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        YesnoFactory $yesnoFactory,
        InputValidationFactory $inputValidationFactory,
        PropertyLocker $propertyLocker,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_yesnoFactory          = $yesnoFactory;
        $this->inputValidationFactory = $inputValidationFactory;
        $this->propertyLocker         = $propertyLocker;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->getAttributeObject();

        /** @var Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('advanced_fieldset', ['legend' => __('Advanced Attribute Properties')]);

        $yesno = $this->_yesnoFactory->create()->toOptionArray();

        $validateClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );
        $fieldset->addField('attribute_code', 'text', [
            'name'  => 'attribute_code',
            'label' => __('Attribute Code'),
            'title' => __('Attribute Code'),
            'note'  => __(
                'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
            ),
            'class' => $validateClass
        ]);

        $fieldset->addField('default_value_text', 'text', [
            'name'  => 'default_value_text',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue()
        ]);

        $fieldset->addField('default_value_textarea', 'textarea', [
            'name'  => 'default_value_textarea',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue()
        ]);

        $fieldset->addField('default_value_textarea_visual', 'textarea', [
            'name'  => 'default_value_textarea_visual',
            'label' => __('Default Value'),
            'title' => __('Default Value'),
            'value' => $attributeObject->getDefaultValue()
        ]);

        $fieldset->addField('max_file_size', 'text', [
            'name'  => 'max_file_size',
            'label' => __('Maximum File Size'),
            'title' => __('Maximum File Size'),
            'class' => 'validate-digits',
            'note'  => __('File size in bytes.')
        ]);
        $fieldset->addField('file_extensions', 'text', [
            'name'  => 'file_extensions',
            'label' => __('Allow File Formats'),
            'title' => __('Allow File Formats'),
            'note'  => __('Separated by comma(,). Example pdf,doc,zip,jpg,png,gif,jpeg')
        ]);

        $fieldset->addField('default_value_date', 'date', [
            'name'        => 'default_value_date',
            'label'       => __('Default Value'),
            'title'       => __('Default Value'),
            'value'       => $attributeObject->getDefaultValue(),
            'date_format' => $this->_localeDate->getDateFormatWithLongYear()
        ]);

        $fieldset->addField('default_value_yesno', 'select', [
            'name'   => 'default_value_yesno',
            'label'  => __('Default Value'),
            'title'  => __('Default Value'),
            'values' => $yesno,
            'value'  => $attributeObject->getDefaultValue()
        ]);

        $fieldset->addField('input_validation', 'select', [
            'name'   => 'input_validation',
            'label'  => __('Input Validation'),
            'title'  => __('Input Validation'),
            'values' => $this->inputValidationFactory->create()->toOptionArray()
        ]);

        $fieldset->addField('is_used_in_grid', 'select', [
            'name'   => 'is_used_in_grid',
            'label'  => __('Add to Customer Grid'),
            'title'  => __('Add to Customer Grid'),
            'values' => $yesno,
            'note'   => __('Column will be added to column options, filter options & search options of Customer Grid')
        ]);

        $fieldset->addField('is_used_in_sales_order_grid', 'select', [
            'name'   => 'is_used_in_sales_order_grid',
            'label'  => __('Add to Sales Order Grid'),
            'title'  => __('Add to Sales Order Grid'),
            'values' => $yesno,
            'note'   => __('Column will be added to column options, filter options & search options of Sales Order Grid')
        ]);

        if ($attributeObject->getId()) {
            $form->getElement('attribute_code')->setDisabled(true);
        }

        $this->setForm($form);
        $this->getPropertyLocker()->lock($form);

        return parent::_prepareForm();
    }

    /**
     * @return $this
     */
    protected function _initFormValues()
    {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->getAttributeObject();

        if (!empty($attributeObject->getValidateRules()['input_validation'])) {
            $attributeObject->setData('input_validation', $attributeObject->getValidateRules()['input_validation']);
        }

        $this->getForm()->addValues($attributeObject->getData());

        return parent::_initFormValues();
    }

    /**
     * @return mixed
     */
    protected function getAttributeObject()
    {
        return $this->_coreRegistry->registry('entity_attribute');
    }

    /**
     * Get property locker
     *
     * @return PropertyLocker
     */
    protected function getPropertyLocker()
    {
        if (null === $this->propertyLocker) {
            $this->propertyLocker = ObjectManager::getInstance()->get(PropertyLocker::class);
        }

        return $this->propertyLocker;
    }
}

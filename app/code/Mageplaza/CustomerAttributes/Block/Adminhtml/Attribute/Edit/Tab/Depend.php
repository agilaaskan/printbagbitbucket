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
use Magento\Customer\Model\Attribute;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\CustomerAttributes\Model\ResourceModel\Attribute\Address\Collection as AddressCollection;
use Mageplaza\CustomerAttributes\Model\ResourceModel\Attribute\Collection;

/**
 * Class Depend
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab
 */
class Depend extends Generic
{
    /**
     * @var PropertyLocker
     */
    protected $propertyLocker;

    /**
     * @var Collection
     */
    protected $attributeCollection;

    /**
     * @var AddressCollection
     */
    protected $addressAttributeCollection;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param PropertyLocker $propertyLocker
     * @param Collection $attributeCollection
     * @param AddressCollection $addressAttributeCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        PropertyLocker $propertyLocker,
        Collection $attributeCollection,
        AddressCollection $addressAttributeCollection,
        array $data = []
    ) {
        $this->propertyLocker             = $propertyLocker;
        $this->attributeCollection        = $attributeCollection;
        $this->addressAttributeCollection = $addressAttributeCollection;

        parent::__construct($context, $registry, $formFactory, $data);
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

        $fieldset = $form->addFieldset('depend_fieldset', ['legend' => __('Depend Fields')]);

        $dependFields = [['value' => '', 'label' => __('None')]];
        $dependValues = [];

        /** @var Attribute $attribute */
        foreach ($this->attributeCollection as $attribute) {
            $excludeDependAttributes = ['group_id', 'website_id'];
            if ($attribute->getId() === $attributeObject->getId() ||
                in_array($attribute->getAttributeCode(), $excludeDependAttributes, true)) {
                continue;
            }

            if (in_array($attribute->getFrontendInput(), ['select', 'select_visual'])) {
                $dependFields[] = [
                    'value' => $attribute->getId(),
                    'label' => $attribute->getDefaultFrontendLabel()
                ];

                foreach ($attribute->getOptions() as $option) {
                    if ($option->getLabel() && $option->getValue()) {
                        $dependValues[] = [
                            'value' => $attribute->getId() . '_' . $option->getValue(),
                            'label' => $option->getLabel()
                        ];
                    }
                }
            }
        }

        $fieldset->addField('field_depend', 'select', [
            'name'   => 'field_depend',
            'label'  => __('Select Field Depend'),
            'title'  => __('Select Field Depend'),
            'values' => $dependFields
        ]);

        $fieldset->addField('value_depend', 'multiselect', [
            'name'   => 'value_depend',
            'label'  => __('Value Depend'),
            'title'  => __('Value Depend'),
            'values' => $dependValues,
        ])->setSize(5);

        if (!$attributeObject->getIsUserDefined() && $attributeObject->getFrontendInput() == 'date') {
            $form->getElement('field_depend')->setDisabled(true);
            $form->getElement('value_depend')->setDisabled(true);
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
        $this->getForm()->addValues($this->getAttributeObject()->getData());

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

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
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\CustomerAttributes\Helper\Data as DataHelper;

/**
 * Class Front
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab
 */
class Front extends Generic
{
    /**
     * @var YesnoFactory
     */
    protected $yesnoFactory;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var PropertyLocker
     */
    protected $propertyLocker;

    /**
     * Send constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param YesnoFactory $yesnoFactory
     * @param DataHelper $dataHelper
     * @param CollectionFactory $groupCollectionFactory
     * @param Store $systemStore
     * @param PropertyLocker $propertyLocker
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        YesnoFactory $yesnoFactory,
        DataHelper $dataHelper,
        CollectionFactory $groupCollectionFactory,
        Store $systemStore,
        PropertyLocker $propertyLocker,
        array $data = []
    ) {
        $this->yesnoFactory           = $yesnoFactory;
        $this->dataHelper             = $dataHelper;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->systemStore            = $systemStore;
        $this->propertyLocker         = $propertyLocker;

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

        $fieldset = $form->addFieldset('front_fieldset', ['legend' => __('Storefront Properties')]);

        $yesno = $this->yesnoFactory->create()->toOptionArray();

        $fieldset->addField('is_visible', 'select', [
            'name'   => 'is_visible',
            'label'  => __('Show on Storefront'),
            'title'  => __('Show on Storefront'),
            'values' => $yesno,
            'value'  => $attributeObject->getIsVisible() ?: 1
        ]);

        $fieldset->addField('customer_can_edit', 'select', [
            'name'   => 'customer_can_edit',
            'label'  => __('Customer can edit'),
            'title'  => __('Customer can edit'),
            'values' => $yesno,
            'value'  => $attributeObject->getData('customer_can_edit') ?: 1
        ]);

        /** @var RendererInterface $rendererBlock */
        $rendererBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
        $fieldset->addField('mp_store_id', 'multiselect', [
            'name'     => 'mp_store_id',
            'label'    => __('Store View'),
            'title'    => __('Store View'),
            'values'   => $this->systemStore->getStoreValuesForForm(false, true),
            'value'    => 0,
            'required' => true
        ])->setRenderer($rendererBlock)->setSize(5);

        $fieldset->addField('mp_customer_group', 'multiselect', [
            'name'     => 'mp_customer_group',
            'label'    => __('Customer Group'),
            'title'    => __('Customer Group'),
            'values'   => $this->groupCollectionFactory->create()->toOptionArray(),
            'value'    => 0,
            'required' => true
        ])->setSize(5);

        $fieldset->addField('used_in_forms', 'multiselect', [
            'name'         => 'used_in_forms',
            'label'        => __('Shown on forms'),
            'title'        => __('Shown on forms'),
            'values'       => $this->dataHelper->getCustomerFormOptions(),
            'value'        => $attributeObject->getUsedInForms(),
            'can_be_empty' => true
        ])->setSize(3);

        $fieldset->addField('sort_order', 'text', [
            'name'  => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'class' => 'validate-digits'
        ]);

        if ($attributeObject->getId()) {
            if ($attributeObject->getIsSystem()) {
                foreach (['sort_order', 'used_in_forms'] as $elementId) {
                    $form->getElement($elementId)->setDisabled(true);
                }
            }

            if (!$attributeObject->getIsUserDefined()) {
                foreach (['sort_order', 'used_in_forms'] as $elementId) {
                    $form->getElement($elementId)->setDisabled(true);
                }

                if ($attributeObject->getFrontendInput() == 'date') {
                    $form->getElement('is_visible')->setDisabled(true);
                }
            }
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

        if (is_null($attributeObject->getData('mp_store_id'))) {
            $attributeObject->setData('mp_store_id', 0);
        }

        if (is_null($attributeObject->getData('mp_customer_group'))) {
            $group = array_keys($this->groupCollectionFactory->create()->toOptionArray());
            $attributeObject->setData('mp_customer_group', implode(',', $group));
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

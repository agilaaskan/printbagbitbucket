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

namespace Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\StateInterface;
use Magento\Framework\Phrase;
use Magento\Swatches\Model\Swatch;
use Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute;
use Mageplaza\CustomerAttributes\Helper\Data;
use Mageplaza\CustomerAttributes\Helper\Data as DataHelper;
use Zend_Serializer_Exception;
use Zend_Validate_Exception;
use Zend_Validate_Regex;

/**
 * Class Save
 * @package Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute
 */
class Save extends Attribute
{
    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     * @throws Zend_Validate_Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            if (!empty($data['serialized_options']) && $this->dataHelper->versionCompare('2.2.9')) {
                $options = [];
                foreach (Data::jsonDecode($data['serialized_options']) as $option) {
                    parse_str($option, $option);
                    $options[] = $option;
                }

                $data = array_merge_recursive($data, ...$options);
            }

            $attributeObject = $this->_initAttribute();

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);

                $data['frontend_input']  = $attributeObject->getFrontendInput();
                $data['is_user_defined'] = $attributeObject->getIsUserDefined();
            } else {
                $attributeCode = $data['attribute_code'] ?:
                    $this->dataHelper->generateCode($this->getRequest()->getParam('frontend_label')[0]);

                $validatorAttrCode = new Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,59}[a-z0-9]$/']);
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );

                    return $this->returnResult('customer/*/', ['_current' => true]);
                }

                $data['attribute_code']     = $attributeCode;
                $data['source_model']       = $this->dataHelper->getSourceModelByInputType($data['frontend_input']);
                $data['backend_model']      = $this->dataHelper->getBackendModelByInputType($data['frontend_input']);
                $data['backend_type']       = $this->dataHelper->getBackendTypeByInputType($data['frontend_input']);
                $data['attribute_set_id']   = $this->_getEntityType()->getDefaultAttributeSetId();
                $data['attribute_group_id'] = $this->attrSetFactory->create()
                    ->getDefaultGroupId($data['attribute_set_id']);
                $data['is_user_defined']    = 1;
                $data['multiline_count']    = 0;
            }

            $data['validate_rules'] = $this->dataHelper->getValidateRules($data, $attributeObject->getValidateRules());

            if ($data['is_user_defined']) {
                if (empty($data['used_in_forms'])) {
                    $data['used_in_forms'] = [];
                } else {
                    $data['used_in_forms'][] = $this->type === 'customer' ? 'adminhtml_' . $this->type : '';
                }
            }

            foreach (['is_filterable_in_grid', 'is_searchable_in_grid'] as $item) {
                $data[$item] = $data['is_used_in_grid'];

                if (!empty($data['is_used_in_sales_order_grid'])) {
                    $data[$item] = $data['is_used_in_sales_order_grid'];
                }
            }

            foreach (['mp_store_id', 'mp_customer_group', 'value_depend'] as $item) {
                if (isset($data[$item])) {
                    $data[$item] = implode(',', $data[$item]);
                }
            }

            $frontendInput = $data['frontend_input'];
            if (!empty($attributeObject->getData('additional_data'))) {
                $additionalData = DataHelper::jsonDecode($attributeObject->getData('additional_data'));
                if (isset($additionalData[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                    $frontendInput .= '_' . $additionalData[Swatch::SWATCH_INPUT_TYPE_KEY];
                }
            }
            if (!empty($data[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                $frontendInput .= '_' . $data[Swatch::SWATCH_INPUT_TYPE_KEY];
            }
            if ($defaultValueField = $this->dataHelper->getDefaultValueByInput($frontendInput)) {
                $defaultValue          = $this->getRequest()->getParam($defaultValueField);
                $data['default_value'] = $defaultValue;
            }

            $attributeObject->addData($data);

            try {
                $attributeObject->save();

                $usedInGridError = $this->handleUsedInGrid($attributeObject);

                $this->_eventManager->dispatch(
                    'mageplaza_' . $this->type . '_attribute_save',
                    ['attribute' => $attributeObject]
                );

                if ($usedInGridError) {
                    $data['is_used_in_grid'] = 0;
                    throw new LocalizedException($usedInGridError);
                }

                $this->messageManager->addSuccessMessage(__('You saved the attribute.'));
                $this->_session->setAttributeData(false);

                if ($this->getRequest()->getParam('back', false)) {
                    return $this->returnResult(
                        'customer/*/edit',
                        ['id' => $attributeObject->getId(), '_current' => true]
                    );
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setAttributeData($data);

                if ($attributeId) {
                    return $this->returnResult('customer/*/edit', ['id' => $attributeId, '_current' => true]);
                }
            }
        }

        return $this->returnResult('customer/*/', []);
    }

    /**
     * @param \Magento\Customer\Model\Attribute $attributeObject
     *
     * @return bool|Phrase
     * @throws Exception
     */
    protected function handleUsedInGrid($attributeObject)
    {
        $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);

        if ($indexer->getState()->getStatus() !== StateInterface::STATUS_INVALID) {
            return false;
        }

        $attributeObject->setData('is_used_in_grid', 0)->save();

        $indexer->reindexAll();

        return __('Customer grid cannot contain more attribute that has one of these types: Text Field, Text Area, Content');
    }
}

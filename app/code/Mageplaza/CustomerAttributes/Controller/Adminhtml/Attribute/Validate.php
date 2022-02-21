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

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\FormData;
use Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute;
use Zend_Validate_Exception;

/**
 * Class Validate
 * @package Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute
 */
class Validate extends Attribute
{
    const DEFAULT_MESSAGE_KEY = 'message';

    /**
     * @return void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function execute()
    {
        $response = new DataObject();
        $response->setError(false);

        $attributeCode = $this->getRequest()->getParam('attribute_code');
        $frontendLabel = $this->getRequest()->getParam('frontend_label');
        $attributeCode = $attributeCode ?: $this->dataHelper->generateCode($frontendLabel[0]);
        $attributeId   = $this->getRequest()->getParam('attribute_id');

        if (!$attributeId) {
            $attributeObject = $this->_initAttribute()->loadByCode($this->_getEntityType()->getId(), $attributeCode);
            if ($attributeObject->getId()) {
                $this->setMessageToResponse($response, [__('An attribute with this code already exists.')]);
            }
        }

        if ($this->getRequest()->getParam('optionvisual')) {
            $options = $this->getRequest()->getParam('optionvisual');
        } elseif (class_exists(FormData::class) && $this->getRequest()->getParam('serialized_options')) {
            // for compatible with multiple Magento version
            $formDataSerializer = $this->_objectManager->create(FormData::class);

            $options = $formDataSerializer->unserialize($this->getRequest()->getParam('serialized_options'));

            if (!empty($options['option'])) {
                $options = $options['option'];
            }
        }

        if (isset($options)) {
            $valueOptions = (isset($options['value']) && is_array($options['value'])) ? $options['value'] : [];
            foreach (array_keys($valueOptions) as $key) {
                if (!empty($options['delete'][$key])) {
                    unset($valueOptions[$key]);
                }
            }
            $this->checkEmptyOption($response, $valueOptions);
            $this->checkUniqueOption($response, $options);
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Set message to response object
     *
     * @param DataObject $response
     * @param string[] $messages
     *
     * @return DataObject
     */
    private function setMessageToResponse($response, $messages)
    {
        $response->setError(true);
        $messageKey = $this->getRequest()->getParam('message_key', static::DEFAULT_MESSAGE_KEY);
        if ($messageKey === static::DEFAULT_MESSAGE_KEY) {
            $messages = reset($messages);
        }

        return $response->setData($messageKey, $messages);
    }

    /**
     * Performs checking the uniqueness of the attribute options.
     *
     * @param DataObject $response
     * @param array|null $options
     *
     * @return $this
     */
    private function checkUniqueOption(DataObject $response, array $options = null)
    {
        if (is_array($options) && !empty($options['value']) && !empty($options['delete'])) {
            $duplicates = $this->isUniqueAdminValues($options['value'], $options['delete']);
            if (!empty($duplicates)) {
                $this->setMessageToResponse(
                    $response,
                    [__('The value of Admin must be unique. (%1)', implode(', ', $duplicates))]
                );
            }
        }

        return $this;
    }

    /**
     * Throws Exception if not unique values into options.
     *
     * @param array $optionsValues
     * @param array $deletedOptions
     *
     * @return array
     */
    private function isUniqueAdminValues(array $optionsValues, array $deletedOptions)
    {
        $adminValues = [];
        foreach ($optionsValues as $optionKey => $values) {
            if (!(isset($deletedOptions[$optionKey]) && $deletedOptions[$optionKey] === '1')) {
                $adminValues[] = reset($values);
            }
        }
        $uniqueValues = array_unique($adminValues);

        return array_diff_assoc($adminValues, $uniqueValues);
    }

    /**
     * Check that admin does not try to create option with empty admin scope option.
     *
     * @param DataObject $response
     * @param array $optionsForCheck
     *
     * @return void
     */
    private function checkEmptyOption(DataObject $response, array $optionsForCheck = null)
    {
        foreach ($optionsForCheck as $optionValues) {
            if (isset($optionValues[0]) && $optionValues[0] === '') {
                $this->setMessageToResponse($response, [__('The value of Admin scope can\'t be empty.')]);
            }
        }
    }
}

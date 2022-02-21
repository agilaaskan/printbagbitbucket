<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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

namespace Mageplaza\CustomerAttributes\Model\Data;

use Magento\Customer\Model\Data\AttributeMetadata as CustomerAttributeMetadata;
use Mageplaza\CustomerAttributes\Api\Data\AttributeMetadataInterface;

/**
 * Class AttributeMetadata
 * @package Mageplaza\CustomerAttributes\Model\Data
 */
class AttributeMetadata extends CustomerAttributeMetadata implements AttributeMetadataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIsUsedInSalesOrderGrid()
    {
        return $this->_get(self::IS_USED_IN_SALES_ORDER_GRID);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsUsedInSalesOrderGrid($value)
    {
        return $this->setData(self::IS_USED_IN_SALES_ORDER_GRID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDepend()
    {
        return $this->_get(self::FIELD_DEPEND);
    }

    /**
     * {@inheritdoc}
     */
    public function setFieldDepend($value)
    {
        return $this->setData(self::FIELD_DEPEND, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueDepend()
    {
        return $this->_get(self::VALUE_DEPEND);
    }

    /**
     * {@inheritdoc}
     */
    public function setValueDepend($value)
    {
        return $this->setData(self::VALUE_DEPEND, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerCanEdit()
    {
        return $this->_get(self::CUSTOMER_CAN_EDIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerCanEdit($value)
    {
        return $this->setData(self::CUSTOMER_CAN_EDIT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMpStoreId()
    {
        return $this->_get(self::MP_STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setMpStoreId($value)
    {
        return $this->setData(self::MP_STORE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMpCustomerGroup()
    {
        return $this->_get(self::MP_CUSTOMER_GROUP);
    }

    /**
     * {@inheritdoc}
     */
    public function setMpCustomerGroup($value)
    {
        return $this->setData(self::MP_CUSTOMER_GROUP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMpCreatedDate()
    {
        return $this->_get(self::MP_CREATED_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMpCreatedDate($value)
    {
        return $this->setData(self::MP_CREATED_DATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalData()
    {
        return $this->_get(self::ADDITIONAL_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalData($value)
    {
        return $this->setData(self::ADDITIONAL_DATA, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxFileSize()
    {
        return $this->_get(self::MAX_FILE_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxFileSize($value)
    {
        return $this->setData(self::MAX_FILE_SIZE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtensions()
    {
        return $this->_get(self::FILE_EXTENSIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setFileExtensions($value)
    {
        return $this->setData(self::FILE_EXTENSIONS, $value);
    }
}

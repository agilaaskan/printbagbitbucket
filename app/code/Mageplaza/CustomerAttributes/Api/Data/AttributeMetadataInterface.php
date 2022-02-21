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

namespace Mageplaza\CustomerAttributes\Api\Data;

/**
 * Interface AttributeMetadataInterface
 * @package Mageplaza\CustomerAttributes\Api\Data
 */
interface AttributeMetadataInterface extends \Magento\Customer\Api\Data\AttributeMetadataInterface
{
    const IS_USED_IN_SALES_ORDER_GRID = 'is_used_in_sales_order_grid';
    const FIELD_DEPEND                = 'field_depend';
    const VALUE_DEPEND                = 'value_depend';
    const CUSTOMER_CAN_EDIT           = 'customer_can_edit';
    const MP_STORE_ID                 = 'mp_store_id';
    const MP_CUSTOMER_GROUP           = 'mp_customer_group';
    const MP_CREATED_DATE             = 'mp_created_date';
    const ADDITIONAL_DATA             = 'additional_data';
    const MAX_FILE_SIZE               = 'max_file_size';
    const FILE_EXTENSIONS             = 'file_extensions';

    /**
     * @return int
     */
    public function getIsUsedInSalesOrderGrid();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsUsedInSalesOrderGrid($value);

    /**
     * @return int
     */
    public function getFieldDepend();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setFieldDepend($value);

    /**
     * @return string
     */
    public function getValueDepend();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValueDepend($value);

    /**
     * @return int
     */
    public function getCustomerCanEdit();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerCanEdit($value);

    /**
     * @return string
     */
    public function getMpStoreId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMpStoreId($value);

    /**
     * @return string
     */
    public function getMpCustomerGroup();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMpCustomerGroup($value);

    /**
     * @return string
     */
    public function getMpCreatedDate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMpCreatedDate($value);

    /**
     * @return string
     */
    public function getAdditionalData();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAdditionalData($value);

    /**
     * @return string
     */
    public function getMaxFileSize();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMaxFileSize($value);

    /**
     * @return string
     */
    public function getFileExtensions();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFileExtensions($value);
}

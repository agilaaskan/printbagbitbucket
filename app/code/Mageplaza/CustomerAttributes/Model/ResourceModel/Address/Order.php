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

namespace Mageplaza\CustomerAttributes\Model\ResourceModel\Address;

use Magento\Customer\Model\Attribute;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Order
 * @package Mageplaza\CustomerAttributes\Model\ResourceModel\Address
 */
class Order extends AbstractAddress
{
    /**
     * Order constructor.
     *
     * @param Context $context
     * @param Attribute $attribute
     * @param string $connectionName
     */
    public function __construct(Context $context, Attribute $attribute, $connectionName = 'sales')
    {
        parent::__construct($context, $attribute, $connectionName);
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_customer_attribute_sales_order_address', 'entity_id');
    }
}

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

/**
 * Class SalesOrderCreate
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml
 */
class SalesOrderCreate extends CustomerEditTab
{
    /**
     * @var array
     */
    protected $entityTypes = ['customer', 'customer_address'];

    /**
     * @var array
     */
    protected $formCodes = ['adminhtml_checkout', 'adminhtml_customer_address'];
}

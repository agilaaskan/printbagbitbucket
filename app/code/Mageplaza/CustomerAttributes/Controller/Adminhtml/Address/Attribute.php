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

namespace Mageplaza\CustomerAttributes\Controller\Adminhtml\Address;

/**
 * Class Attribute
 * @package Mageplaza\CustomerAttributes\Controller\Adminhtml\Address
 */
abstract class Attribute extends \Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageplaza_CustomerAttributes::customer_address_attributes';
    /**
     * entity type
     */
    const ENTITY_TYPE = 'customer_address';
}

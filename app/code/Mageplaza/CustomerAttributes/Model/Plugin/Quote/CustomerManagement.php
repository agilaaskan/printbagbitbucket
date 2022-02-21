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
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerAttributes\Model\Plugin\Quote;

use Magento\Quote\Model\Quote;

/**
 * Class CustomerManagement
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Quote
 */
class CustomerManagement
{
    /**
     * @param \Magento\Quote\Model\CustomerManagement $subject
     * @param Quote $quote
     *
     * @return array
     */
    public function beforeValidateAddresses(
        \Magento\Quote\Model\CustomerManagement $subject,
        Quote $quote
    ) {
        if (!$quote->isVirtual() || $quote->getCustomerIsGuest() || !$quote->getCustomerId()) {
            return [$quote];
        }

        $shipping = $quote->getShippingAddress();

        if ($shipping && $shipping->getCustomerAddressId()) {
            $shipping->setCustomerAddressId(null);
        }

        return [$quote];
    }
}

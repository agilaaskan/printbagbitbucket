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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Customer\Address;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address;
use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class ConvertQuoteAddressToCustomerAddress
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Customer\Address
 */
class ConvertQuoteAddressToCustomerAddress
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param Address $quoteAddress
     * @param AddressInterface $customerAddress
     *
     * @return AddressInterface
     * @throws LocalizedException
     */
    public function afterExportCustomerAddress(
        Address $quoteAddress,
        AddressInterface $customerAddress
    ) {
        $attributes = $this->helperData->getUserDefinedAttributeCodes('customer_address');
        foreach ($attributes as $attribute) {
            $customerAddress->setCustomAttribute($attribute, $quoteAddress->getData($attribute));
        }

        return $customerAddress;
    }
}

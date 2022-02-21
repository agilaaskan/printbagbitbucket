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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Quote\Address;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomAttributeList
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Quote\Address
 */
class CustomAttributeList
{
    /**
     * @var \Mageplaza\CustomerAttributes\Model\Customer\Address\CustomAttributeList
     */
    private $customAttributeList;

    /**
     * CustomAttributeList constructor.
     *
     * @param \Mageplaza\CustomerAttributes\Model\Customer\Address\CustomAttributeList $customAttributeList
     */
    public function __construct(
        \Mageplaza\CustomerAttributes\Model\Customer\Address\CustomAttributeList $customAttributeList
    ) {
        $this->customAttributeList = $customAttributeList;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\CustomAttributeList $subject
     * @param array $result
     *
     * @return array
     * @throws LocalizedException
     */
    public function afterGetAttributes(
        \Magento\Quote\Model\Quote\Address\CustomAttributeList $subject,
        $result
    ) {
        return array_merge($result, $this->customAttributeList->getAttributes());
    }
}

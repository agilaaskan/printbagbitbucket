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

namespace Mageplaza\CustomerAttributes\Model\Customer\Address;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\MetadataObjectInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\CustomAttributeListInterface;

/**
 * Class CustomAttributeList
 * @package Mageplaza\CustomerAttributes\Model\Customer\Address
 */
class CustomAttributeList implements CustomAttributeListInterface
{
    /**
     * @var AddressMetadataInterface
     */
    protected $addressMetadata;

    /**
     * @var MetadataObjectInterface[]
     */
    protected $attributes = null;

    /**
     * @param AddressMetadataInterface $addressMetadata
     */
    public function __construct(AddressMetadataInterface $addressMetadata)
    {
        $this->addressMetadata = $addressMetadata;
    }

    /**
     * Retrieve list of quote address custom attributes
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAttributes()
    {
        if ($this->attributes === null) {
            $this->attributes         = [];
            $customAttributesMetadata = $this->addressMetadata->getCustomAttributesMetadata(AddressInterface::class);
            if (is_array($customAttributesMetadata)) {
                /** @var $attribute MetadataObjectInterface */
                foreach ($customAttributesMetadata as $attribute) {
                    $this->attributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
            $customAttributesMetadata = $this->addressMetadata->getCustomAttributesMetadata(CustomerInterface::class);
            if (is_array($customAttributesMetadata)) {
                /** @var $attribute MetadataObjectInterface */
                foreach ($customAttributesMetadata as $attribute) {
                    $this->attributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
        }

        return $this->attributes;
    }
}

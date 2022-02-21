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
namespace Mageplaza\CustomerAttributes\Model;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\CustomerAttributes\Api\AddressMetadataInterface;
use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class AddressMetadata
 * @package Mageplaza\CustomerAttributes\Model
 */
class AddressMetadata implements AddressMetadataInterface
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * CustomerMetadata constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function getAttribute($attributeCode)
    {
        return $this->helper->getAttributeMetadata($attributeCode, self::ENTITY_TYPE_CUSTOMER);
    }

    /**
     * @inheritdoc
     */
    public function getAllAttributes()
    {
        /** @var AbstractAttribute[] $attribute */
        $attributeCodes = $this->helper->getAttributeCodes(self::ENTITY_TYPE_CUSTOMER, self::ATTRIBUTE_SET_ID_CUSTOMER);

        $attributesMetadata = [];

        foreach ($attributeCodes as $attributeCode) {
            try {
                $attributesMetadata[] = $this->getAttribute($attributeCode);
            } catch (NoSuchEntityException $e) {
                //If no such entity, skip
            }
        }

        return $attributesMetadata;
    }
}

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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Customer\Metadata;

use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;

/**
 * Class Form
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Customer\Metadata
 */
class Form
{
    /**
     * @var Datetime
     */
    protected $datetime;

    /**
     * Form constructor.
     *
     * @param Datetime $datetime
     */
    public function __construct(Datetime $datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @param \Magento\Customer\Model\Metadata\Form $subject
     * @param array $data
     *
     * @return array
     */
    public function beforeValidateData(\Magento\Customer\Model\Metadata\Form $subject, array $data)
    {
        $attributes = $subject->getAllowedAttributes();

        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if ($attribute->isUserDefined() && $attribute->getFrontendInput() == 'date' && isset($data[$attributeCode])) {
                $data[$attributeCode] = $this->datetime->formatDate($data[$attributeCode]);
            }
        }

        return [$data];
    }
}

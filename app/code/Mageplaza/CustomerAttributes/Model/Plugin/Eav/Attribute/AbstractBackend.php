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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Eav\Attribute;

use Magento\Framework\DataObject;

/**
 * Class AbstractBackend
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Eav\Attribute
 */
class AbstractBackend
{
    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend $subject
     * @param DataObject $object
     *
     * @return array|DataObject
     */
    public function beforeValidate(\Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend $subject, $object)
    {
        $attribute = $subject->getAttribute();

        foreach (['customer_account_create', 'customer_account_edit'] as $form) {
            if ($attribute->getIsUserDefined() && $attribute->getUsedInForms() && !in_array(
                $form,
                $attribute->getUsedInForms()
            )) {
                $attribute->setIsRequired(false);
            }
        }

        return [$object];
    }
}

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

namespace Mageplaza\CustomerAttributes\Model\Entity\Attribute\Source;

/**
 * Class Table
 * @package Mageplaza\CustomerAttributes\Model\Entity\Attribute\Source
 */
class Table extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty Add empty option to array
     * @param bool $defaultValues
     *
     * @return array
     */
    public function getAllOptions($withEmpty = false, $defaultValues = false)
    {
        return parent::getAllOptions($withEmpty, $defaultValues);
    }
}

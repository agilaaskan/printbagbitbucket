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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Eav\Model\Attribute\Data;

use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class File
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Eav\Model\Attribute\Data
 */
class File
{
    /**
     * @param \Magento\Eav\Model\Attribute\Data\File $subject
     * @param $value
     *
     * @return array
     */
    public function beforeValidateValue(\Magento\Eav\Model\Attribute\Data\File $subject, $value)
    {
        if (is_string($value) && !empty($value) && is_array(Data::jsonDecode($value))) {
            $value = '';
        }

        return [$value];
    }
}

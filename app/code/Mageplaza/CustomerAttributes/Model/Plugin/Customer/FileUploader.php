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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Customer;

/**
 * Class FileUploader
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Customer
 */
class FileUploader
{
    /**
     * @param \Magento\Customer\Model\FileUploader $subject
     * @param string[] $result
     *
     * @return string[]
     */
    public function afterUpload(\Magento\Customer\Model\FileUploader $subject, $result)
    {
        unset($result['tmp_name']);

        return $result;
    }
}

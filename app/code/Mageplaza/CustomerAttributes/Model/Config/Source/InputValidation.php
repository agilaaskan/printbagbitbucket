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

namespace Mageplaza\CustomerAttributes\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class InputValidation
 * @package Mageplaza\CustomerAttributes\Model\Config\Source
 */
class InputValidation implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('None')],
            ['value' => 'alphanumeric', 'label' => __('Letters (a-z, A-Z) or Numbers (0-9)')],
            ['value' => 'numeric', 'label' => __('Numbers')],
            ['value' => 'alpha', 'label' => __('Letters')],
            ['value' => 'url', 'label' => __('URL')],
            ['value' => 'email', 'label' => __('Email')]
        ];
    }
}

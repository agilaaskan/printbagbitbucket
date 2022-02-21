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

namespace Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Address;

/**
 * Class Edit
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Address
 */
class Edit extends \Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit
{
    /**
     * @var string
     */
    protected $_controller = 'adminhtml_attribute_address';

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getAttribute()->getId()) {
            $label = $this->getAttribute()->getFrontendLabel();
            if (is_array($label)) {
                $label = $label[0];
            }

            return __('Edit Customer Address Attribute "%1"', $label);
        }

        return __('New Customer Address Attribute');
    }
}

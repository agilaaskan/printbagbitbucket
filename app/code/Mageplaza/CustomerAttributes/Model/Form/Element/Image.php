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

namespace Mageplaza\CustomerAttributes\Model\Form\Element;

/**
 * Class Image
 * @package Mageplaza\CustomerAttributes\Model\Form\Element
 */
class Image extends \Magento\Customer\Block\Adminhtml\Form\Element\Image
{
    /**
     * Return Image URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        if (strpos($this->_adminhtmlData->getPageHelpUrl(), 'Magento_Sales/order/address') !== false) {
            $type = 'customer_address';
        } else {
            $type = 'customer';
        }

        return $this->_adminhtmlData->getUrl(
            'customer/viewfile/index',
            ['image' => $this->urlEncoder->encode($this->getValue()), 'type' => $type]
        );
    }
}

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

var config = {
    config: {
        mixins: {
            "Magento_Checkout/js/view/billing-address": {
                "Mageplaza_CustomerAttributes/js/view/billing-address": true
            },
            "Magento_Checkout/js/view/shipping": {
                "Mageplaza_CustomerAttributes/js/view/shipping": true
            },
            "Magento_Checkout/js/view/shipping-address/address-renderer/default": {
                "Mageplaza_CustomerAttributes/js/view/shipping-address/address-renderer/default": true
            },
            "mage/storage": {
                "Mageplaza_CustomerAttributes/js/storage": true
            }
        }
    }
};

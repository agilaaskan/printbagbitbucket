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

define([
    'jquery',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/url'
], function ($, resourceUrlManager, quote, url) {
    'use strict';

    return $.extend(resourceUrlManager, {
        getCAUploadUrl: function (addressType) {
            var params = resourceUrlManager.getCheckoutMethod() === 'guest' ? {cartId: quote.getQuoteId()} : {},
                urls = {
                    'guest': '/guest-carts/:cartId/mp-customer-address-attributes/:addressType/upload',
                    'customer': '/carts/mine/mp-customer-address-attributes/:addressType/upload/'
                };

            params['addressType'] = addressType;

            return url.build(resourceUrlManager.getUrl(urls, params));
        },
    });
});

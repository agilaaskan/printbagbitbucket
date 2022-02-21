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

define([
], function () {
    'use strict';

    return function (Component) {
        return Component.extend({
            defaults: {
                detailsTemplate: 'Mageplaza_CustomerAttributes/billing-address/details'
            },

            getAttrData: function (element) {
                var value = element.value;

                if (typeof value === 'string') {
                    return value;
                }

                if (typeof value === 'object' && !element.attribute_code.includes('-prepared-for-send')) {
                    return value.join();
                }

                return '';
            }
        });
    };
});

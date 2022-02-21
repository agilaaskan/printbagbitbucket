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

define(['jquery', 'mage/url'], function ($, urlBuilder) {
    'use strict';

    function convertArrayAttribute (array) {
        $.each(array, function (index, attribute) {
            var isFile = false;

            if ($.isArray(attribute)) {
                $.each(attribute, function (key, value) {
                    if ($.isPlainObject(value) && value['file']) {
                        isFile       = true;
                        array[index] = JSON.stringify(value);
                    }

                    return false;
                });

                if (!isFile) {
                    array[index] = attribute.join(',');
                }
            } else if (attribute.hasOwnProperty('value') && $.isArray(attribute.value)) {
                $.each(attribute.value, function (key, value) {
                    if ($.isPlainObject(value) && value['file']) {
                        isFile                = true;
                        array[index]['value'] = JSON.stringify(value);
                    }

                    return false;
                });

                if (!isFile) {
                    array[index]['value'] = attribute.value.join(',');
                }
            }
        });

        return array;
    }

    return function (storage) {
        storage.post = function (url, data, global, contentType) {
            if (data) {
                var payload = JSON.parse(data);

                if (payload.hasOwnProperty('addressInformation')) {
                    if (payload.addressInformation.hasOwnProperty('shipping_address') &&
                        payload.addressInformation.shipping_address &&
                        payload.addressInformation.shipping_address.hasOwnProperty('customAttributes')) {
                        payload.addressInformation.shipping_address.customAttributes =
                            convertArrayAttribute(payload.addressInformation.shipping_address.customAttributes);
                    }

                    if (payload.addressInformation.hasOwnProperty('billing_address') &&
                        payload.addressInformation.billing_address &&
                        payload.addressInformation.billing_address.hasOwnProperty('customAttributes')) {
                        payload.addressInformation.billing_address.customAttributes =
                            convertArrayAttribute(payload.addressInformation.billing_address.customAttributes);
                    }
                }

                if (payload.hasOwnProperty('address') && payload.address &&
                    payload.address.hasOwnProperty('custom_attributes')) {
                    payload.address.custom_attributes = convertArrayAttribute(payload.address.custom_attributes);
                }

                if (payload.hasOwnProperty('billingAddress') && payload.billingAddress &&
                    payload.billingAddress.hasOwnProperty('customAttributes')) {
                    payload.billingAddress.customAttributes = convertArrayAttribute(payload.billingAddress.customAttributes);
                }

                if (payload.hasOwnProperty('customerAttributes')) {
                    payload.customerAttributes = convertArrayAttribute(payload.customerAttributes);
                }

                data = JSON.stringify(payload);
            }

            global      = global === undefined ? true : global;
            contentType = contentType || 'application/json';

            return $.ajax({
                url: urlBuilder.build(url),
                type: 'POST',
                data: data,
                global: global,
                contentType: contentType
            });
        };

        return storage;
    };
});

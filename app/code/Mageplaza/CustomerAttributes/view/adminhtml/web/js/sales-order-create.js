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
    'jquery',
    'Mageplaza_CustomerAttributes/js/init-editor-content'
], function ($, initEditorContent) {
    'use strict';

    return function (config) {
        var dependency = config.attributes.dependency,
            formElem   = ['', 'order-billing_address_', 'order-shipping_address_'];

        function handleDependency (elem, prefix) {
            var attrId, dependElem, valueDepend;

            $.each(dependency, function (index, attribute) {
                if (prefix + attribute.attribute_code === elem.attr('id')) {
                    attrId = attribute.attribute_id;
                }
            });

            $.each(dependency, function (index, attribute) {
                if (attribute.field_depend !== attrId || !attribute.value_depend) {
                    return;
                }

                dependElem = $('#' + prefix + attribute.attribute_code);

                if (dependElem.length) {
                    valueDepend = attribute.value_depend.split(',');

                    dependElem.parents('.field').hide();

                    if ($.inArray(attrId + '_' + elem.val(), valueDepend) !== -1) {
                        dependElem.parents('.field').show();
                    }
                }
            });
        }

        function checkDependency (elem, prefix) {
            handleDependency(elem, prefix);

            elem.on('change', function () {
                handleDependency(elem, prefix);
            });
        }

        function processAttributes (form) {
            $.each(form, function (key, value) {
                $.each(dependency, function (index, attribute) {
                    var elem = $('#' + value + attribute.attribute_code);

                    if (elem.length && elem.prop('type') === 'select-one') {
                        checkDependency(elem, value);
                    }
                });

                $.each(config.attributes.contentType, function (index, attribute) {
                    var elem = $('#' + value + attribute.attribute_code);

                    if (elem.length) {
                        initEditorContent(elem, true, false, config.attributes.tinymceConfig);
                    }
                });
            });
        }

        processAttributes(formElem);

        (function (parent) {
            AdminOrder.prototype.loadAreaResponseHandler = function (response) {
                parent.call(this, response);

                if (response.hasOwnProperty('shipping_address')) {
                    processAttributes(['order-shipping_address_']);
                }

                if (response.hasOwnProperty('data')) {
                    processAttributes(formElem);
                }
            };

            AdminOrder.prototype.syncAddressField = function (container, fieldName, fieldValue) {
                var syncName;

                if (this.isBillingField(fieldName)) {
                    syncName = fieldName.replace('billing', 'shipping');
                }

                $(container).select('[name="' + syncName + '"]').each(function (element) {
                    if (~['input', 'textarea', 'select'].indexOf(element.tagName.toLowerCase())) {
                        if (element.type === "checkbox") {
                            element.checked = fieldValue.checked;
                        } else if(element.type !== 'file'){
                            element.value = fieldValue.value;
                        }
                    }
                });
            };
        }(AdminOrder.prototype.loadAreaResponseHandler, AdminOrder.prototype.syncAddressField));
    };
});

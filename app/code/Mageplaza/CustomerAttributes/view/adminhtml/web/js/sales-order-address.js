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
        var dependency    = config.attributes.dependency,
            contentType   = config.attributes.contentType,
            tinymceConfig = config.attributes.tinymceConfig,
            form          = [''];

        processAttributes(form);

        function processAttributes (form) {
            $.each(form, function (key, value) {
                $.each(dependency, function (index, attribute) {
                    var elem = $('#' + value + attribute.attribute_code);

                    if (elem.length && elem.prop('type') === 'select-one') {
                        checkDependency(elem, value);

                        elem.trigger('change');
                    }
                });

                $.each(contentType, function (index, attribute) {
                    var elem = $('#' + value + attribute.attribute_code);

                    if (elem.length) {
                        initEditorContent(elem, false, false, tinymceConfig);
                    }
                })
            });
        }

        function checkDependency (elem, prefix) {
            elem.on('change', function () {
                var self = this,
                    attrId;

                $.each(dependency, function (index, attribute) {
                    if (prefix + attribute.attribute_code === self.id) {
                        attrId = attribute.attribute_id;
                    }
                });

                $.each(dependency, function (index, attribute) {
                    if (attribute.field_depend === attrId && attribute.value_depend) {
                        var dependElem = $('#' + prefix + attribute.attribute_code);

                        if (dependElem.length) {
                            var valueDepend = attribute.value_depend.split(',');

                            if ($.inArray(attrId + '_' + self.value, valueDepend) !== -1) {
                                dependElem.prop('disabled', false);
                                dependElem.parents('.field').show();
                            } else {
                                dependElem.prop('disabled', true);
                                dependElem.parents('.field').hide();
                            }
                        }
                    }
                });
            });
        }
    };
});

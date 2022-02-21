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
    'underscore'
], function ($, _) {
    'use strict';

    return function (config) {
        var configAttrs  = config.attributes,
            disableAttrs = config.attributesDisabled;

        function checkDependency (elem) {
            elem.on('change', function () {
                var el            = this,
                    attrObj       = _.findWhere(configAttrs, {attribute_code: el.name}),
                    attrId        = attrObj['attribute_id'],
                    attrCode, dependConfigs, dependAttrs, dependElem, valueDepend;

                dependConfigs = _.filter(configAttrs, function (configAttr) {
                    return configAttr.value_depend;
                });
                dependAttrs   = _.where(dependConfigs, {field_depend: attrId});

                if (dependAttrs.length) {
                    _.each(dependAttrs, function (dependAttr) {
                        attrCode = dependAttr.attribute_code;
                        if (dependAttr.frontend_input === 'multiselect') {
                            attrCode += '[]';
                        }
                        dependElem = $('[name="' + attrCode + '"]');
                        if (dependElem.length) {
                            valueDepend = dependAttr.value_depend.split(',');
                            if ($.inArray(attrId + '_' + el.value, valueDepend) !== -1) {
                                dependElem.prop('disabled', false);
                                dependElem.parents('.field').show();
                            } else {
                                dependElem.prop('disabled', true);
                                dependElem.parents('.field').hide();
                            }
                        }
                    });
                }
            });
        }

        $.each(configAttrs, function (index, attribute) {
            var elem = $('[name="' + attribute.attribute_code + '"]');

            checkDependency(elem);

            if (elem.length > 1) {
                elem.each(function (elemIndex, elemChild) {
                    if (elemChild.type === 'radio'  && elemChild.checked) {
                        $(elemChild).trigger('change');
                    }
                });
            } else {
                elem.trigger('change');
            }

            if (Number (attribute.field_depend) && _.findIndex(configAttrs, {attribute_id : attribute.field_depend}) === -1) {
                elem.prop('disabled', true);
                elem.parents('.field').hide();
            }
        });

        $.each(disableAttrs, function (index, attribute) {
            var attributeCode = attribute.attribute_code,
                elem          = $('[id="' + attributeCode + '"]'),
                children      = $('[for="' + attributeCode + '"]').siblings('.control').children();

            if (attribute.customer_can_edit === '0') {
                elem.prop('readonly', true);
                elem.css({"opacity": "0.5", "pointer-events": "none"});

                if (attribute.frontend_input === 'multiselect' || attribute.frontend_input === 'select') {

                    $.each(children, function () {
                        $(this).find('input').prop('readonly', true);
                        $(this).find('input').css({"opacity": "0.5", "pointer-events": "none"});
                    });
                }

                if (attribute.frontend_input === 'textarea' && attribute.additional_data && tinymce) {
                    tinymce.get(attributeCode).setMode('readonly');
                }
            }
        });
    };
});

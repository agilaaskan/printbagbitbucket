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
    'uiRegistry',
    'Mageplaza_CustomerAttributes/js/init-editor-content'
], function ($, registry, initEditorContent) {
    'use strict';

    return function (Component) {
        var popupEditor = false;

        return Component.extend({
            attributes: window.checkoutConfig.mpCaConfig.customerAttributes.contentType,
            tinymceConfig: window.checkoutConfig.mpCaConfig.customerAttributes.tinymceConfig,

            showFormPopUp: function () {
                this._super();

                if (!popupEditor) {
                    var fieldset = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset');

                    $.each(this.attributes, function (index, attribute) {
                        $.each(fieldset.elems(), function (key, field) {
                            if (attribute.attribute_code === field.index && attribute.additional_data) {
                                initEditorContent($('#' + field.uid), true, false, this.tinymceConfig);
                            }
                        });
                    });

                    popupEditor = true;
                }
            }
        });
    }
});

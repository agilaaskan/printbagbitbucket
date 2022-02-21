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
    'underscore',
    'jquery',
    'mage/adminhtml/wysiwyg/tiny_mce/setup'
], function (_, $) {
    'use strict';

    return function (elem, repeat, breakdown, tinymceConfig) {
        require([
            'tinymce' + ((typeof tinymceConfig !== 'boolean') ? '4' : '')
        ], function (tinyMCE) {
        var config = {
            width: '100%',
            settings: {
                theme_advanced_buttons1: 'bold,italic,|,justifyleft,justifycenter,justifyright,|,fontsizeselect'
                    + ',|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code',
                theme_advanced_buttons2: null,
                theme_advanced_buttons3: null,
                theme_advanced_buttons4: null
            }
        };

        if (breakdown){
            $.extend(config.settings, {
                theme_advanced_buttons1: 'bold,italic,|,justifyleft,justifycenter,justifyright,|,fontsizeselect',
                theme_advanced_buttons2: 'forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code'
            });
        }

        if (typeof tinymceConfig !== 'boolean') {
            if (typeof tinymceConfig == 'string') {
                tinymceConfig = JSON.parse(tinymceConfig);
            }
            var editor = new wysiwygSetup(elem.attr('id'), _.extend(config, tinymceConfig));
            if ($.isReady) {
                tinyMCE.dom.Event.domLoaded = true;
            }
            if (repeat) {
                editor.wysiwygInstance.turnOff();
            }
            editor.setup('exact');
        } else {
            var editor = new tinyMceWysiwygSetup(elem.attr('id'), config);
            if ($.isReady) {
                tinyMCE.dom.Event.domLoaded = true;
            }
            if (repeat) {
                editor.turnOff();
            }
            editor.turnOn();
        }

        elem.addClass('wysiwyg-editor').data('wysiwygEditor', editor);

        return elem;
        });
    };
});

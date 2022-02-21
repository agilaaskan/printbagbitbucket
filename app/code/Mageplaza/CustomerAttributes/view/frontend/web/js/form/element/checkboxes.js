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
    'ko',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/form/element/abstract'
], function(ko, _, utils, Abstract) {
    'use strict';

    return Abstract.extend({
        initObservable: function() {
            var defaultValue = this.value;
            this._super();
            var value  = this.value;
            this.value = ko.observableArray([]).extend(value);
            this.value(this.normalizeData(defaultValue));
            return this;
        },

        normalizeData: function(value) {
            if (utils.isEmpty(value)){
                value = [];
            }

            return _.isString(value) ? value.split(',') : value;
        },

        hasChanged: function() {
            var value   = this.value(),
                initial = this.initialValue;

            return !utils.equalArrays(value, initial);
        }
    });
});

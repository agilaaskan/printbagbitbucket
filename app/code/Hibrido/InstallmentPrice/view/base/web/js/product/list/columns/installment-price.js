/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Ui/js/grid/columns/column',
    'Magento_Catalog/js/product/list/column-status-validator',
    'mage/translate',
    'Hibrido_InstallmentPrice/js/installment-price-box'
], function ($, Column, validator) {
    'use strict';
    
    return Column.extend({
        initialize: function () {
            this._super();
        },

        isAllowed: function() {
            return validator.isValid(this.source(), 'installment_price', 'show_attributes');
        },

        getConfigInterestInitialPercentage: function (row) {
            return row.extension_attributes.installment_price.config_interest_initial_percentage;
        },

        getConfigInterestIncrementalPercentage: function (row) {
            return row.extension_attributes.installment_price.config_interest_incremental_percentage;
        },

        getConfigInterestPriceQuote: function (row) {
            return row.extension_attributes.installment_price.config_interest_price_quote;
        },

        getConfigInstallmentNumber: function (row) {
            return row.extension_attributes.installment_price.config_installment_number;
        },

        getConfigInstallmentNumberWithoutInterest: function (row) {
            return row.extension_attributes.installment_price.config_installment_number_without_interest;
        },

        getConfigInstallmentShowOnlyLastOne: function (row) {
            return row.extension_attributes.installment_price.config_installment_show_only_last_one;
        },

        getConfigPriceFormat: function (row) {
            return row.extension_attributes.installment_price.config_price_format;
        },

        translateOneInstallment: function () {
            $.mage.__('%qty time of %total')
                .replace('%qty', '<%- installment.qty %>')
                .replace('%total', '<%- installment.formatted.total %>');
        },

        translateInstallmentsWithInterest: function () {
            $.mage.__('%qty times of %price with an interest rate of %interest_rate% per month (%interest_amount of interest) totalizing %total')
                .replace('%qty', '<%- installment.qty %>')
                .replace('%price', '<%- installment.formatted.price %>')
                .replace('%interest_rate', '<%- installment.interestRate %>')
                .replace('%interest_amount', '<%- installment.formatted.interestAmount %>')
                .replace('%total', '<%- installment.formatted.total %>');

        },

        translateInstallmentsWithoutInterest: function () {
            $.mage.__('%qty times of %price without interest totalizing %total')
                .replace('%qty', '<%- installment.qty %>')
                .replace('%price', '<%- installment.formatted.price %>')
                .replace('%total', '<%- installment.formatted.total %>');

        }
    });
});

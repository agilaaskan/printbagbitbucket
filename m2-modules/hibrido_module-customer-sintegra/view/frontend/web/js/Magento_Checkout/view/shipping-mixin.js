/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

define(function (customerData) {
    'use strict';

    let mixin = {

        defaults: {
            template: 'Hibrido_CustomerSintegra/Magento_Checkout/shipping',
        },

        /**
         * @return object|void
         * @private
         */
        _getCustomerDataFromCheckoutConfig: function () {
            if (typeof window.checkoutConfig === 'undefined' || window.checkoutConfig.customerData === 'undefined') {
                return;
            }

            return window.checkoutConfig.customerData;
        },

        /**
         * @return boolean
         */
        isCustomerLegalPerson: function () {
            if (!(customerData = this._getCustomerDataFromCheckoutConfig())) {
                return false;
            }

            if (typeof customerData.custom_attributes === 'undefined' ||
                typeof customerData.custom_attributes.hb_person_type === 'undefined' ||
                typeof customerData.custom_attributes.hb_person_type.value === 'undefined'
            ) {
                return false;
            }

            return (customerData.custom_attributes.hb_person_type.value === 'legal');
        }

    };


    return function (target) {
        return target.extend(mixin);
    };
});
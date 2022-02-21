/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    'use strict';

    return function () {

        $.validator.addMethod(
            'validate-cnpj',
            function (value) {
                value = value.replace(/[^\d]+/g, '');

                if (value === '') {
                    return false;
                }

                if (value.length < 14) {
                    return false;
                }

                if (value === "00000000000000" ||
                    value === "000000000000000" ||
                    value === "11111111111111" ||
                    value === "111111111111111" ||
                    value === "22222222222222" ||
                    value === "222222222222222" ||
                    value === "33333333333333" ||
                    value === "333333333333333" ||
                    value === "44444444444444" ||
                    value === "444444444444444" ||
                    value === "55555555555555" ||
                    value === "555555555555555" ||
                    value === "66666666666666" ||
                    value === "666666666666666" ||
                    value === "77777777777777" ||
                    value === "777777777777777" ||
                    value === "88888888888888" ||
                    value === "888888888888888" ||
                    value === "99999999999999" ||
                    value === "999999999999999"
                ) {
                    return false;
                }

                let size = value.length - 2,
                    numbers = value.substring(0, size),
                    digits = value.substring(size),
                    sum = 0,
                    pos = size - 7;

                for (let i = size; i >= 1; i--) {
                    sum += numbers.charAt(size - i) * pos--;
                    if (pos < 2) {
                        pos = 9;
                    }
                }

                let result = sum % 11 < 2 ? 0 : 11 - sum % 11;

                // noinspection EqualityComparisonWithCoercionJS
                if (result != digits.charAt(0)) {
                    return false;
                }

                size = size + 1;
                numbers = value.substring(0, size);
                sum = 0;
                pos = size - 7;

                for (let i = size; i >= 1; i--) {
                    sum += numbers.charAt(size - i) * pos--;
                    if (pos < 2) {
                        pos = 9;
                    }
                }

                result = sum % 11 < 2 ? 0 : 11 - sum % 11;

                // noinspection EqualityComparisonWithCoercionJS
                if (result != digits.charAt(1)) {
                    return false;
                }

                return true;
            },
            $.mage.__('Invalid CNPJ.')
        );
    }
});

/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery',
    'mage/translate',
    'domReady!'
], function ($) {
    'use strict';

    let streetLabel = $.mage.__('Street'),
        numberLabel = $.mage.__('Number'),
        complementLabel = $.mage.__('Complement'),
        districtLabel = $.mage.__('District');

    let streetLabelElementSelector = [
        'div[name="shippingAddress.street.0"] > label',
        'div[name="billingAddresscheckmo.street.0"] > label',
        '.field.primary > label[for="street_1"]'
    ].join(',');

    let numberLabelElementSelector = [
        'div[name="shippingAddress.street.1"] > label',
        'div[name="billingAddresscheckmo.street.1"] > label',
        '.field.additional > label[for="street_2"]'
    ].join(',');

    let complementLabelElementSelector = [
        'div[name="shippingAddress.street.2"] > label',
        'div[name="billingAddresscheckmo.street.2"] > label',
        '.field.additional > label[for="street_3"]'
    ].join(',');

    let districtLabelElementSelector = [
        'div[name="shippingAddress.street.3"] > label',
        'div[name="billingAddresscheckmo.street.3"] > label',
        '.field.additional > label[for="street_4"]'
    ].join(',');

    //Create a change label reusable function.
    let changeStreetLabels = function () {
        let $streetLabelElement = $(streetLabelElementSelector),
            $numberLabelElement = $(numberLabelElementSelector),
            $complementLabelElement = $(complementLabelElementSelector),
            $districtLabelElement = $(districtLabelElementSelector);

        $streetLabelElement.find('span').text(streetLabel);
        $numberLabelElement.find('span').text(numberLabel);
        $complementLabelElement.find('span').text(complementLabel);
        $districtLabelElement.find('span').text(districtLabel);

        $streetLabelElement.addClass('show');
        $numberLabelElement.addClass('show');
        $complementLabelElement.addClass('show');
        $districtLabelElement.addClass('show');
    };

    //We try to change some rendered element on startup.
    let intervalTimes = 0, intervalLimit = 20;
    let intervalHandle = setInterval(function () {
        intervalTimes++;
        let $streetLabelElement = $(streetLabelElementSelector);
        if ($streetLabelElement.length) {
            changeStreetLabels();
            clearInterval(intervalHandle);
        } else {
            if (intervalTimes > intervalLimit) {
                clearInterval(intervalHandle);
            }
        }
    }, 1000);

    //We also bind this label on change event (like billing checkbox clicked).
    $('body').on('change', changeStreetLabels);
});

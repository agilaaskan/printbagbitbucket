/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    let streetElementSelector = 'input[name="street[]"]',
        $streetElement = $(streetElementSelector);

    $streetElement.each((index, element) => {
        $(element).attr('name', `street[${index}]`).addClass('required-entry');
    });
});

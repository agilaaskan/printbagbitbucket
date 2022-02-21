/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    let postcodeElementSelector = '.field.zip',
        streetElementSelector = '.field.street',
        $postcodeElement = $(postcodeElementSelector),
        $streetElement = $(streetElementSelector);

    if ($postcodeElement.length && $streetElement.length) {
        $postcodeElement.insertBefore($streetElement);
    }
});

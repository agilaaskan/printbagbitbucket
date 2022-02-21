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
    'Mageplaza_CustomerAttributes/js/form/element/file-uploader',
    'Mageplaza_CustomerAttributes/js/model/resource-url-manager'
], function(Component, resourceUrl) {
    'use strict';

    return Component.extend({
        defaults: {
            uploaderConfig: {
                url: resourceUrl.getCAUploadUrl('shipping')
            }
        },
    });
});

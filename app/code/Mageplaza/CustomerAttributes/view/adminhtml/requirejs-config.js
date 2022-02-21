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

var config = {
    config: {
        mixins: {
            "Magento_Ui/js/form/element/select": {
                "Mageplaza_CustomerAttributes/js/form/element/select": true
            },
            "Magento_Ui/js/form/element/textarea": {
                "Mageplaza_CustomerAttributes/js/form/element/textarea": true
            },
            "Magento_Ui/js/form/element/file-uploader": {
                "Mageplaza_CustomerAttributes/js/form/element/file-uploader": true
            }
        }
    }
};

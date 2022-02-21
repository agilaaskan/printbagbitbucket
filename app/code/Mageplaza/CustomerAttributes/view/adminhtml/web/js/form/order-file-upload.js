/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
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
    'jquery',
    'Magento_Ui/js/modal/alert',
    'Magento_Sales/order/create/form',
    'jquery/file-uploader'
], function ($, uiAlert) {
    'use strict';

    return function (config) {
        /** ajax upload file function */
        function uploadProcessor (formdata, file) {
            formdata.append(config.fileAttribute, file);
            formdata.append('param_name', config.fileAttribute);
            formdata.append('form_key', window.FORM_KEY);
            $.ajax({
                type: "POST",
                url: config.ajaxUrl,
                data: formdata,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.error) {
                        $('#' + config.htmlId).val("");
                        uiAlert({content: response.error});
                    } else {
                        $('#' + config.htmlId + '_value').val(response.file);
                    }
                }
            });
        }

        function ajaxFileUpload (uploadSelector) {
            // /** file upload function */
            var formdata = new FormData();

            uploadSelector.change(function () {
                if (this.files.length > 0) {
                    uploadProcessor(formdata, this.files[0]);
                }
            });
        }

        ajaxFileUpload($('#' + config.htmlId + ''));
    };
});

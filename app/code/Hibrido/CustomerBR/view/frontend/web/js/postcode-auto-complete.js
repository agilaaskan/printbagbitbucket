/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    let postcodeElementSelector = '[name="postcode"]',
        streetElementSelector = ['[name="street[0]"]', '#street_1'].join(','),
        complementElementSelector = ['[name="street[2]"]', '#street_3'].join(','),
        districtElementSelector = ['[name="street[3]"]', '#street_4'].join(','),
        cityElementSelector = '[name="city"]',
        regionElementSelector = '[name="region_id"]',
        ufs = {
        'AC': '485',
        'AL': '486',
        'AP': '487',
        'AM': '488',
        'BA': '489',
        'CE': '490',
        'ES': '491',
        'GO': '492',
        'MA': '493',
        'MT': '494',
        'MS': '495',
        'MG': '496',
        'PA': '497',
        'PB': '498',
        'PR': '499',
        'PE': '500',
        'PI': '501',
        'RJ': '502',
        'RN': '503',
        'RS': '504',
        'RO': '505',
        'RR': '506',
        'SC': '507',
        'SP': '508',
        'SE': '509',
        'TO': '510',
        'DF': '511',
    };

    let formClear = function () {
        let fields = streetElementSelector + ',' + districtElementSelector + ',' + cityElementSelector + ',' + regionElementSelector;
        $(fields).val('');
        $('.modal-content').find(fields).val('');
    };

    let formErrorMessage = function ($element) {
        let $errorMessageElement = $('<span>CEP não encontrado.</span>').css('color', 'red').insertAfter($element);
        setTimeout(function () {
            $errorMessageElement.remove();
        }, 2000);
    };

    let formCheckComplement = function ($complementElement) {
        $complementElement = $complementElement || $(complementElementSelector);

        if ($complementElement.val() === '') {
            $complementElement.val('N/A');
            $complementElement.trigger('change');
        }
    };

    let formBeforeSend = function ($formElement) {
        let fieldsSelectors = [streetElementSelector, districtElementSelector, districtElementSelector].join(','),
            $fieldsElements = typeof $formElement !== 'undefined' ? $(fieldsSelectors) : $formElement.find(fieldsSelectors);

        $fieldsElements.val('...');
        $('.modal-content').find(fieldsSelectors).val('...');
        formCheckComplement($formElement.find(complementElementSelector));
    };

    // noinspection JSJQueryEfficiency
    $('body').on('blur', complementElementSelector, function (event) {
        formCheckComplement($(event.currentTarget));
    });

    // noinspection JSJQueryEfficiency
    $('body').on('change', postcodeElementSelector, function (event) {
        let $postcodeElement = $(event.currentTarget),
            postcodeValue = $postcodeElement.val().replace(/\D/g, ''),
            $formElement = $postcodeElement.parents('form');

        if (!/^[0-9]{8}$/.test(postcodeValue)) {
            formErrorMessage($postcodeElement);
            formClear();
            return;
        }

        formBeforeSend($formElement);

        $.getJSON('https://viacep.com.br/ws/'+ postcodeValue +'/json', function ({ uf, bairro, localidade, logradouro }) {
            if (typeof uf === 'undefined' || typeof bairro === 'undefined' || typeof localidade === 'undefined' || typeof logradouro === 'undefined') {
                formErrorMessage($postcodeElement);
                formClear();
                return;
            }

            $(cityElementSelector).val(localidade).trigger('change');
            $(districtElementSelector).val(bairro).trigger('change');
            $(streetElementSelector).val(logradouro).trigger('change');

            if (typeof ufs[uf] !== 'undefined' && $(regionElementSelector + ' > [value="'+ ufs[uf] +'"]').length > 0) {
                $(regionElementSelector).val(ufs[uf]).trigger('change');
            }
        });
    });

    // Se o CEP já foi preenchido alguma vez na quote (normalmente
    // no sumário do carrinho), o auto complete do CEP não irá funcionar,
    // dessa forma, precisamos tentar dar o trigger no change quando a página
    // for carregada

    let intervalTimes = 0, intervalLimit = 20;
    let intervalHandle = setInterval(function () {
        intervalTimes++;
        let $postcodeElement = $(postcodeElementSelector);
        if ($postcodeElement.length) {
            $postcodeElement.trigger('change');
            clearInterval(intervalHandle);
        } else {
            if (intervalTimes > intervalLimit) {
                clearInterval(intervalHandle);
            }
        }
    }, 1000);

});

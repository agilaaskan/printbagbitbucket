/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery',
    'jqueryMask',
    'mage/translate',
    'domReady!'
], function ($) {
    'use strict';

    //Types constants.
    const PERSON_TYPE_LEGAL = 'legal';
    const STATE_REGISTRATION_EXEMPT = 'exempt';
    const STATE_REGISTRATION_ACQUIT = 'acquit';

    //Selectors.
    let $elementFirstname = $('#firstname');
    let $elementLastname = $('#lastname');
    let $elementTaxvat = $('#taxvat');
    let $elementPersonType = $('#hb_person_type');
    let $elementStateRegistration = $('#hb_state_registration');

    // Classes and Masks.
    let classCpf = 'validate-cpf';
    let classCnpj = 'validate-cnpj';
    let maskCpf = '000.000.000-00';
    let maskCnpj = '00.000.000/0000-00';

    //Default labels.
    let labelPersonTypeNaturalFirstname = $.mage.__('First Name');
    let labelPersonTypeNaturalLastname = $.mage.__('Last Name');
    let labelPersonTypeLegalFirstname = $.mage.__('Company Name');
    let labelPersonTypeLegalLastname = $.mage.__('Fantasy Name');
    let labelStateRegistrationAcquit = $.mage.__('Acquit');
    let labelStateRegistrationExempt = $.mage.__('Exempt');

    //Execute what we need.
    showAcquitFieldHtml();
    applyPersonTypeChanges();

    $('form').on('change', $elementTaxvat, function () {
        applyPersonTypeChanges();
    });

    /**
     * @return void
     */
    function applyPersonTypeChanges()
    {
        $elementTaxvat.removeClass(classCpf);
        $elementTaxvat.removeClass(classCnpj);

        if ($elementPersonType.val() === PERSON_TYPE_LEGAL) {
            $elementTaxvat.addClass(classCnpj);
            $elementTaxvat.mask(maskCnpj, {reverse: true});
            toggleStateRegistrationField(true);
            changeLabels(labelPersonTypeLegalFirstname, labelPersonTypeLegalLastname);
        } else {
            $elementTaxvat.addClass(classCpf);
            $elementTaxvat.mask(maskCpf);
            toggleStateRegistrationField(false);
            changeLabels(labelPersonTypeNaturalFirstname, labelPersonTypeNaturalLastname);
        }
    }

    /**
     * @param labelFirstname string
     * @param labelLastname string
     * @return void
     */
    function changeLabels(labelFirstname, labelLastname)
    {
        $elementFirstname.closest('.field').find('span').text(labelFirstname);
        $elementLastname.closest('.field').find('span').text(labelLastname);
    }

    /**
     * @param show boolean
     * @return void
     */
    function toggleStateRegistrationField(show)
    {
        if (show) {
            $elementStateRegistration.addClass('required-entry');
            $elementStateRegistration.closest('.field').addClass('required');
            $elementStateRegistration.closest('.field-hb_state_registration').show();
            $('.acquit-field').show();
        } else {
            $elementStateRegistration.removeClass('required-entry');
            $elementStateRegistration.closest('.field').removeClass('required');
            $elementStateRegistration.closest('.field-hb_state_registration').hide();
            $('.acquit-field').hide();
        }
    }

    /**
     * @return void
     */
    function showAcquitFieldHtml()
    {
        if (!$elementStateRegistration.length) {
            return;
        }

        let html = `<div class="field choice acquit-field">
            <input type="checkbox" id="acquit" class="checkbox">
            <label for="acquit" class="label">
                <span>${labelStateRegistrationAcquit}</span>
            </label>
        </div>`;

        $(html).insertAfter($elementStateRegistration.closest('.field'));
        checkAcquitStatus();
    }

    /**
     * @return void
     */
    function checkAcquitStatus()
    {
        let stateRegistrationValue = $elementStateRegistration.val();
        let $elementStateRegistrationCheckbox = $('#acquit');

        if (stateRegistrationValue.toLowerCase() === STATE_REGISTRATION_EXEMPT || stateRegistrationValue.toLowerCase() === STATE_REGISTRATION_ACQUIT) {
            $elementStateRegistrationCheckbox.prop('checked', true);
            $elementStateRegistration.prop('readonly', true);
        }

        $elementStateRegistrationCheckbox.change(function () {
            if ($(this).prop('checked')) {
                $elementStateRegistration.val(labelStateRegistrationExempt);
                $elementStateRegistration.prop('readonly', true);
                return;
            }

            $elementStateRegistration.val('');
            $elementStateRegistration.removeProp('readonly');
        })
    }
});

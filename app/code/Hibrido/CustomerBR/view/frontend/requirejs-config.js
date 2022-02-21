/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            jqueryMask: 'Hibrido_CustomerBR/js/jquery.mask.min'
        }
    },
    config: {
        mixins: {
            'mage/validation': {
                'Hibrido_CustomerBR/js/validator-cpf': true,
                'Hibrido_CustomerBR/js/validator-cnpj': true
            }
        }
    }
};

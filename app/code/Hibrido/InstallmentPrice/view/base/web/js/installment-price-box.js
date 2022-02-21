/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/template',
    'underscore',
    'Magento_Catalog/js/price-utils',
    'jquery/ui'
], function ($, mageTemplate, _, priceUtils) {
    'use strict';

    $.widget('mage.installmentPriceBox', {
        options: {
            interest: {
                on: false,
                initialPercentage: 0,
                incrementalPercentage: 0
            },
            installment: {
                number: 0,
                numberWithoutInterest: 0,
                priceTemplate: '#installment-price-template',
                minQuota: 0,
                show: {
                    onlyLastOne: false
                }
            },
            finalPriceSelector: '[data-role=priceBox].price-final_price',
            finalPrice: 0,
            installments: [],
            priceFormat: {}
        },

        _init: function () {
            if (!this.options.installment.number) {
                return;
            }

            this.options.finalPrice = $(this.options.finalPriceSelector).data('magePriceBox')
                ? $(this.options.finalPriceSelector).data('magePriceBox').options.prices.finalPrice.amount
                : $(this.options.finalPriceSelector).find('.price-final_price .price-wrapper').data('priceAmount');

            if (this.options.finalPrice) {
                this._reloadInstallments();
                this._redrawInstallments();

                $(this.options.finalPriceSelector).on('updatePrice', this._updatePrice.bind(this));
            }
        },

        _updatePrice: function (event) {
            this.options.finalPrice = $(event.target).data('magePriceBox').cache.displayPrices.finalPrice.final;
            this._reloadInstallments();
            this._redrawInstallments();
        },

        _calcInterestRate: function (qty) {
            var interestRate = 0,
                diff = qty - this.options.installment.numberWithoutInterest;
            if (diff >= 1) {
                interestRate = ((diff - 1) * this.options.interest.incrementalPercentage) + this.options.interest.initialPercentage;
            }
            return interestRate;
        },

        _calcInstallmentAmount: function (qty, interestRate) {
            return (this.options.finalPrice * (1 + (interestRate / 100))) / qty;
        },

        _reloadInstallments: function () {
            this.options.installments = [];

            for (let i = 1; i <= this.options.installment.number; i++) {
                var total = this.options.finalPrice,
                    oldTotal = total,
                    price = parseFloat(total / i),
                    interestAmount = 0,
                    interestRate = 0,
                    hasInterest = false;

                if (this.options.interest.on && (i > this.options.installment.numberWithoutInterest)) {
                    hasInterest = true;
                    interestRate = this._calcInterestRate(i);
                    price = this._calcInstallmentAmount(i, interestRate);
                    total = price * i;
                    interestAmount = total - oldTotal;
                }

                this.options.installments.push({
                    qty: i,
                    price: price,
                    total: total,
                    hasInterest: hasInterest,
                    interestAmount: interestAmount,
                    interestRate: interestRate,
                    formatted: {
                        price: priceUtils.formatPrice(price, this.options.priceFormat),
                        total: priceUtils.formatPrice(total, this.options.priceFormat),
                        interestAmount: priceUtils.formatPrice(interestAmount, this.options.priceFormat)
                    }
                });

                if (this.options.installment.minQuota > price) {
                    break;
                }
            }

            this.options.installment.number = this.options.installments.length;

            if (this.options.installment.show.onlyLastOne) {
                this.options.installments = this.options.installments.slice(-1);
            }
        },

        _redrawInstallments: function () {
            var html = '',
                installmentPriceTemplate = mageTemplate(this.options.installment.priceTemplate);

            _.each(this.options.installments, function (installment) {
                html += installmentPriceTemplate({ installment: installment });
            });

            this.element.html(html);
        }

    });

    return $.mage.installmentPriceBox;
});

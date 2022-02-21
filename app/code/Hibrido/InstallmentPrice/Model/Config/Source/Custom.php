<?php

namespace Hibrido\InstallmentPrice\Model\Config\Source;

class Custom implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '0' => 'all installments',
            '1' => 'only last installment',
            '2' => 'both modes',

        ];
    }
}
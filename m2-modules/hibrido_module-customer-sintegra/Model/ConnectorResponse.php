<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;

/**
 * @method string getUf()
 * @method string getCep()
 * @method string getBairo()
 * @method string getNumero()
 * @method string getTelefone()
 * @method string getMunicipio()
 * @method string getLogradouro()
 * @method string getComplemento()
 */
class ConnectorResponse extends DataObject
{
    const ALLOWED_FIELDS = [
        'uf',
        'cep',
        'bairro',
        'numero',
        'telefone',
        'municipio',
        'logradouro',
        'complemento',
    ];

    const REQUIRED_FIELDS = [
        'uf',
        'cep',
        'bairro',
        'numero',
        'telefone',
        'municipio',
        'logradouro',
        'complemento',
    ];

    /**
     * @inheritDoc
     * @throws InputException
     */
    public function __construct(array $data = [])
    {
        //Get only allowed data before setting it into object.
        $data = $this->getOnlyAllowedFields($data);

        //Call parent as usual.
        parent::__construct($data);

        //Validate fields.
        //Explicitly let the InputException bubble up.
        $this->validateFields();
    }

    /**
     * @return string
     */
    public function getPais()
    {
        return 'BR';
    }

    /**
     * @param array $data
     * @return array
     */
    private function getOnlyAllowedFields(array $data): array
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, static::ALLOWED_FIELDS)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * @return void
     * @throws InputException
     */
    private function validateFields(): void
    {
        //Test for required fields.
        foreach (static::REQUIRED_FIELDS as $field) {
            if (!$this->hasData($field)) {
                throw new InputException(__('Required field "%1" not present.', $field));
            }
        }
    }
}
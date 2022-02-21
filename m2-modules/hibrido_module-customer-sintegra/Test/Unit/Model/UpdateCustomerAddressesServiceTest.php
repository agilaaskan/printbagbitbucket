<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hibrido\CustomerSintegra\Test\Unit\Model;

use Hibrido\CustomerSintegra\Model\Connector;
use Hibrido\CustomerSintegra\Model\ConnectorResponse;
use Hibrido\CustomerSintegra\Model\UpdateCustomerAddressesService;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Directory\Model\Region;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpdateCustomerAddressesServiceTest extends TestCase
{
    /** @var UpdateCustomerAddressesService */
    protected $subject;

    /** @var MockObject */
    private $connectorMock;

    /** @var MockObject */
    private $regionFactoryMock;

    /** @var MockObject */
    private $addressFactoryMock;

    /** @var MockObject */
    private $addressRepositoryMock;

    /** @var MockObject */
    protected $customerRepositoryMock;

    /** @var MockObject */
    private $region;

    /** @var MockObject */
    private $address;

    /** @var MockObject */
    private $customer;

    /** @var MockObject */
    private $connectorResponse;

    /**
     * Tweak to Mock Magic Methods.
     * @see: https://magento.stackexchange.com/a/274761
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->region = $this->createMock(Region::class);
        $this->address = $this->createMock(AddressInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);

        $this->connectorResponse = $this->createPartialMock(
            ConnectorResponse::class,
            array_merge(
                get_class_methods(ConnectorResponse::class),
                ['getUf', 'getCep', 'getBairro', 'getNumero', 'getTelefone', 'getMunicipio', 'getLogradouro', 'getComplemento']
            )
        );

        $this->connectorMock = $this->createMock(Connector::class);
        $this->regionFactoryMock = $this->createMock(RegionFactory::class);
        $this->addressFactoryMock = $this->createMock(AddressInterfaceFactory::class);
        $this->addressRepositoryMock = $this->createMock(AddressRepositoryInterface::class);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);

        $this->subject = $objectManager->getObject(UpdateCustomerAddressesService::class, [
            'sintegraConnector' => $this->connectorMock,
            'regionFactory' => $this->regionFactoryMock,
            'addressFactory' => $this->addressFactoryMock,
            'addressRepository' => $this->addressRepositoryMock,
            'customerRepository' => $this->customerRepositoryMock
        ]);
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testSaveAddressToCustomer()
    {
        //Data.
        $customerData = [
            'id' => 99,
            'taxvat' => 'Test Taxvat',
            'lastname' => 'Test Lastname',
            'firstname' => 'Test Firstname',
            'hb_person_type' => 'legal'
        ];

        $hbPersonTypeCustomAttributeMock = $this->createPartialMock('\StdClass', ['getValue']);
        $hbPersonTypeCustomAttributeMock->method('getValue')->willReturn($customerData['hb_person_type']);

        $this->customer->method('getId')->willReturn($customerData['id']);
        $this->customer->method('getTaxvat')->willReturn($customerData['taxvat']);
        $this->customer->method('getLastname')->willReturn($customerData['lastname']);
        $this->customer->method('getFirstname')->willReturn($customerData['firstname']);
        $this->customer->method('getCustomAttribute')->willReturn($hbPersonTypeCustomAttributeMock);

        $connectorResponseData = [
            'uf' => 'Test UF',
            'cep' => 'Test CEP',
            'pais' => 'BR', //Hardcoded in code.
            'region_id' => 123, //Must be an int.
            'bairro' => 'Test Bairro',
            'numero' => 'Test Numero',
            'telefone' => 'Test Telefone',
            'municipio' => 'Test Municipio',
            'logradouro' => 'Test Logradouro',
            'complemento' => 'Test Complemento',
        ];

        $this->connectorResponse->method('getUf')->willReturn($connectorResponseData['uf']);
        $this->connectorResponse->method('getCep')->willReturn($connectorResponseData['cep']);
        $this->connectorResponse->method('getPais')->willReturn($connectorResponseData['pais']);
        $this->connectorResponse->method('getBairro')->willReturn($connectorResponseData['bairro']);
        $this->connectorResponse->method('getNumero')->willReturn($connectorResponseData['numero']);
        $this->connectorResponse->method('getTelefone')->willReturn($connectorResponseData['telefone']);
        $this->connectorResponse->method('getMunicipio')->willReturn($connectorResponseData['municipio']);
        $this->connectorResponse->method('getLogradouro')->willReturn($connectorResponseData['logradouro']);
        $this->connectorResponse->method('getComplemento')->willReturn($connectorResponseData['complemento']);

        //Expectations.
        $this->region->method('getId')->willReturn($connectorResponseData['region_id']);
        $this->region->expects($this->once())->method('loadByCode')->with($connectorResponseData['uf'], $connectorResponseData['pais'])->willReturn($connectorResponseData['region_id']);

        $this->address->expects($this->once())->method('setStreet')->willReturnSelf();
        $this->address->expects($this->once())->method('setCustomerId')->with($customerData['id'])->willReturnSelf();
        $this->address->expects($this->once())->method('setLastname')->with($customerData['lastname'])->willReturnSelf();
        $this->address->expects($this->once())->method('setFirstname')->with($customerData['firstname'])->willReturnSelf();
        $this->address->expects($this->once())->method('setPostcode')->with($connectorResponseData['cep'])->willReturnSelf();
        $this->address->expects($this->once())->method('setCity')->with($connectorResponseData['municipio'])->willReturnSelf();
        $this->address->expects($this->once())->method('setTelephone')->with($connectorResponseData['telefone'])->willReturnSelf();
        $this->address->expects($this->once())->method('setRegionId')->with($connectorResponseData['region_id'])->willReturnSelf();
        $this->address->expects($this->once())->method('setCountryId')->with($connectorResponseData['pais'])->willReturnSelf();
        $this->address->expects($this->once())->method('setIsDefaultBilling')->with(true)->willReturnSelf();
        $this->address->expects($this->once())->method('setIsDefaultShipping')->with(true)->willReturnSelf();

        $this->addressRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->address);

        //Returns.
        $this->customerRepositoryMock
            ->method('getById')
            ->with($customerData['id'])
            ->willReturn($this->customer);

        $this->connectorMock
            ->method('execute')
            ->with($customerData['taxvat'])
            ->willReturn($this->connectorResponse);

        $this->regionFactoryMock
            ->method('create')
            ->willReturn($this->region);

        $this->addressFactoryMock
            ->method('create')
            ->willReturn($this->address);

        //Call.
        $this->subject->execute($customerData['id']);
    }
}


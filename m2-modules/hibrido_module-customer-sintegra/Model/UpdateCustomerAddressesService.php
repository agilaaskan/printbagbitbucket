<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model;

use Hibrido\CustomerBR\Model\Source\Config\PersonType;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class UpdateCustomerAddressesService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Connector
     */
    private $sintegraConnector;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @param LoggerInterface $logger
     * @param Connector $sintegraConnector
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory $addressFactory
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Connector $sintegraConnector,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressFactory,
        RegionFactory $regionFactory
    ) {
        $this->logger = $logger;
        $this->sintegraConnector = $sintegraConnector;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->addressFactory = $addressFactory;
        $this->regionFactory = $regionFactory;
    }

    /**
     * @param int|string $customerId
     * @return bool
     * @throws LocalizedException
     */
    public function execute($customerId): bool
    {
        //Get clean customer id.
        $customerId = $this->getCleanCustomerId($customerId);

        //Get customer by id.
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (LocalizedException|NoSuchEntityException $e) {
            $this->logger->info('Problem finding customer.', compact('customerId'));
            throw new LocalizedException(__($e->getMessage()));
        }

        //If customer is not PJ or does not have CNPJ bail out.
        try {
            if (!$this->validateCustomer($customer)) {
                return false;
            }
        } catch (LocalizedException $e) {
            $this->logger->info($e->getMessage(), compact('customerId'));
            throw $e;
        }

        //Get Sintegra response.
        $connectorResponse = $this->sintegraConnector->execute($customer->getTaxvat());

        //Create and save address with Sintegra response.
        //Explicitly let the exception bubble up.
        $this->saveAddressToCustomer($connectorResponse, $customer);

        return true;
    }

    /**
     * @param int|string $customerId
     * @return int
     * @throws LocalizedException
     */
    private function getCleanCustomerId($customerId): int
    {
        //Validates if customer id int or string.
        if (!is_int($customerId) && !is_string($customerId)) {
            $message = 'Customer ID is not string neither int.';
            $this->logger->info($message, compact('customerId'));
            throw new LocalizedException(__($message));
        }

        //Return customerId casted to int.
        return (int)$customerId;
    }

    /**
     * Validate customer.
     * Returns true, if everything is fine.
     * Returns false, if customer person type differs from Legal Person.
     * Throws exception, if customer does not have some data or is Legal Person with invalid Tax/VAT.
     *
     * @param CustomerInterface $customer
     * @return bool
     * @throws LocalizedException
     */
    private function validateCustomer(CustomerInterface $customer): bool
    {
        if (!$customer->getCustomAttribute('hb_person_type') || $customer->getTaxvat() == '') {
            throw new LocalizedException(__('Customer does not have hb_person_type or taxvat attribute.'));
        }

        if ($customer->getCustomAttribute('hb_person_type')->getValue() !== PersonType::PERSON_TYPE_LEGAL) {
            return false;
        }

        return true;
    }

    /**
     * @param ConnectorResponse $connectorResponse
     * @param CustomerInterface $customer
     * @return void
     * @throws LocalizedException
     */
    private function saveAddressToCustomer(ConnectorResponse $connectorResponse, CustomerInterface $customer): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->create();

        $address->setFirstname($customer->getFirstname())
            ->setLastname($customer->getLastname())
            ->setCountryId($connectorResponse->getPais())
            ->setCity($connectorResponse->getMunicipio())
            ->setPostcode($connectorResponse->getCep())
            ->setCustomerId($customer->getId())
            ->setTelephone($connectorResponse->getTelefone())
            ->setIsDefaultBilling(true)
            ->setIsDefaultShipping(true)
            ->setStreet([
                $connectorResponse->getLogradouro(),
                $connectorResponse->getNumero(),
                $connectorResponse->getComplemento(),
                $connectorResponse->getBairo()
            ])

            //Explicitly let the exception bubble up.
            ->setRegionId($this->getRegionId($connectorResponse));

        //Explicitly let the exception bubble up.
        $this->addressRepository->save($address);
    }

    /**
     * @param ConnectorResponse $connectorResponse
     * @return int
     * @throws LocalizedException
     */
    private function getRegionId(ConnectorResponse $connectorResponse): int
    {
        $region = $this->regionFactory->create();
        $region->loadByCode($connectorResponse->getUf(), $connectorResponse->getPais());

        if (!$region->getId()) {
            throw new LocalizedException(__('Region not found.'));
        }

        return (int)$region->getId();
    }
}
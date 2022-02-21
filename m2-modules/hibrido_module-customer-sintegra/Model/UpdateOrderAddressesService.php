<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model;

use Hibrido\CustomerBR\Model\Source\Config\PersonType;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateOrderAddressesService
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
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @param LoggerInterface $logger
     * @param Connector $sintegraConnector
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Connector $sintegraConnector,
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        RegionFactory $regionFactory
    ) {
        $this->logger = $logger;
        $this->sintegraConnector = $sintegraConnector;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->regionFactory = $regionFactory;
    }

    /**
     * @param int|string $orderId
     * @return bool
     * @throws LocalizedException
     */
    public function execute($orderId): bool
    {
        //Get clean order id.
        $orderId = $this->getCleanOrderId($orderId);

        //Get order by id.
        //It is not explicit the interface, but, this call may throw exceptions.
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (InputException|NoSuchEntityException $e) {
            $this->logger->info('Problem finding order.', compact('orderId'));
            throw new LocalizedException(__($e->getMessage()));
        }

        //Get customer from order.
        //Explicitly let the exception bubble up.
        $customer = $this->getCustomer($order);

        //If customer is not PJ or does not have CNPJ bail out.
        try {
            if (!$this->validateCustomer($customer)) {
                return false;
            }
        } catch (LocalizedException $e) {
            $this->logger->info($e->getMessage(), compact('orderId'));
            throw $e;
        }

        //Get Sintegra response.
        $connectorResponse = $this->sintegraConnector->execute($order->getCustomerTaxvat());

        //Update order addresses.
        $this->updateOrderAddresses($connectorResponse, $order);

        return true;
    }

    /**
     * @param int|string $orderId
     * @return int
     * @throws LocalizedException
     */
    private function getCleanOrderId($orderId): int
    {
        //Validates if order id int or string.
        if (!is_int($orderId) && !is_string($orderId)) {
            $message = 'Order ID is not string neither int.';
            $this->logger->info($message, compact('orderId'));
            throw new LocalizedException(__($message));
        }

        //Return order id casted to int.
        return (int)$orderId;
    }

    /**
     * @param OrderInterface $order
     * @return CustomerInterface
     * @throws LocalizedException
     */
    private function getCustomer(OrderInterface $order): CustomerInterface
    {
        if (!$customerId = $order->getCustomerId()) {
            throw new LocalizedException(__('Customer ID not found in Order.'));
        }

        //Get customer by id.
        try {
            return $this->customerRepository->getById($customerId);
        } catch (LocalizedException|NoSuchEntityException $e) {
            $this->logger->info('Problem finding customer.', compact('customerId'));
            throw new LocalizedException(__($e->getMessage()));
        }
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
     * @param OrderInterface $order
     * @return void
     * @throws LocalizedException
     */

    private function updateOrderAddresses(ConnectorResponse $connectorResponse, OrderInterface $order): void
    {
        //Explicitly let the exception bubble up.
        if ($billingAddress = $order->getBillingAddress()) {
            $billingAddress = $this->updateOrderAddressWithConnectorResponse($connectorResponse, $billingAddress);
            $order->setBillingAddress($billingAddress);
        }

        //Explicitly let the exception bubble up.
        if ($shippingAddress = $order->getShippingAddress()) {
            $shippingAddress = $this->updateOrderAddressWithConnectorResponse($connectorResponse, $shippingAddress);
            $order->setShippingAddress($shippingAddress);
        }

        //It is not explicit the interface, but, this call may throw exceptions.
        try {
            $this->orderRepository->save($order);
        } catch (InputException|NoSuchEntityException|AlreadyExistsException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param ConnectorResponse $connectorResponse
     * @param OrderAddressInterface $address
     * @return OrderAddressInterface
     * @throws LocalizedException
     */
    private function updateOrderAddressWithConnectorResponse(ConnectorResponse $connectorResponse, OrderAddressInterface $address): OrderAddressInterface
    {
        $address->setCountryId($connectorResponse->getPais())
            ->setCity($connectorResponse->getMunicipio())
            ->setPostcode($connectorResponse->getCep())
            ->setTelephone($connectorResponse->getTelefone())
            ->setStreet([
                $connectorResponse->getLogradouro(),
                $connectorResponse->getNumero(),
                $connectorResponse->getComplemento(),
                $connectorResponse->getBairo()
            ])

        //Explicitly let the exception bubble up.
        ->setRegionId($this->getRegionId($connectorResponse));

        return $address;
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
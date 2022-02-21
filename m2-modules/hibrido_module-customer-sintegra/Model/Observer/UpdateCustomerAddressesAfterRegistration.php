<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model\Observer;

use Hibrido\CustomerSintegra\Model\Queue\UpdateCustomerAddressesPublisher;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class UpdateCustomerAddressesAfterRegistration implements ObserverInterface
{
    /**
     * @var UpdateCustomerAddressesPublisher
     */
    private $updateCustomerAddressesPublisher;

    /**
     * @param UpdateCustomerAddressesPublisher $updateCustomerAddressesPublisher
     */
    public function __construct(UpdateCustomerAddressesPublisher $updateCustomerAddressesPublisher)
    {
        $this->updateCustomerAddressesPublisher = $updateCustomerAddressesPublisher;
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedMethodInspection
     */
    public function execute(Observer $observer): void
    {
        if (!$observer->hasCustomer()) {
            return;
        }

        /** @var CustomerInterface $customer */
        $customer = $observer->getCustomer();

        if (!$customerId = $customer->getId()) {
            return;
        }

        $this->updateCustomerAddressesPublisher->execute($customerId);
    }
}
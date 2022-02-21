<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model\Queue;

use Hibrido\CustomerSintegra\Model\UpdateCustomerAddressesService;
use Magento\Framework\Exception\LocalizedException;

class UpdateCustomerAddressesConsumer
{
    /**
     * @var UpdateCustomerAddressesService
     */
    private $updateCustomerAddressesService;

    /**
     * @param UpdateCustomerAddressesService $updateCustomerAddressesService
     */
    public function __construct(UpdateCustomerAddressesService $updateCustomerAddressesService)
    {
        $this->updateCustomerAddressesService = $updateCustomerAddressesService;
    }

    /**
     * @param string $customerId
     * @throws LocalizedException
     */
    public function process(string $customerId): void
    {
        //Explicitly let the exception bubble up.
        $this->updateCustomerAddressesService->execute($customerId);
    }
}
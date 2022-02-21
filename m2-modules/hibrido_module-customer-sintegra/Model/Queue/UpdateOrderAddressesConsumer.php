<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model\Queue;

use Hibrido\CustomerSintegra\Model\UpdateOrderAddressesService;
use Magento\Framework\Exception\LocalizedException;

class UpdateOrderAddressesConsumer
{
    /**
     * @var UpdateOrderAddressesService
     */
    private $updateOrderAddressesService;

    /**
     * @param UpdateOrderAddressesService $updateOrderAddressesService
     */
    public function __construct(UpdateOrderAddressesService $updateOrderAddressesService)
    {
        $this->updateOrderAddressesService = $updateOrderAddressesService;
    }

    /**
     * @param string $customerId
     * @throws LocalizedException
     */
    public function process(string $customerId): void
    {
        //Explicitly let the exception bubble up.
        $this->updateOrderAddressesService->execute($customerId);
    }
}
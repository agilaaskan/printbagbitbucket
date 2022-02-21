<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model\Plugin\Magento\Sales\Api\OrderManagementInterface;

use Hibrido\CustomerSintegra\Model\Queue\UpdateOrderAddressesPublisher;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;

class AddOrderIdToUpdateAddressesQueue
{
    /**
     * @var UpdateOrderAddressesPublisher
     */
    private $updateOrderAddressesPublisher;

    /**
     * @param UpdateOrderAddressesPublisher $updateOrderAddressesPublisher
     */
    public function __construct(UpdateOrderAddressesPublisher $updateOrderAddressesPublisher)
    {
        $this->updateOrderAddressesPublisher = $updateOrderAddressesPublisher;
    }

    /**
     * @param OrderManagementInterface $orderManagement
     * @param OrderInterface $order
     * @return OrderInterface $order
     */
    public function afterPlace(OrderManagementInterface $orderManagement , $order): OrderInterface
    {
        if (!$orderId = $order->getId()) {
            return $order;
        }

        $this->updateOrderAddressesPublisher->execute($orderId);

        return $order;
    }
}
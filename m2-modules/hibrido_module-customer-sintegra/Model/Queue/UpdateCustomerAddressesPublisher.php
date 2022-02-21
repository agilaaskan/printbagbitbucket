<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model\Queue;

use Magento\Framework\MessageQueue\PublisherInterface;

class UpdateCustomerAddressesPublisher
{
    const TOPIC_NAME = 'customerSintegra.updateCustomerAddressesTopic';

    /**
     * @var PublisherInterface
     */
    private $queuePublisher;

    /**
     * @param PublisherInterface $queuePublisher
     */
    public function __construct(PublisherInterface $queuePublisher)
    {
        $this->queuePublisher = $queuePublisher;
    }

    /**
     * @param int|string $customerId
     */
    public function execute($customerId): void
    {
        /** @noinspection PhpParamsInspection */
        $this->queuePublisher->publish(self::TOPIC_NAME, (string)$customerId);
    }
}
<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomerAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerAttributes\Observer;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\CustomerAttributes\Model\Address\OrderFactory;

/**
 * Class SalesOrderAddressCollectionAfterLoad
 * @package Mageplaza\CustomerAttributes\Observer
 */
class SalesOrderAddressCollectionAfterLoad implements ObserverInterface
{
    /**
     * @var OrderFactory
     */
    protected $orderAddressFactory;

    /**
     * @param OrderFactory $orderAddressFactory
     */
    public function __construct(OrderFactory $orderAddressFactory)
    {
        $this->orderAddressFactory = $orderAddressFactory;
    }

    /**
     * After load observer for collection of order address
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderAddressCollection();
        if ($collection instanceof AbstractDb) {
            $orderAddress = $this->orderAddressFactory->create();
            $orderAddress->attachDataToEntities($collection->getItems());
        }

        return $this;
    }
}

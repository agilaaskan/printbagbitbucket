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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Mageplaza\CustomerAttributes\Model\Address\OrderFactory;

/**
 * Class SalesOrderAddressAfterLoad
 * @package Mageplaza\CustomerAttributes\Observer
 */
class SalesOrderAddressAfterLoad implements ObserverInterface
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
     * After load observer for order address
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        if ($address instanceof AbstractModel) {
            $orderAddress = $this->orderAddressFactory->create();
            $orderAddress->attachDataToEntities([$address]);
        }

        return $this;
    }
}

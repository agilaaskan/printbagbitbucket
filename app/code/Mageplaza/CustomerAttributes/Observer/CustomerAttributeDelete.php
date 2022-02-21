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

use Magento\Customer\Model\Attribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\CustomerAttributes\Model\Order;
use Mageplaza\CustomerAttributes\Model\OrderFactory;
use Mageplaza\CustomerAttributes\Model\Quote;
use Mageplaza\CustomerAttributes\Model\QuoteFactory;

/**
 * Class CustomerAttributeDelete
 * @package Mageplaza\CustomerAttributes\Observer
 */
class CustomerAttributeDelete implements ObserverInterface
{
    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @param OrderFactory $orderFactory
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(
        OrderFactory $orderFactory,
        QuoteFactory $quoteFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * After delete observer for customer attribute
     *
     * @param Observer $observer
     *
     * @return $this|void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Attribute && !$attribute->isObjectNew()) {
            /** @var $quoteModel Quote */
            $quoteModel = $this->quoteFactory->create();
            $quoteModel->deleteAttribute($attribute);
            /** @var $orderModel Order */
            $orderModel = $this->orderFactory->create();
            $orderModel->deleteAttribute($attribute);
        }

        return $this;
    }
}

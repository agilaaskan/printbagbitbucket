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
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\CustomerAttributes\Model\Address\QuoteFactory;

/**
 * Class SalesQuoteAddressCollectionAfterLoad
 * @package Mageplaza\CustomerAttributes\Observer
 */
class SalesQuoteAddressCollectionAfterLoad implements ObserverInterface
{
    /**
     * @var QuoteFactory
     */
    protected $quoteAddressFactory;

    /**
     * @param QuoteFactory $quoteAddressFactory
     */
    public function __construct(QuoteFactory $quoteAddressFactory)
    {
        $this->quoteAddressFactory = $quoteAddressFactory;
    }

    /**
     * After load observer for collection of quote address
     *
     * @param Observer $observer
     *
     * @return $this|void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getQuoteAddressCollection();
        if ($collection instanceof AbstractDb) {
            $quoteAddress = $this->quoteAddressFactory->create();
            $quoteAddress->attachDataToEntities($collection->getItems());
        }

        return $this;
    }
}

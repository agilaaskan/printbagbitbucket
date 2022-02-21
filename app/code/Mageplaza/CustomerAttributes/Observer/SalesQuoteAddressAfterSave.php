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

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Mageplaza\CustomerAttributes\Model\Address\QuoteFactory;

/**
 * Class SalesQuoteAddressAfterSave
 * @package Mageplaza\CustomerAttributes\Observer
 */
class SalesQuoteAddressAfterSave implements ObserverInterface
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
     * After save observer for quote address
     *
     * @param Observer $observer
     *
     * @return $this|void
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $quoteAddress = $observer->getEvent()->getQuoteAddress();
        if ($quoteAddress instanceof AbstractModel) {
            $quoteAddressModel = $this->quoteAddressFactory->create();
            $quoteAddressModel->saveAttributeData($quoteAddress);
        }

        return $this;
    }
}

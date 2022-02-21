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
use Magento\Quote\Model\Quote;
use Mageplaza\CustomerAttributes\Model\QuoteFactory;

/**
 * Class SalesQuoteAfterSave
 * @package Mageplaza\CustomerAttributes\Observer
 */
class SalesQuoteAfterSave implements ObserverInterface
{
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(QuoteFactory $quoteFactory)
    {
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * After save observer for quote
     *
     * @param Observer $observer
     *
     * @return $this|void
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof AbstractModel) {
            $quoteModel       = $this->quoteFactory->create();
            $customAttributes = $quote->getCustomer()->getCustomAttributes();
            if (!empty($customAttributes)) {
                foreach ($customAttributes as $customAttribute) {
                    $quote->setData('customer_' . $customAttribute->getAttributeCode(), $customAttribute->getValue());
                }
            }
            $quoteModel->saveAttributeData($quote);
        }

        return $this;
    }
}

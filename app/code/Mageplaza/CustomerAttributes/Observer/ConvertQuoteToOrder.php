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
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Api\Data\OrderInterface;
use Mageplaza\CustomerAttributes\Helper\Data;
use Mageplaza\CustomerAttributes\Model\Address\QuoteFactory as QuoteAddressFactory;
use Zend_Validate_File_Upload;

/**
 * Class ConvertQuoteToOrder
 * @package Mageplaza\CustomerAttributes\Observer
 */
class ConvertQuoteToOrder implements ObserverInterface
{
    /**
     * @var string
     */
    protected $scope = 'order';

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Zend_Validate_File_Upload
     */
    protected $fileUpload;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var QuoteAddressFactory
     */
    protected $quoteAddressFactory;

    /**
     * ConvertQuoteToOrder constructor.
     *
     * @param Data $helperData
     * @param Zend_Validate_File_Upload $fileUpload
     * @param Filesystem $filesystem
     * @param QuoteAddressFactory $quoteAddressFactory
     */
    public function __construct(
        Data $helperData,
        Zend_Validate_File_Upload $fileUpload,
        Filesystem $filesystem,
        QuoteAddressFactory $quoteAddressFactory
    ) {
        $this->helperData          = $helperData;
        $this->fileUpload          = $fileUpload;
        $this->filesystem          = $filesystem;
        $this->quoteAddressFactory = $quoteAddressFactory;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws LocalizedException
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var AbstractExtensibleModel|OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $this->setCustomData($quote, $order);

        $customerAttributes = $this->helperData->getUserDefinedAttributeCodes('customer');
        foreach ($customerAttributes as $attribute) {
            $order->setData('customer_' . $attribute, $quote->getData('customer_' . $attribute));
        }

        if (!$this->helperData->isAdmin()) {
            $fileAddressAttributes = $this->helperData->getUserDefinedFileAttributeCodes('customer_address');
            $quoteAddress          = [$quote->getBillingAddress()];
            if (!$quote->isVirtual()) {
                $quoteAddress[] = $quote->getShippingAddress();
            }

            foreach ($quoteAddress as $address) {
                foreach ($fileAddressAttributes as $fileAddressAttribute) {
                    $this->validateAddressFile($address, $fileAddressAttribute);
                }
            }
        }

        return $this;
    }

    /**
     * @param Address $address
     * @param string $attributeCode
     *
     * @return bool
     * @throws InputException
     */
    public function validateAddressFile($address, $attributeCode)
    {
        $mpcaQuoteAddress = $this->quoteAddressFactory->create()->load($address->getId());

        return $this->helperData->validateFile(
            $address->getData($attributeCode),
            $mpcaQuoteAddress->getData($attributeCode),
            $attributeCode
        );
    }

    /**
     * @param Quote $quote
     * @param OrderInterface $order
     */
    public function setCustomData($quote, $order)
    {
    }
}

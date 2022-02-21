<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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

namespace Mageplaza\CustomerAttributes\Model;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\AddressFactory as QuoteAddressFactory;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Mageplaza\CustomerAttributes\Api\AddressAttributesRepositoryInterface;
use Mageplaza\CustomerAttributes\Helper\Data;
use Mageplaza\CustomerAttributes\Model\Address\QuoteFactory as QuoteAddressAttributeFactory;

/**
 * Class AddressAttributesRepository
 * @package Mageplaza\CustomerAttributes\Model
 */
class AddressAttributesRepository implements AddressAttributesRepositoryInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var QuoteAddressAttributeFactory
     */
    protected $quoteAttributeAddressFactory;

    /**
     * @var QuoteAddressFactory
     */
    protected $quoteAddressFactory;

    /**
     * AddressAttributesRepository constructor.
     *
     * @param Data $helperData
     * @param CartRepositoryInterface $cartRepository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuoteAddressAttributeFactory $quoteAttributeAddressFactory
     * @param QuoteAddressFactory $quoteAddressFactory
     */
    public function __construct(
        Data $helperData,
        CartRepositoryInterface $cartRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteAddressAttributeFactory $quoteAttributeAddressFactory,
        QuoteAddressFactory $quoteAddressFactory
    ) {
        $this->helperData                   = $helperData;
        $this->cartRepository               = $cartRepository;
        $this->quoteIdMaskFactory           = $quoteIdMaskFactory;
        $this->quoteAttributeAddressFactory = $quoteAttributeAddressFactory;
        $this->quoteAddressFactory          = $quoteAddressFactory;
    }

    /**
     * @inheritDoc
     */
    public function guestUpload($cartId, $addressType)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->upload($quoteIdMask->getQuoteId(), $addressType);
    }

    /**
     * @inheritDoc
     */
    public function upload($cartId, $addressType)
    {
        try {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->cartRepository->getActive($cartId);
            $files = $this->helperData->convertFilesArray();
            if (empty($files)) {
                throw new LocalizedException(__('File is empty.'));
            }

            $attrCode = key($files);
            $result   = $this->helperData->uploadFile($attrCode, $files);

            if (!$result->getError()) {
                $addressId             = $addressType === 'shipping' ? $quote->getShippingAddress()->getId() : $quote->getBillingAddress()->getId();
                $quoteAddressAttribute = $this->quoteAttributeAddressFactory->create();
                $quoteAddress          = $this->quoteAddressFactory->create()->load($addressId);
                $quoteAddress->setData($attrCode, Data::jsonEncode($result->getData()));
                $quoteAddressAttribute->saveAttributeData($quoteAddress);
            }

            return $result;
        } catch (Exception $e) {
            $result = [
                'error' => __($e->getMessage())
            ];

            return new FileResult($result);
        }
    }
}

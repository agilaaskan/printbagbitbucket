<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Hibrido\InstallmentPrice\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class InstallmentPrice extends Template
{
    /**
     * @var EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @param Template\Context $context
     * @param EncoderInterface $jsonEncoder
     * @param FormatInterface $localeFormat
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        EncoderInterface $jsonEncoder,
        FormatInterface $localeFormat,
        Registry $registry,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_localeFormat = $localeFormat;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!isset($this->product)) {
            $this->product = $this->hasData('product')
                ? $this->getData('product')
                : $this->registry->registry('product');
        }

        return $this->product;
    }
    
    /**
     * @return float
     */
    public function getInterestInitialPercentage()
    {
        return (float) $this->_scopeConfig->getValue('catalog/installment/interest_rate_initial', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return float
     */
    public function getInterestIncrementalPercentage()
    {
        return (float) $this->_scopeConfig->getValue('catalog/installment/interest_rate_incremental', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getInstallmentNumber()
    {
        return (int) $this->_scopeConfig->getValue('catalog/installment/number', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getInstallmentMinQuota()
    {
        return (int) $this->_scopeConfig->getValue('catalog/installment/min_quota', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getInstallmentNumberWithoutInterest()
    {
        return (int) $this->_scopeConfig->getValue('catalog/installment/max_without_interest', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getIsEnabledOnProductList()
    {
        return $this->_scopeConfig->isSetFlag('catalog/installment/list_is_enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getIsEnabledOnProductPage()
    {
        return $this->_scopeConfig->isSetFlag('catalog/installment/product_is_enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getShowOnlyTheLastOneInProductList()
    {
        return $this->_scopeConfig->isSetFlag('catalog/installment/list_show_only_last', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getShowOnlyTheLastOneInProductPage()
    {
        return  $this->_scopeConfig->getValue('catalog/installment/product_show_only_last', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getPriceFormatJsonConfig()
    {
        return $this->_jsonEncoder->encode($this->_localeFormat->getPriceFormat());
    }
}
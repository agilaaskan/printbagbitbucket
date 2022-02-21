<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hibrido\InstallmentPrice\Model;

class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    private $localeFormat;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonEncoder = $jsonEncoder;
        $this->localeFormat = $localeFormat;
    }

    /**
     * @return float
     */
    public function getInterestInitialPercentage()
    {
        return (float) $this->scopeConfig->getValue('catalog/installment/interest_rate_initial');
    }

    /**
     * @return float
     */
    public function getInterestIncrementalPercentage()
    {
        return (float) $this->scopeConfig->getValue('catalog/installment/interest_rate_incremental');
    }

    /**
     * @return int
     */
    public function getInstallmentNumber()
    {
        return (int) $this->scopeConfig->getValue('catalog/installment/number');
    }

    /**
     * @return int
     */
    public function getInstallmentMinQuota()
    {
        return (int) $this->scopeConfig->getValue('catalog/installment/min_quota');
    }

    /**
     * @return int
     */
    public function getInstallmentNumberWithoutInterest()
    {
        return (int) $this->scopeConfig->getValue('catalog/installment/max_without_interest');
    }

    /**
     * @return bool
     */
    public function getIsEnabledOnProductList()
    {
        return $this->scopeConfig->isSetFlag('catalog/installment/list_is_enabled');
    }

    /**
     * @return bool
     */
    public function getIsEnabledOnProductPage()
    {
        return $this->scopeConfig->isSetFlag('catalog/installment/product_is_enabled');
    }

    /**
     * @return bool
     */
    public function getShowOnlyTheLastOneInProductList()
    {
        return $this->scopeConfig->isSetFlag('catalog/installment/list_show_only_last');
    }

    /**
     * @return string
     */
    public function getShowOnlyTheLastOneInProductPage()
    {
        return  $this->scopeConfig->getValue('catalog/installment/product_show_only_last');
    }

    /**
     * @return string
     */
    public function getPriceFormatJsonConfig()
    {
        return $this->jsonEncoder->encode($this->localeFormat->getPriceFormat());
    }
}

<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hibrido\InstallmentPrice\Model\ProductRender;

class InstallmentPrice extends \Magento\Framework\DataObject implements \Hibrido\InstallmentPrice\Api\Data\ProductRender\InstallmentPriceInterface
{
    /**
     * {@inheritdoc}
     */
    public function setConfigInterestInitialPercentage($configInterestInitialPercentage)
    {
        $this->setData('config_interest_initial_percentage', $configInterestInitialPercentage);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigInterestIncrementalPercentage($configInterestIncrementalPercentage)
    {
        $this->setData('config_interest_incremental_percentage', $configInterestIncrementalPercentage);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigInstallmentNumber($configInstallmentNumber)
    {
        $this->setData('config_installment_number', $configInstallmentNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigInterestPriceQuote($configInterestPriceQuote)
    {
        $this->setData('config_interest_price_quote', $configInterestPriceQuote);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigInstallmentNumberWithoutInterest($configInstallmentNumberWithoutInterest)
    {
        $this->setData('config_installment_number_without_interest', $configInstallmentNumberWithoutInterest);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigInstallmentShowOnlyLastOne($configInstallmentShowOnlyLastOne)
    {
        $this->setData('config_installment_show_only_last_one', $configInstallmentShowOnlyLastOne);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigPriceFormat($configPriceFormat)
    {
        $this->setData('config_price_format', $configPriceFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigInterestInitialPercentage()
    {
        return $this->getData('config_interest_initial_percentage');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigInterestIncrementalPercentage()
    {
        return $this->getData('config_interest_incremental_percentage');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigInstallmentNumber()
    {
        return $this->getData('config_installment_number');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigInterestPriceQuote()
    {
        return $this->getData('config_interest_price_quote');
    }

    /**
     * @return mixed
     */
    public function getConfigInstallmentNumberWithoutInterest()
    {
        return $this->getData('config_installment_number_without_interest');
    }

    /**
     * @return mixed
     */
    public function getConfigInstallmentShowOnlyLastOne()
    {
        return $this->getData('config_installment_show_only_last_one');
    }

    /**
     * @return mixed
     */
    public function getConfigPriceFormat()
    {
        return $this->getData('config_price_format');
    }
}

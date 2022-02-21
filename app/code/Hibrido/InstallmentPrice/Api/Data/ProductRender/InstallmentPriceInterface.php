<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hibrido\InstallmentPrice\Api\Data\ProductRender;

interface InstallmentPriceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @param mixed $configInterestInitialPercentage
     * @return void
     */
    public function setConfigInterestInitialPercentage($configInterestInitialPercentage);

    /**
     * @param mixed $configInterestIncrementalPercentage
     * @return void
     */
    public function setConfigInterestIncrementalPercentage($configInterestIncrementalPercentage);

    /**
     * @param mixed $configInstallmentNumber
     * @return void
     */
    public function setConfigInstallmentNumber($configInstallmentNumber);

    /**
     * @param mixed $configInterestPriceQuote
     * @return void
     */
    public function setConfigInterestPriceQuote($configInterestPriceQuote);

    /**
     * @param mixed $configInstallmentNumberWithoutInterest
     * @return void
     */
    public function setConfigInstallmentNumberWithoutInterest($configInstallmentNumberWithoutInterest);

    /**
     * @param mixed $configInstallmentShowOnlyLastOne
     * @return void
     */
    public function setConfigInstallmentShowOnlyLastOne($configInstallmentShowOnlyLastOne);

    /**
     * @param mixed $configPriceFormat
     * @return void
     */
    public function setConfigPriceFormat($configPriceFormat);

    /**
     * @return mixed
     */
    public function getConfigInterestInitialPercentage();

    /**
     * @return mixed
     */
    public function getConfigInterestIncrementalPercentage();

    /**
     * @return mixed
     */
    public function getConfigInstallmentNumber();

    /**
     * @return mixed
     */
    public function getConfigInterestPriceQuote();

    /**
     * @return mixed
     */
    public function getConfigInstallmentNumberWithoutInterest();

    /**
     * @return mixed
     */
    public function getConfigInstallmentShowOnlyLastOne();

    /**
     * @return mixed
     */
    public function getConfigPriceFormat();
}

<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hibrido\InstallmentPrice\Ui\DataProvider\Product\Listing\Collector;

class InstallmentPrice implements \Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param \Hibrido\InstallmentPrice\Model\Config $config
     */
    public function __construct(
        \Hibrido\InstallmentPrice\Model\Config $config,
        \Magento\Catalog\Api\Data\ProductRenderExtensionFactory $productRenderExtensionFactory,
        \Hibrido\InstallmentPrice\Model\ProductRender\InstallmentPriceFactory $productRenderInstallmentPriceFactory
    ) {
        $this->config = $config;
        $this->productRenderExtensionFactory = $productRenderExtensionFactory;
        $this->productRenderInstallmentPriceFactory = $productRenderInstallmentPriceFactory;
    }

    /**
     * @inheritdoc
     */
    public function collect(\Magento\Catalog\Api\Data\ProductInterface $product, \Magento\Catalog\Api\Data\ProductRenderInterface $productRender)
    {
        if (!$extensionAttributes = $productRender->getExtensionAttributes()) {
            $extensionAttributes = $this->productRenderExtensionFactory->create();
        }

        $productRenderInstallmentPrice = $this->productRenderInstallmentPriceFactory->create();

        $productRenderInstallmentPrice->setConfigInterestInitialPercentage($this->config->getInterestInitialPercentage());
        $productRenderInstallmentPrice->setConfigInterestIncrementalPercentage($this->config->getInterestIncrementalPercentage());
        $productRenderInstallmentPrice->setConfigInterestPriceQuote($this->config->getInstallmentMinQuota());
        $productRenderInstallmentPrice->setConfigInstallmentNumber($this->config->getInstallmentNumber());
        $productRenderInstallmentPrice->setConfigInstallmentNumberWithoutInterest($this->config->getInstallmentNumberWithoutInterest());
        $productRenderInstallmentPrice->setConfigInstallmentShowOnlyLastOne($this->config->getShowOnlyTheLastOneInProductList());
        $productRenderInstallmentPrice->setConfigPriceFormat($this->config->getPriceFormatJsonConfig());

        $extensionAttributes->setInstallmentPrice($productRenderInstallmentPrice);
        $productRender->setExtensionAttributes($extensionAttributes);
    }
}

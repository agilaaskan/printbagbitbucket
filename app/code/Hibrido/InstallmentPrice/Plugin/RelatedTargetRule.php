<?php

/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hibrido\InstallmentPrice\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Layout;

class RelatedTargetRule
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var Layout
     */
    private $layout;

    /**
     * @param ScopeConfigInterface $config
     * @param Layout $layout
     */
    public function __construct(ScopeConfigInterface $config, Layout $layout)
    {
        $this->config = $config;
        $this->layout = $layout;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetProductPrice(
        \Magento\TargetRule\Block\Catalog\Product\ProductList\Related $subject,
        callable $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $price = $proceed($product);

        $price .= $this->layout
            ->createBlock(\Hibrido\InstallmentPrice\Block\InstallmentPrice::class)
            ->setTemplate('Hibrido_InstallmentPrice::product/price/installments.phtml')
            ->setIsProductListing(true)
            ->setProduct($product)
            ->toHtml();

        return $price;
    }
}
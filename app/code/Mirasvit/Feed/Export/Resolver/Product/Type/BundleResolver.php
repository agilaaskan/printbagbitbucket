<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.1.24
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Feed\Export\Resolver\Product\Type;

use Magento\Catalog\Model\Product;
use Mirasvit\Feed\Export\Resolver\ProductResolver;

class BundleResolver extends ProductResolver
{
    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getAssociatedProducts($product)
    {
        /** @var \Magento\Bundle\Model\Product\Type $type */
        $type = $product->getTypeInstance();

        return $type->getAssociatedProducts($type);
    }
}
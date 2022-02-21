<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AttrBaseSplitcart
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\AttrBaseSplitcart\Model\Config;

class ProductAttribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Store\Model\ResourceModel\Store\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $collection = $this->collectionFactory->create()->addVisibleFilter()
                           ->addFieldToFilter("frontend_input", ["eq"=>"select"]);
        $array = ['status','quantity_and_stock_status'];
        foreach ($collection as $key => $attribute) {
            if (!in_array($attribute->getAttributeCode(), $array)) {
                $options[$key]['label'] = $attribute->getFrontendLabel();
                $options[$key]['value'] = $attribute->getAttributeCode();
            }
        }
        return $options;
    }
}

<?php
/**
 * @author Mconnect Team
 * @package Mconnect_Csproduct
 */
namespace Mconnect\Csproduct\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class DisplayMessageListPage implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'none', 'label' => __('None')],
            ['value' => 'catalog_category_view', 'label' => __('Catalog Category View')],
            ['value' => 'catalog_product_view', 'label' => __('Catalog Product View')],
            ['value' => 'cms_index_index', 'label' => __('Cms Page')],
            
        ];
    }
}

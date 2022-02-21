<?php
/**
 * @author Mconnect Team
 * @package Mconnect_Csproduct
 */
namespace Mconnect\Csproduct\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PriorityIndex implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            
            ['value' => 'CGP', 'label' => __('Customer Specific Price')],
            ['value' => 'GP', 'label' => __('Group Specific Price')],
        ];
    }
}

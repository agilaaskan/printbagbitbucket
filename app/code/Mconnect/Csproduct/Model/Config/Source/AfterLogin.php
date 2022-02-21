<?php
/**
 * @author Mconnect Team
 * @package Mconnect_Csproduct
 */
namespace Mconnect\Csproduct\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class AfterLogin implements ArrayInterface
{
    public function toOptionArray()
    {
        return [            
			['value' => 'referral_url', 'label' => __('Referral Url')],            
			['value' => 'account_page', 'label' => __('Account Page')],        ];
    }
}

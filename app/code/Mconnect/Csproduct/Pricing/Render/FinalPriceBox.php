<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mconnect\Csproduct\Pricing\Render;

use Magento\Catalog\Pricing\Price;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Msrp\Pricing\Price\MsrpPrice;

/**
 * Class for final_price rendering
 *
 * @method bool getUseLinkForAsLowAs()
 * @method bool getDisplayMinimalPrice()
 */
class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
	
    /**
     * Wrap with standard required container
     *
     * @param string $html
     * @return string
     */
    protected function wrapResult($html)
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();			
		$httpContext=$objectManager->get('Magento\Framework\App\Http\Context');			
		$isLoggedIn=$httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
		
		$helper=$objectManager->get('Mconnect\Csproduct\Helper\Data');
		$hidePrice=$helper->getConfig('mconnect_csproduct/general/hide_price_for_guest_user');	
		$userLoginText=$helper->getConfig('mconnect_csproduct/general/user_login_text');

		$isEnable=$helper->getConfig('mconnect_csproduct/general/active');		
		
		
		
			
		if(!$isLoggedIn && $hidePrice && $isEnable){
			
			$fullAction = $this->getRequest()->getFullActionName();		
			$whereDisplayMessage=$helper->getConfig('mconnect_csproduct/general/where_display_message');
			$whereDisplayMessageArray= explode(',',$whereDisplayMessage);
			
			if(in_array($fullAction,$whereDisplayMessageArray)){
				
				return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
            'data-role="priceBox" ' .
            'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
            '><div class="mcs-user-login-text">'.$userLoginText.'</div></div>';
				
			}else{
				return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
            'data-role="priceBox" ' .
            'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
            '><div class="mcs-user-login-text"></div></div>';
			}			
			
		}else{
			return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
				'data-role="priceBox" ' .
				'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
				'>' . $html . '</div>';	
		}
		
		
    }

}
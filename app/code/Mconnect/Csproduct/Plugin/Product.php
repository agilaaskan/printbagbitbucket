<?php
namespace Mconnect\Csproduct\Plugin;
 
class Product
{
   
    protected $_objectManager = null;
    
    protected $_helper;
        
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Mconnect\Csproduct\Helper\Data $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
    }
        
    public function afterIsSaleable(\Magento\Catalog\Model\Product $product, $isSaleable)
    {
        
        $hidePrice=$this->_helper->getConfig('mconnect_csproduct/general/hide_price_for_guest_user');
        $isEnable=$this->_helper->getConfig('mconnect_csproduct/general/active');
        
        $httpContext=$this->_objectManager->get('Magento\Framework\App\Http\Context');
        $isLoggedIn=$httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if (!$isLoggedIn && $hidePrice && $isEnable) {
            return false;
        } else {
            return $isSaleable;
        }
        return $isSaleable;
    }
    
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $productId = $subject->getId();
        $typeId=$subject->getTypeId();
        
        if ($typeId=='bundle') {
            $getprice =$this->_helper->getpricebyprdId($productId);
            if (!empty($getprice) && ($getprice > 0)) {
                return $getprice;
            } else {
                return $result;
            }
        } else {
            return $result;
        }
    }
}

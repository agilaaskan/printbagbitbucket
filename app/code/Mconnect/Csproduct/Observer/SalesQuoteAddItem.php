<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class SalesQuoteAddItem implements ObserverInterface
{

    protected $_catalogProductHelper;
        
    protected $_scopeConfigObject;
    
    protected $_storeManager;
        
    protected $_request;
        
    protected $_logger;
        
    protected $_csgroupproductModel;
        
    protected $_csPriceModel;
                
    
    public function __construct(
        \Magento\Catalog\Helper\Product $catalogProductHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Mconnect\Csproduct\Model\CsgroupproductFactory $csgroupproduct,
        \Mconnect\Csproduct\Model\CspriceFactory $csPriceModel,
        \Psr\Log\LoggerInterface $logger
    ) {
            
        $this->_catalogProductHelper = $catalogProductHelper;
        $this->_scopeConfigObject = $scopeConfigObject;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_sessionQuote = $sessionQuote;
        $this->_csgroupproductModel = $csgroupproduct;
        $this->_csPriceModel = $csPriceModel;
        $this->_logger = $logger;
    }
    
   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
         
        $item = $observer->getQuoteItem();
        $productId= $item->getProductId();
        $_customer = $this->_sessionQuote->getQuote()->getCustomer();
        $customerId = $_customer->getId();
        if (!$customerId) {
            return;
        }
        $groupId=$_customer->getGroupId();
        $followproductsfromgroup = $_customer->getCustomAttribute('followproductsfromgroup')->getValue();
        
        if ($followproductsfromgroup==1) {
            $priceColls = $this->_csgroupproductModel->create()->getCollection()->addFieldToFilter('group_id', $groupId)->addFieldToFilter('product_id', $productId)->getFirstItem();
            //$priceColls->getSelect();
            $price = '';
            if (!empty($priceColls->getCsgpPrice())) {
                $price  = $priceColls->getCsgpPrice();
            }
        } else {
            $priceColls = $this->_csPriceModel->create()->getCollection()->addFieldToFilter('customer_id', $customerId)->addFieldToFilter('product_id', $productId)->getFirstItem();
            //$priceColls->getSelect();
            $price = '';
            if (!empty($priceColls->getCsPrice())) {
                $price  = $priceColls->getCsPrice();
            }
        }
                
        if (!empty($price) && $price > 0) {
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->save();
            return;
        }
            return;
    }
}

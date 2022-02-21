<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class CatalogProductGetFinalprice implements ObserverInterface {

		protected $_scopeConfigObject;			
		
		protected $_request;
		
		protected $_customerSession;
		
		protected $_helper;
		
		protected $_storeManager;

	
		public function __construct(				

			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
			\Magento\Framework\App\RequestInterface $request,
			\Magento\Customer\Model\Session $customerSession, 	
			\Mconnect\Csproduct\Helper\Data $helper,
			\Magento\Store\Model\StoreManagerInterface $storeManager
			){
						
			$this->_scopeConfigObject = $scopeConfigObject;			
			$this->_request = $request;
			$this->_customerSession = $customerSession;
			$this->_helper = $helper;
			$this->_storeManager = $storeManager;
		}
	
   
   
		public function execute(\Magento\Framework\Event\Observer $observer){

				
			$product = $observer->getEvent()->getProduct();		
			
			$getprice =$this->_helper->getpricebyprdId($product->getEntityId());			
			
			if(!empty($getprice) &&  ($getprice > 0)){				
			
			$product->setMinimalPrice($getprice);
			$product->setMinPrice($getprice);
			$product->setMaxPrice($getprice);
			$product->setPrice($getprice);			
			$product->setFinalPrice($getprice);
			$product->setCustomPrice($getprice);
			$product->setOriginalCustomPrice($getprice);
			}
			
			
			
		 return $this;
		}
   
}
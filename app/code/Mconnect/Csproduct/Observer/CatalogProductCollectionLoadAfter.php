<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class CatalogProductCollectionLoadAfter implements ObserverInterface {

		protected $_scopeConfigObject;			
		
		protected $_request;
		
		protected $_customerSession;
		
		protected $_helper;
		
		protected $_storeManager;
		
		/**
		 * @var \Magento\Catalog\Model\ProductFactory
		 */
		protected $_productFactory;
		
		protected $_productLoader;  
		 
		protected $logger;


	
		public function __construct(				

			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
			\Magento\Framework\App\RequestInterface $request,
			\Magento\Customer\Model\Session $customerSession, 	
			\Mconnect\Csproduct\Helper\Data $helper,
			\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
			\Magento\Catalog\Model\ProductFactory $_productLoader,
			\Psr\Log\LoggerInterface $logger,
			\Magento\Store\Model\StoreManagerInterface $storeManager
			){
						
			$this->_scopeConfigObject = $scopeConfigObject;			
			$this->_request = $request;
			$this->_customerSession = $customerSession;
			$this->_helper = $helper;
			$this->_productFactory = $productFactory;
			$this->_productLoader = $_productLoader;
			$this->logger = $logger;
			$this->_storeManager = $storeManager;
		}
	
   
   
		public function execute(\Magento\Framework\Event\Observer $observer){

			$products = $observer->getCollection();
			$event = $observer->getEvent();
			
		
			if($this->_customerSession->isLoggedIn()){
				
				$customer = $this->_customerSession->getCustomer();			
			    $customerId = $customer->getId();			
			
			
				foreach($products as $product){					
					
					//$this->logger->debug($product->getTypeId());
					
					if( $product->getTypeId() == 'grouped'){
						
						/* for group product */

					
						
					}else if(($product->getTypeId() == 'simple') || ($product->getTypeId() == 'configurable') || ($product->getTypeId() == 'downloadable')  || ($product->getTypeId() == 'virtual') || ($product->getTypeId() == 'bundle')){
						
						
						 $getprice =$this->_helper->getpricebyprdId($product->getEntityId());
												
						if(!empty($getprice) &&  ($getprice > 0) )
						{
							$product->setMinimalPrice($getprice );
							$product->setMinPrice($getprice );
							$product->setMaxPrice($getprice );
							$product->setPrice($getprice );
							$product->setFinalPrice( $getprice );	
							
							$product->setCustomPrice($getprice);
							$product->setOriginalCustomPrice($getprice);
					

						}	
					
					}				
					
				}
				return $this;
			
			}
			
		 
		}
   
}
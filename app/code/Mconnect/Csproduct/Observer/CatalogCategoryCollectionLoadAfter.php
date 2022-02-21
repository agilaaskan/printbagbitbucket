<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class CatalogCategoryCollectionLoadAfter implements ObserverInterface
{

    protected $_catalogProductHelper;
    
    protected $_customerSession;
    
    protected $csmodel;
    
    protected $cscollection;
    
//	protected $_objectManager = null;
    
    protected $_helper;
    

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product $catalogProductHelper,
        \Mconnect\Csproduct\Helper\McsHelper $mcsHelper,
        \Mconnect\Csproduct\Model\CsproductFactory $csproduct,
        \Mconnect\Csproduct\Model\ResourceModel\Csproduct\CollectionFactory $cscollection,
        //	\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Mconnect\Csproduct\Model\CsrestricgroupproductFactory $csrestricgroupproduct,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mconnect\Csproduct\Helper\Data $helper
    ) {
    //  $this->context = $context;
        $this->mcsHelper = $mcsHelper;
        $this->_catalogProductHelper = $catalogProductHelper;
        $this->csmodel = $csproduct;
        $this->cscollection = $cscollection;
    //	$this->_objectManager = $objectManager;
        $this->_customerSession = $customerSession;
        $this->_csrestricgroupproductModel = $csrestricgroupproduct;
        $this->_productRepository = $productRepository;
        $this->_categoryFactory = $categoryFactory;
        $this->_productFactory = $productFactory;
        $this->_resource = $resource;
        $this->_helper = $helper;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        
        if (!$this->mcsHelper->checkLicenceKeyActivation()) {
            return;
        }
        if (!($this->_helper->getConfig('mconnect_csproduct/general/active'))) {
            return;
        }

        
        $hide_restrict_categ_on_front=$this->_helper->getConfig('mconnect_csproduct/general/hide_restrict_categ_on_front');
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $httpContext=$objectManager->get('Magento\Framework\App\Http\Context');
        $isLoggedIn=$httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        
        if ($hide_restrict_categ_on_front==1 && !$isLoggedIn) {
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $storeId =$storeManager->getStore()->getStoreId();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            
            $csCategoriesIds=$this->_helper->getConfig('mconnect_csproduct/general/cs_category', $storeScope, $storeId);
            $restrictedIds = explode(",", $csCategoriesIds);
            
                    
            $collection = $observer->getEvent()->getCategoryCollection();
            foreach ($collection as $key => $item) {
                if ($item->getAttributeSetId() == 3) {
                    if (in_array($item->getEntityId(), $restrictedIds)) {
                         $collection->removeItemByKey($key);
                    }
                }
            }
        }
        /*
		if($isLoggedIn){
			
			$catArray = array();
			$crnt_customer = $this->_customerSession->getCustomer();			
			$crnt_customer_id = $crnt_customer->getId();
			
			$cpcalaObj=$objectManager->get('\Mconnect\Csproduct\Observer\CatalogProductCollectionApplyLimitationsAfter');
			
			$getCustomerSpecificProductIds=$cpcalaObj->getCustomerSpecificProductIds();
			
		//	print_r($getCustomerSpecificProductIds);
			
			$collection = $this->_productFactory->create();
			$collection->addAttributeToFilter('entity_id', array('in'=>$getCustomerSpecificProductIds));

			$collection->getSelect()->reset('columns'); 

			$catalog_category_product = $this->_resource->getTableName('catalog_category_product');
			//$catalog_category_entity = $this->_resource->getTableName('catalog_category_entity');
			
			$collection->getSelect()->joinLeft(array('ccp'=>$catalog_category_product), 'ccp.product_id = e.entity_id', array('category_id'=>'ccp.category_id'))->group('category_id');
			
		
			foreach($collection->getData() as $_product){			
				$catArray[] =$_product['category_id'];
				//$catArray[] = $_product['parent_id'];
			}
		//	print_r($catArray);
			if($crnt_customer->getFollowproductsfromgroup()){
				$catProductArrayAllow  = $this->_helper->checkGroupCategoryExit();
				if(!empty($catProductArrayAllow)){
					$catArray=array_merge($catArray,$catProductArrayAllow);
				}
			}
			
			$catArray=array_unique($catArray);
			
			$csCategoriesIds=$this->_helper->getConfig('mconnect_csproduct/general/cs_category');
			$restrictedCatIds = explode(",",$csCategoriesIds);
			
			$displayRestrictedCatIds = array_diff($restrictedCatIds, $catArray);				
			
			$collection = $observer->getEvent()->getCategoryCollection();
			foreach ($collection as $key => $item)
			{						
				if ($item->getAttributeSetId() == 3) {
					if(in_array($item->getEntityId() ,$displayRestrictedCatIds))
					{
						 $collection->removeItemByKey($key);
					}
				}
			}	
			
		}
		*/
    }
}

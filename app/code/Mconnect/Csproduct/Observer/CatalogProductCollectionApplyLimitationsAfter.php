<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Action\Action;

class CatalogProductCollectionApplyLimitationsAfter implements ObserverInterface
{
        
    protected $_request;
        
    protected $_customerSession;
        
    protected $_csproductModel;
        
    protected $_cscollection;
        
    protected $_objectManager = null;
        
    protected $_helper;
        
    protected $_registry;
        
    protected $_csrestricgroupproductModel;
        
    protected $_csgroupproductModel;
        
    protected $_productCollection;
        
    protected $_scopeConfig;
        
    protected $_response;
            
                
        /**
         * @var \Magento\Catalog\Model\ProductFactory
         */
    protected $_productFactory;
        
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Mconnect\Csproduct\Model\CsproductFactory $csproduct,
        \Mconnect\Csproduct\Model\CsgroupproductFactory $csgroupproduct,
        \Mconnect\Csproduct\Model\CsrestricgroupproductFactory $csrestricgroupproduct,
        \Mconnect\Csproduct\Model\ResourceModel\Csproduct\CollectionFactory $cscollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Framework\App\Response\Http $response,
        \Mconnect\Csproduct\Helper\Data $helper,
        \Mconnect\Csproduct\Helper\McsHelper $mcsHelper
    ) {
            
        $this->context = $context;
        $this->resultFactory = $resultFactory;
        $this->_urlInterface = $context->getUrlBuilder();
        $this->_request =$context->getRequest();
        $this->mcsHelper = $mcsHelper;
        $this->_customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        $this->_csproductModel = $csproduct;
        $this->_csgroupproductModel = $csgroupproduct;
        $this->_csrestricgroupproductModel = $csrestricgroupproduct;
        $this->_cscollection = $cscollection;
        $this->_productFactory = $productFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_response = $response;
        $this->_helper = $helper;
    }
   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
            
            
        if (!$this->mcsHelper->checkLicenceKeyActivation()) {
            return;
        }
            
        if (!$this->_helper->getConfig('mconnect_csproduct/general/active')) {
            $this->_productCollection = $observer->getEvent()->getCollection();
            $observer->getEvent()->setCollection($this->_productCollection);
            return '';
        }
            
        if (!$this->_helper->isLoggedIn()) {
            return '';
        }
            
        $collection = $observer->getEvent()->getCollection();
                         
        $category = $this->_registry->registry('current_category');
        $currentCategoryId =($category) ? $category->getId():'';
            
            
                        
        $product = $this->_registry->registry('current_product');
        $currentProductId = ($product) ? $product->getId():'';
            
        $crnt_customer = $this->_customerSession->getCustomer();
        $storeId=$crnt_customer->getStoreId();
        $crnt_customer_id = $crnt_customer->getId();
            
            
        if ($crnt_customer_id && $currentProductId) {
            //Need to development,Thia condition For product detail page
            $restricProductIds = $this->_getRestrictedProductsForCurrentCustomer($crnt_customer);
            
            if (in_array($currentProductId, $restricProductIds)) {
                $no_route=$this->_scopeConfig->getValue('web/default/cms_no_route', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $redirectUrl=$this->_urlInterface->getUrl($no_route);
                $this->response->setRedirect($redirectUrl);
            }
        }
            
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $CustomCategoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category', $storeScope, $storeId);
        $CustomCategoriesIdsArray = explode(',', $CustomCategoriesIds);
            
            
            
        /*-------------------------cat allow----------------------------------*/
        if ($crnt_customer->getFollowproductsfromgroup()) {
            $catProductArrayAllow  = $this->_helper->checkGroupCategoryExit($currentCategoryId);
            if (!empty($catProductArrayAllow)) {
                $CustomCategoriesIdsArray = array_diff($CustomCategoriesIdsArray, $catProductArrayAllow);
            }
        }
        /*-----------------------------------------------------------*/
            
        if (in_array($currentCategoryId, $CustomCategoriesIdsArray) && $this->_request->getControllerName()!='result') {
            if ($this->getCustomerSpecificProductIds()) {
                $collection->addAttributeToFilter('entity_id', ['in'=>$this->getCustomerSpecificProductIds()]);
                                
                $this->_productCollection = $collection;
            } else {
                if (!$this->_helper->getConfig('mconnect_csproduct/general/login_customer')) {
                    $this->_productCollection = $collection;
                } else {
                    $collection->addAttributeToFilter('entity_id', ['in'=>$this->getCustomerSpecificProductIds()]);
                    $this->_productCollection = $collection;
                }
            }
            $observer->getEvent()->setCollection($this->_productCollection);
            return;
        }
            
        if ($this->_request->getControllerName() == 'result') {
            $categoryProducts = $this->_getCategoryProducts();
            $customerProducts = $this->getCustomerSpecificProductIds();
            $removeProductsFormResults = array_diff($categoryProducts, $customerProducts);
            if ($removeProductsFormResults) {
                $collection->addAttributeToFilter('entity_id', ['nin'=>$removeProductsFormResults]);
            }
                
            $this->_productCollection = $collection;
            $observer->getEvent()->setCollection($this->_productCollection);
            return;
        }
            
        return;
    }
        
    private function _getCategoryProducts()
    {
            
        $crnt_customer = $this->_customerSession->getCustomer();
        $storeId=$crnt_customer->getStoreId();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            
        $CustomCategoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category', $storeScope, $storeId);
        $CustomCategoriesIdsArray = explode(',', $CustomCategoriesIds);
            
        /*-------------------------cat allow----------------------------------*/
            
        if ($crnt_customer->getFollowproductsfromgroup()) {
            $catProductArrayAllow  = $this->_helper->checkGroupCategoryExit();
            if (!empty($catProductArrayAllow)) {
                $CustomCategoriesIdsArray = array_diff($CustomCategoriesIdsArray, $catProductArrayAllow);
            }
        }
        /*-----------------------------------------------------------*/
            
                    
        if (!empty($CustomCategoriesIdsArray)) {
            $collection = $this->_productFactory->create();
            $collection->addCategoriesFilter(['in' => $CustomCategoriesIdsArray]);
                
    //$collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
    //$collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                
            $idsArray = [];
            foreach ($collection as $child) {
                     $idsArray[] = $child->getEntityId();
            }
            return $idsArray;
        }
        return [];
    }
        
    public function getCustomerSpecificProductIds()
    {
        
        if ($this->_helper->getConfig('mconnect_csproduct/general/active')) {
            if ($this->_customerSession->isLoggedIn()) {
                $crnt_customer = $this->_customerSession->getCustomer();
                $storeId=$crnt_customer->getStoreId();
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    
                $CustomCategoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category', $storeScope, $storeId);
                    
                $CustomCategoriesIds = explode(',', $CustomCategoriesIds);
                                
                $crnt_customer_id = $crnt_customer->getId();
                $restricProductIds = $this->_getRestrictedProductsForCurrentCustomer($crnt_customer);
                $productsIds =  $this->_getAllowedProductsForCustomer($crnt_customer);
                $returnProductsIds = $this->getCustomerProducts($crnt_customer, $restricProductIds, $productsIds);
                    
                return $returnProductsIds;
            } else {
                return $this->getNotLoggedInCustomerGroupProducts();
            }
        }
        return '';
    }
        
    public function _getRestrictedProductsForCurrentCustomer($crnt_customer)
    {
            
        $restricProductIds = [];
        $restricCollection = $this->_csrestricgroupproductModel->create()->getCollection();
        $restricCollection->addFieldToFilter('group_id', $crnt_customer->getGroupId());
        $restricCollection->addFieldToFilter('customer_id', $crnt_customer->getId());
            
        $restricProductIds=$restricCollection->getColumnValues('product_id');
        
        return $restricProductIds;
    }
        
    public function _getAllowedProductsForCustomer($crnt_customer)
    {
            
        $productsIds = [];
            
        $collectionGroup = $this->_csgroupproductModel->create()->getCollection()->addFieldToFilter('group_id', $crnt_customer->getGroupId());
            
        $collectionCustomer = $this->_csproductModel->create()->getCollection()->addFieldToFilter('customer_id', $crnt_customer->getId());
            
        $collectionGroupId = $collectionGroup->getColumnValues('product_id');
        $collectionCustomerId = $collectionCustomer->getColumnValues('product_id');
            
        $productsIds=array_merge($collectionGroupId, $collectionCustomerId);
        return $productsIds;
    }
        
    public function getCustomerProducts($crnt_customer, $restricProductIds, $productsIds)
    {
         
        $returnProductsIds = [];
        $collectionCustomer = $this->_csproductModel->create()->getCollection()->addFieldToFilter('customer_id', $crnt_customer->getId());
            
        $Followproductsfromgroup = $crnt_customer->getFollowproductsfromgroup();
            
        if ($Followproductsfromgroup) {
            $returnProductsIds = array_unique(array_diff($productsIds, $restricProductIds));
        } else {
            $collectionCustomer = $this->_csproductModel->create()->getCollection()->addFieldToFilter('customer_id', $crnt_customer->getId());
                
            $returnProductsIds = $collectionCustomer->getColumnValues('product_id');
        }
            
        return $returnProductsIds;
    }
        
    public function getNotLoggedInCustomerGroupProducts()
    {
            
        $productsIds = [];
        $CustomCategoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
        $CustomCategoriesIds = explode(',', $CustomCategoriesIds);
        $collectionGroup = $this->_csgroupproductModel->create()
                                ->getCollection()
                                ->addFieldToFilter('group_id', 0);
        
        $productsIds = $collectionGroup->getColumnValues('product_id');
        
        $crnt_categoryid =[];
        if (!empty($this->_registry->registry('current_category'))) {
            $crnt_categoryid = $this->_registry->registry('current_category')->getId();
        }
        
        if (in_array($crnt_categoryid, $CustomCategoriesIds)) {
            return $productsIds;
        }
        return [];
    }
}

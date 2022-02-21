<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class CustomerSaveBefore implements ObserverInterface
{

    protected $_catalogProductHelper;
        
    protected $_scopeConfigObject;
        
    protected $_request;
        
    protected $_customerSession;
        
    protected $_jsHelper;
        
    protected $_cscollection;
        
    protected $_csModel;
        
    protected $_csrgcollection;
        
    protected $_csrgModel;
            
    protected $_productFactory;
        
    protected $_helper;
        
    protected $_storeManager;
        
    protected $_csPriceModel;
        
    protected $_csPriceCollection;
        
    
    public function __construct(
        \Magento\Catalog\Helper\Product $catalogProductHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Helper\Js $jsHelper,
        \Mconnect\Csproduct\Model\ResourceModel\Csproduct\CollectionFactory $cscollection,
        \Mconnect\Csproduct\Model\CsproductFactory $csModelProduct,
        \Mconnect\Csproduct\Model\ResourceModel\Csrestricgroupproduct\CollectionFactory $csrgcollection,
        \Mconnect\Csproduct\Model\CsrestricgroupproductFactory $csrgModelProduct,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Mconnect\Csproduct\Model\CspriceFactory $csPriceModel,
        \Mconnect\Csproduct\Model\ResourceModel\Csprice\CollectionFactory $cspricecollection,
        \Mconnect\Csproduct\Helper\Data $helper,
        StoreManagerInterface $storeManager
    ) {
            
        $this->_catalogProductHelper = $catalogProductHelper;
        $this->_scopeConfigObject = $scopeConfigObject;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_jsHelper = $jsHelper;
        $this->_cscollection = $cscollection;
        $this->_csModel = $csModelProduct;
        $this->_csrgcollection = $csrgcollection;
        $this->_csrgModel = $csrgModelProduct;
        $this->_productFactory = $productFactory;
        $this->_csPriceModel = $csPriceModel;
        $this->_csPriceCollection = $cspricecollection;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
    }
    
    public function checkPriceProductExit($customerId, $productId)
    {
        
        $collection = $this->_csPriceCollection->create();
        $collection ->addFieldToFilter('customer_id', $customerId);
        $collection ->addFieldToFilter('product_id', $productId);
        return $collection->getFirstItem();
    }
   
   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
     
        
        $deletePriceProductId=[];
        $productIds=[];
        $followproductsfromgroup='';
         
        $postData = $this->_request->getPostValue();
            
        if (isset($postData['customer']['entity_id']) && isset($postData['setcsproduct'])) {
                    $customerId = $postData['customer']['entity_id'];
                    $groupId = $postData['customer']['group_id'];
                    
                    
                    //$followproductsfromgroup = $postData['customer']['followproductsfromgroup'];
            if (!empty($postData['followproductsfromgroupSelected'])) {
                $followproductsfromgroup =$postData['followproductsfromgroupSelected'];
                    
                $customer = $observer->getCustomer();
                $customer->setFollowproductsfromgroup($postData['followproductsfromgroupSelected']);
            } else {
                $followproductsfromgroup =$postData['followproductsfromgroupSelected'];
                    
                $customer = $observer->getCustomer();
                $customer->setFollowproductsfromgroup($postData['followproductsfromgroupSelected']);
            }
                    
                    
                 
                            
            //-----------------==---------customer products-------------------------//
                
                //if($postData['customerproduct_ids']){
            if (array_key_exists('customerproduct_ids', $postData)) {
                if (!empty($postData['customerproduct_ids'])) {
                    $productIds = $this->_jsHelper->decodeGridSerializedInput($postData['customerproduct_ids']);
                    $selectedProducts = $productIds;

                    $csCollection = $this->_cscollection->create();
                    $csCollection->addFieldToFilter('customer_id', $customerId);

                    $availableProducts = $csCollection->getColumnValues('product_id');
                    
                    $insertProducts = $deleteProducts = [];
                    
                    $mergeProducts = array_unique(array_merge($selectedProducts, $availableProducts));
                    
                    $insertProducts = array_diff($mergeProducts, $availableProducts);
                    
                    $deleteProducts = array_diff($availableProducts, $selectedProducts);
                    
                                        
                    if (!empty($insertProducts)) {
                        foreach ($insertProducts as $child) {
                            $csproductModel = $this->_csModel->create();
                            $csproductModel->load(null);
                            $csproductModel->setProductId($child);
                            $csproductModel->setCustomerId($customerId);
                            $csproductModel->save();
                        }
                    } else {
                        if ($deleteProducts) {
                            foreach ($deleteProducts as $deleteChild) {
                                $csCollection = $this->_cscollection->create();
                                $csCollection->addFieldToFilter('customer_id', $customerId);
                                $deleteCollection = $csCollection->addFieldToFilter('product_id', $deleteChild);
                                foreach ($deleteCollection as $deleteData) {
                                    $deleteData->delete();
                                }
                                $deletePriceProductId[]=$deleteChild;
                            }
                            $deleteProducts='';
                        }
                    }
                    
                    if (!empty($deleteProducts)) {
                        foreach ($deleteProducts as $deleteChild) {
                            $csCollection = $this->_cscollection->create();
                            $csCollection->addFieldToFilter('customer_id', $customerId);
                            $deleteCollection = $csCollection->addFieldToFilter('product_id', $deleteChild);
                            foreach ($deleteCollection as $deleteData) {
                                $deleteData->delete();
                            }
                            $deletePriceProductId[]=$deleteChild;
                        }
                    }
                } else {
                    $csCollection = $this->_cscollection->create();
                    $csCollection->addFieldToFilter('customer_id', $customerId);
                    $availableProducts = $csCollection->getColumnValues('product_id');
                        
                    foreach ($availableProducts as $availableChild) {
                        $csCollection = $this->_cscollection->create();
                        $csCollection->addFieldToFilter('customer_id', $customerId);
                        $deleteCollection = $csCollection->addFieldToFilter('product_id', $availableChild);
                        foreach ($deleteCollection as $deleteData) {
                            $deleteData->delete();
                        }
                        $deletePriceProductId[]=$availableChild;
                    }
                }
            }
                
            //-------------------==-------customer Group products-------------------------//
            
            if ($followproductsfromgroup==1) {
                if (array_key_exists('customergroupproduct_ids', $postData)) {
                    if ($postData['customergroupproduct_ids']) {
                        $productGroupIds = $this->_jsHelper->decodeGridSerializedInput($postData['customergroupproduct_ids']);
                    
                        $selectedProducts = $productGroupIds;
                
                                    
                        $categoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
                        $categoriesIdsArray = explode(',', $categoriesIds);
               
                        $collection = $this->_productFactory->create();
                        
                        $collection->addCategoriesFilter(['in' => $categoriesIdsArray]);
                        
                        $collection->addAttributeToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE]);
                        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        
                        $availableProducts = [];
                        
                        foreach ($collection as $product) {
                            $availableProducts[] = $product->getEntityId();
                        }

                        
                        $restricProducts = $deleteProducts = [];
                        $restricProducts = array_diff($availableProducts, $selectedProducts);
                        
                        
                        $csrgCollection = $this->_csrgcollection->create();
                        $csrgCollection->addFieldToFilter('customer_id', $customerId);
                        $csrgCollection->addFieldToFilter('group_id', $groupId);
                        $availableProductsOfCSRG = $csrgCollection->getColumnValues('product_id');
                                        
                        $deleteProducts = array_unique(array_intersect($availableProductsOfCSRG, $selectedProducts));
                        
                                            
                        if (!empty($restricProducts)) {
                            foreach ($restricProducts as $child) {
                                $csrgProductModel = $this->_csrgModel->create();
                                $csrgProductModel->load(null);
                                $csrgProductModel->setProductId($child);
                                $csrgProductModel->setGroupId($groupId);
                                $csrgProductModel->setCustomerId($customerId);
                                $csrgProductModel->save();
                            }
                        }
                        
                        if (!empty($deleteProducts)) {
                            foreach ($deleteProducts as $deleteChild) {
                                $csrgCollection = $this->_csrgcollection->create();
                                $csrgCollection->addFieldToFilter('customer_id', $customerId);
                                $csrgCollection->addFieldToFilter('group_id', $groupId);
                                $deleteCollection = $csrgCollection->addFieldToFilter('product_id', $deleteChild);
                                foreach ($deleteCollection as $deleteData) {
                                    $deleteData->delete();
                                }
                            }
                        }
                    } else {
                        /*------add all data ------*/
                        
                        $categoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
                        $categoriesIdsArray = explode(',', $categoriesIds);
               
                        $collection = $this->_productFactory->create();
                        
                        $collection->addCategoriesFilter(['in' => $categoriesIdsArray]);
                        
                        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        
                        $availableProducts = [];
                        
                        foreach ($collection as $product) {
                            $availableProducts[] = $product->getEntityId();
                        }
                        
                        if (!empty($availableProducts)) {
                            foreach ($availableProducts as $child) {
                                $csrgProductModel = $this->_csrgModel->create();
                                $csrgProductModel->load(null);
                                $csrgProductModel->setProductId($child);
                                $csrgProductModel->setGroupId($groupId);
                                $csrgProductModel->setCustomerId($customerId);
                                $csrgProductModel->save();
                            }
                        }
                    }
                }
            }
            
            //---------------------------products price-------------------------//
            
            if (array_key_exists('customer_product', $postData)) {
                if ($postData['customer_product']) {
                    foreach ($deletePriceProductId as $deleteChild) {
                            $priceColls = $this->_csPriceCollection->create();
                            $priceColls->addFieldToFilter('customer_id', $customerId);
                            $deleteCollection = $priceColls->addFieldToFilter('product_id', $deleteChild);
                        foreach ($deleteCollection as $deleteData) {
                            $deleteData->delete();
                        }
                    }
                    
                
                    
                    $custompriceproductsBy = $postData['customer_product'];
                    $custompriceproductsBy = array_filter($custompriceproductsBy, function ($value) {
                        return $value !== '';
                    });
                    
                    foreach ($custompriceproductsBy as $productId => $price) {
                        if (in_array($productId, $productIds)) {
                            $cspricedata = $this->checkPriceProductExit($customerId, $productId);
                                
                            if ($cspricedata->getData()) {
                                $modelId = $cspricedata->getId();
                                    
                                $csPriceModel = $this->_csPriceModel->create();
                                $csPriceModel->load($modelId);
                                $csPriceModel->setProductId($productId);
                                $csPriceModel->setCustomerId($customerId);
                                $csPriceModel->setCsPrice($price);
                                $csPriceModel->save();
                                //$model->unsetData();
                            } else {
                                $csPriceModel = $this->_csPriceModel->create();
                                $csPriceModel->load(null);
                                $csPriceModel->setProductId($productId);
                                $csPriceModel->setCustomerId($customerId);
                                $csPriceModel->setCsPrice($price);
                                $csPriceModel->save();
                            }
                        }
                    }
                }
            }
        }
    }
}

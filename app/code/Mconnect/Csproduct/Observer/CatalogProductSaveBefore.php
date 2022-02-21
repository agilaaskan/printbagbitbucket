<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Mconnect\Csproduct\Model\CsproductFactory;
use Mconnect\Csproduct\Model\ResourceModel\Csproduct\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CatalogProductSaveBefore implements ObserverInterface
{

    
    protected $scopeConfigObject;
    
    protected $_storeManager;
        
    protected $_request;
        
    protected $csmodel;
        
    protected $cscollection;
        
    protected $_csgCollection;
        
    protected $csgroup;
        
        
        
    
    public function __construct(
        \Magento\Catalog\Helper\Product $catalogProductHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Framework\App\RequestInterface $request,
        CsproductFactory $csproduct,
        CollectionFactory $cscollection,
        \Mconnect\Csproduct\Model\CsgroupproductFactory $csgroup,
        \Mconnect\Csproduct\Model\ResourceModel\Csgroupproduct\CollectionFactory $csgcollection,
        StoreManagerInterface $storeManager
    ) {
        $this->catalogProductHelper = $catalogProductHelper;
        $this->scopeConfigObject = $scopeConfigObject;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->csmodel = $csproduct;
        $this->csgroup = $csgroup;
        $this->cscollection = $cscollection;
        $this->_csgCollection = $csgcollection;
    }
    
   
    public function execute(Observer $observer)
    {
        
            
        $product = $observer->getProduct();
        $productId = $product->getId();
        $postData = $this->_request->getPostValue();
        
        /*-----------------------------group selection-------------------------*/
        
        if (array_key_exists('product', $postData)) {
            if (array_key_exists('cs_group_multiselect', $postData["product"])) {
                $productGroup = $postData['product']['cs_group_multiselect'];
                if ($postData['product']['cs_group_multiselect']) {
                        $selectedGroup = $postData['product']['cs_group_multiselect'];
                } else {
                    $selectedGroup = [];
                }
                            

                $csgCollection = $this->_csgCollection->create();
                $csgCollection->addFieldToFilter('product_id', $productId);
                    
                if ($csgCollection->getColumnValues('group_id')) {
                    $availableGroup = $csgCollection->getColumnValues('group_id');
                } else {
                    $availableGroup = [];
                }
                
                
                $insertGroup = $deleteGroup = [];
                $mergeGroup = array_unique(array_merge($selectedGroup, $availableGroup));
                $insertGroup = array_diff($mergeGroup, $availableGroup);
                $deleteGroup = array_diff($availableGroup, $selectedGroup);

                if (!empty($insertGroup)) {
                    foreach ($insertGroup as $child) {
                        $csGroupModel = $this->csgroup->create();
                        $csGroupModel->load(null);
                        $csGroupModel->setProductId($productId);
                        $csGroupModel->setGroupId($child);
                        $csGroupModel->save();
                    }
                } else {
                    if ($deleteGroup) {
                        foreach ($deleteGroup as $deleteChild) {
                            $csgCollection = $this->_csgCollection->create();
                            $csgCollection->addFieldToFilter('product_id', $productId);
                            $deleteCollection = $csgCollection->addFieldToFilter('group_id', $deleteChild);
                            foreach ($deleteCollection as $deleteData) {
                                $deleteData->delete();
                            }
                        }
                        $deleteGroup='';
                    }
                }
            
                if ($deleteGroup) {
                    foreach ($deleteGroup as $deleteChild) {
                        $csgCollection = $this->_csgCollection->create();
                        $csgCollection->addFieldToFilter('product_id', $productId);
                        $deleteCollection = $csgCollection->addFieldToFilter('group_id', $deleteChild);
                        foreach ($deleteCollection as $deleteData) {
                            $deleteData->delete();
                        }
                    }
                }
            } else {
                $csgCollection = $this->_csgCollection->create();
                $csgCollection->addFieldToFilter('product_id', $productId);
                $availableGroup = $csgCollection->getColumnValues('group_id');
                foreach ($availableGroup as $availableGroupChild) {
                    $csgCollection = $this->_csgCollection->create();
                    $csgCollection->addFieldToFilter('product_id', $productId);
                    $deleteCollection = $csgCollection->addFieldToFilter('group_id', $availableGroupChild);
                    foreach ($deleteCollection as $deleteData) {
                        $deleteData->delete();
                    }
                }
            }
        }
        
        
        /*-----------------------------product selection-------------------------*/
        
        
        if (array_key_exists('links', $postData)) {
            if (array_key_exists('customertab', $postData["links"])) {
                $productCustomer = $postData["links"]["customertab"];
                $selectedCustomer = array_column($productCustomer, 'id');
                
                $csCollection = $this->cscollection->create();
                $csCollection->addFieldToFilter('product_id', $productId);
                $availableCustomers = $csCollection->getColumnValues('customer_id');
                $insertCustomer = $deleteCustomer = [];
                $mergeCustomer = array_unique(array_merge($selectedCustomer, $availableCustomers));
                $insertCustomer = array_diff($mergeCustomer, $availableCustomers);
                $deleteCustomer = array_diff($availableCustomers, $selectedCustomer);
                
                if (!empty($insertCustomer)) {
                    foreach ($insertCustomer as $child) {
                        $csproductModel = $this->csmodel->create();
                        $csproductModel->load(null);
                        $csproductModel->setProductId($productId);
                        $csproductModel->setCustomerId($child);
                        $csproductModel->save();
                    }
                } else {
                    if ($deleteCustomer) {
                        foreach ($deleteCustomer as $deleteChild) {
                            $csCollection = $this->cscollection->create();
                            $csCollection->addFieldToFilter('product_id', $productId);
                            $deleteCollection = $csCollection->addFieldToFilter('customer_id', $deleteChild);
                            foreach ($deleteCollection as $deleteData) {
                                $deleteData->delete();
                            }
                        }
                    }
                }
            } else {
                $csCollection = $this->cscollection->create();
                $csCollection->addFieldToFilter('product_id', $productId);
                $availableCustomers = $csCollection->getColumnValues('customer_id');
                foreach ($availableCustomers as $availableCustomersChild) {
                    $csCollection = $this->cscollection->create();
                    $csCollection->addFieldToFilter('product_id', $productId);
                    $deleteCollection = $csCollection->addFieldToFilter('customer_id', $availableCustomersChild);
                    foreach ($deleteCollection as $deleteData) {
                        $deleteData->delete();
                    }
                }
            }
        } else {
                $csCollection = $this->cscollection->create();
                $csCollection->addFieldToFilter('product_id', $productId);
                $availableCustomers = $csCollection->getColumnValues('customer_id');
            foreach ($availableCustomers as $availableCustomersChild) {
                $csCollection = $this->cscollection->create();
                $csCollection->addFieldToFilter('product_id', $productId);
                $deleteCollection = $csCollection->addFieldToFilter('customer_id', $availableCustomersChild);
                foreach ($deleteCollection as $deleteData) {
                    $deleteData->delete();
                }
            }
        }
    }
}

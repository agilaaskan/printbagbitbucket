<?php
namespace Mconnect\Csproduct\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
        const GROUP_PRICE = "GP";
        const CUSTOMER_GROUP_PRICE = "CGP";
        
    protected $scopeConfig;
        
    protected $_customerSession;
        
    protected $_csgroupproductModel;
        
    protected $_csgpCollection;
        
    protected $_csPriceModel;
        
    protected $_csPriceCollection;
        
    protected $_csgcModel;
        
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        \Mconnect\Csproduct\Model\CsgroupproductFactory $csgroupproduct,
        \Mconnect\Csproduct\Model\ResourceModel\Csgroupproduct\CollectionFactory $csgpcollection,
        \Mconnect\Csproduct\Model\CspriceFactory $csPriceModel,
        \Mconnect\Csproduct\Model\ResourceModel\Csprice\CollectionFactory $cspricecollection,
        \Mconnect\Csproduct\Model\CsgroupcategoryFactory $csgModelCategory,
        \Mconnect\Csproduct\Model\CsgroupwebsiteFactory $csGroupWebsiteModel,
        array $data = []
    ) {
        //$this->scopeConfig = $scopeConfigObject;
        $this->_customerSession = $customerSession;
        $this->_httpContext = $httpContext;
        $this->_csgroupproductModel = $csgroupproduct;
        $this->_csgpCollection = $csgpcollection;
        $this->_csPriceModel = $csPriceModel;
        $this->_csPriceCollection = $cspricecollection;
        $this->_csgcModel = $csgModelCategory;
        $this->_csGroupWebsiteModel = $csGroupWebsiteModel;
        parent::__construct($context);
    }
        
        
        /**
         * Functionality to get configuration values of plugin
         *
         * @param $configPath: System xml config path
         * @return value of requested configuration
         */
         
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
                
    public function isLoggedIn()
    {
            
        if ($this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            return true;
        }
        return false;
    }
        
        
    public function checkGroupCategoryExit()
    {
            
        if ($this->_customerSession->isLoggedIn()) {
            $groupId=$this->_customerSession->getCustomer()->getGroupId();
        } else {
            $groupId=0;
        }
            
        $csGroupCatCollection= $this->_csgcModel->create()->getCollection();
        $csGroupCatCollection->addFieldToFilter('group_id', $groupId);
        $csGroupCategoryArray =[];
        if ($csGroupCatCollection->getData()) {
            $categoryIds=$csGroupCatCollection->getColumnValues('category_ids');
            $csGroupCategoryArray = explode(',', $categoryIds[0]);
            return $csGroupCategoryArray;
        } else {
            return '';
        }
    }
        
            
    public function checkLogin()
    {
            
        if ($this->_customerSession->isLoggedIn()) {
            return true;
        }
        return false;
    }
        
    public function getCustomerSessionData()
    {

                        
        return $this->_customerSession->getCustomer();
        ;
    }
        
        
    
    public function getpricebyprdId($product_id)
    {
        $isLogin=$this->checkLogin();

        if ($isLogin) {
            $customer = $this->_customerSession->getCustomer();
            $customerId = $customer->getId();
            $groupId = $customer->getGroupId();
            $followProductsFromGroup=$customer->getFollowproductsfromgroup();
            $priorityIndex=$this->getConfig('mconnect_csproduct/general/priority_index');
            //$priorityIndex=   'CGP';  //(select only customer price)

                
            if ($followProductsFromGroup) {
                if ($priorityIndex == self::GROUP_PRICE) {
                    $csGroupPriceColls = $this->_csgroupproductModel->create()->getCollection()
                    ->addFieldToFilter('group_id', $groupId)
                    ->addFieldToFilter('product_id', $product_id)
                    ->getFirstItem();
                    if (!empty($csGroupPriceColls->getCsgpPrice())) {
                        return $csGroupPriceColls->getCsgpPrice();
                    } else {
                        $priceColls=$this->_csPriceModel->create()->getCollection()
                        ->addFieldTofilter('customer_id', $customerId)
                        ->addFieldTofilter('product_id', $product_id)
                        ->getFirstItem();
                        if (!empty($priceColls->getCsPrice())) {
                            return $priceColls->getCsPrice();
                        }
                    }
                } elseif ($priorityIndex == self::CUSTOMER_GROUP_PRICE) {
                        $priceColls=$this->_csPriceModel->create()->getCollection()
                        ->addFieldTofilter('customer_id', $customerId)
                        ->addFieldTofilter('product_id', $product_id)
                        ->getFirstItem();
                                                        
                    if (!empty($priceColls->getCsPrice())) {
                        return $priceColls->getCsPrice();
                    } else {
                        $csGroupPriceColls = $this->_csgroupproductModel->create()->getCollection()
                        ->addFieldToFilter('group_id', $groupId)
                        ->addFieldToFilter('product_id', $product_id)
                        ->getFirstItem();
                        if (!empty($csGroupPriceColls->getCsgpPrice())) {
                            return $csGroupPriceColls->getCsgpPrice();
                        }
                    }
                }
            } else {
                $priceColls=$this->_csPriceModel->create()->getCollection()
                ->addFieldTofilter('customer_id', $customerId)
                ->addFieldTofilter('product_id', $product_id)
                ->getFirstItem();
                if (!empty($priceColls->getCsPrice())) {
                    return $priceColls->getCsPrice();
                }
            }
        }
    }
    
    public function getCustomerLabel()
    {
        $label = "Customer Products";
        if ($this->getConfig('mconnect_csproduct/cs_customer/customer_products_link_label')) {
             $label = $this->getConfig('mconnect_csproduct/cs_customer/customer_products_link_label');
        }
        return $label;
    }
    
    public function getWebsiteIdByGroupId($groupId)
    {
        $websiteColls=$this->_csGroupWebsiteModel->create()->getCollection()
                            ->addFieldTofilter('group_id', $groupId)
                            ->getFirstItem();
        $wid=$websiteColls->getWebsiteId();
        if ($wid) {
            return $wid;
        } else {
            return '';
        }
    }

    public function getWebsiteNameByWebsiteId($websiteId)
    {
                    
        $wid=$websiteId;
      
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $websites = $storeManager->getWebsites();
        
        foreach ($websites as $website) {
            if ($website->getWebsiteId()==$wid) {
                return $website->getName();
            }
        }
    }
}

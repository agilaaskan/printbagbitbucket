<?php
/**
 * @author Mconnect Team
 * @package Mconnect_Csproduct
 */
namespace Mconnect\Csproduct\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Categorylist implements ArrayInterface
{
   
    protected $_categoryCollection;
    protected $_request;
    protected $_storeManager;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreRepository $storeRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_categoryCollection = $categoryCollection;
        $this->_request = $request;
        $this->_storeRepository = $storeRepository;
        $this->_storeManager = $storeManager;
    }
    
    /*
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_request->getParam('website', 0)) {
            $websiteId = (int) $this->_request->getParam('website', 0);
            $rootCatIdArray=[];
            $stores = $this->_storeRepository->getList();
            
            foreach ($stores as $store) {
                if ($store["website_id"]==$websiteId) {
                    //$websiteId = $store["website_id"];
                                        
                    $storeId = $store["store_id"];
                    $store = $this->_storeManager->getStore($storeId);
                    $storeId = $this->_storeManager->setCurrentStore($store->getCode());
                    $rootCatId = $this->_storeManager->getStore($storeId)->getRootCategoryId();
                    $rootCatIdArray[]['like']= "1/$rootCatId/%";
                }
            }                               $categories = $this->_categoryCollection->create()                                                            ->addAttributeToSelect('*')                                                 ->addFieldToFilter('path', $rootCatIdArray)                             ->addAttributeToSort('path', 'asc');
        } elseif ($this->_request->getParam('store', 0)) {
            $storeId = (int) $this->_request->getParam('store', 0);
            $store = $this->_storeManager->getStore($storeId);
            $storeId = $this->_storeManager->setCurrentStore($store->getCode());
            $rootCatId = $this->_storeManager->getStore($storeId)->getRootCategoryId();
            $categories = $this->_categoryCollection->create()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('path', ['like'=> "1/$rootCatId/%"])
                                ->addAttributeToSort('path', 'asc');
        } else {
            $categories = $this->_categoryCollection->create()
                               ->addAttributeToSelect('*')
                               ->addAttributeToSort('path', 'asc');
        }
        if (!empty($categories)) {
            $catagoryList = [];
            foreach ($categories as $category) {
                if ($category->getName() != 'Root Catalog') {
                    $dash = "";
                    for ($i=1; $i<$category->getLevel(); $i++) {
                        $dash .= "---";
                    }
                    $catagoryList[] = ['label' => $dash.$category->getName(),'value' => $category->getId()];
                }
            }
            return $catagoryList;
        } else {
            return [];
        }
    }
}

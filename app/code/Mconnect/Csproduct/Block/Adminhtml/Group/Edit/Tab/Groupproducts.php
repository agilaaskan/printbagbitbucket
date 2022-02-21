<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mconnect\Csproduct\Block\Adminhtml\Group\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Groupproducts extends \Magento\Backend\Block\Widget\Grid\Extended
{
        
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $_linkFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;
    
    protected $_csproduct;
    
    protected $_helper;
    
    protected $_categoryFactory;
    
    protected $_csgroupproduct;
    
    protected $_resource;
    
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\Product\LinkFactory $linkFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product\LinkFactory $linkFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Registry $coreRegistry,
        \Mconnect\Csproduct\Model\CsproductFactory $csproduct,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Mconnect\Csproduct\Helper\Data $helper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Mconnect\Csproduct\Model\ResourceModel\Csgroupproduct\CollectionFactory $csgroupproduct,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_linkFactory = $linkFactory;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        $this->_csproduct = $csproduct;
        $this->_objectManager = $objectManager;
        $this->_formFactory = $formFactory;
        $this->_helper = $helper;
        $this->_categoryFactory = $categoryFactory;
        $this->_csgroupproduct = $csgroupproduct;
        $this->_resource = $resource;
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
            
        parent::_construct();
        $this->setId('GroupproductlistGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        //$this->setDestElementId('edit_form');
        $this->setSaveParametersInSession(true);
        //$this->setFilterVisibility(true);
        $this->setUseAjax(true);
        $this->setDefaultFilter(['in_product' => 1]);
    }
    
    /**
     * add Column Filter To Collection
     */
    protected function _addColumnFilterToCollection($column)
    {
        
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }
    
    
    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
     //$collection = $this->productCollectionFactory->create();
     
        $group_id = $this->getRequest()->getParam('id');
        $websiteId=$this->_helper->getWebsiteIdByGroupId($group_id);
         
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
        $categoriesIds= $this->_scopeConfig->getValue('mconnect_csproduct/general/cs_category', $storeScope, $websiteId);
        $categoriesIdsArray = explode(',', $categoriesIds);
        
        if (empty($categoriesIdsArray) || empty($categoriesIds)) {
            $catIds = [];
            
            $storeRepository = $this->_objectManager->get('\Magento\Store\Model\StoreRepository');
            $stores = $storeRepository->getList();
            
            foreach ($stores as $store) {
                if ($store["website_id"]==$websiteId) {
                    $storeId = $store["store_id"];
                    
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $cIds= $this->_scopeConfig->getValue('mconnect_csproduct/general/cs_category', $storeScope, $storeId);
                    $cIdsArray = explode(',', $cIds);
                    
                    $catIds=array_merge($catIds, $cIdsArray);
                }
            }
                $categoriesIdsArray=array_unique($catIds);
        }
        
        
        
               
       
        //$categoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
       // $categoriesIdsArray = explode(',', $categoriesIds);
       
            
        $collection = $this->_productFactory->create();
       
        $collection->addCategoriesFilter(['in' => $categoriesIdsArray]);
       
        
    //$collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
    //$collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
    
    //$collection->addAttributeToSelect('*');
    
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToSelect('price_type');
        
        $group_id = $this->getRequest()->getParam('id');
        
        //$connection = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
        //$mconnect_csgroupproduct = $connection->getTableName('mconnect_csgroupproduct');
        
        $mconnect_csgroupproduct = $this->_resource->getTableName('mconnect_csgroupproduct');
        
         $collection->getSelect()->joinLeft([ $mconnect_csgroupproduct=> $mconnect_csgroupproduct], "$mconnect_csgroupproduct.product_id = e.entity_id  AND $mconnect_csgroupproduct.group_id = $group_id", ["$mconnect_csgroupproduct.csgp_price as customprice"]);
        
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
                        
        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
                'column_css_class' => 'col-select',
                'field_name' => 'selectedproducts[]'
            ]
        );
        
                
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        );
        
         $this->addColumn(
             'customprice',
             [
                'header' => __('Custom Price'),
                'type' => 'currency',
                'filter' => false,
                'index' => 'custom_price',
                'width' => '50px',
                'renderer'  => 'Mconnect\Csproduct\Block\Adminhtml\Group\Edit\Renderer\Groupproductprice',
             ]
         );
    
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/Groupproductgrid', ['_current' => true]);
    }
    
    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    protected function _getSelectedProducts()
    {
        $contact = $this->getContact();
        return  $contact;
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        $contact = $this->getContact();
        return $contact;
    }

    protected function getContact()
    {
        $products = [];
        
        $tmpProducts = $this->getRequest()->getParam('selected_products');
        if (isset($tmpProducts) && is_array($tmpProducts)) {
            return $tmpProducts;
        }
        
        $customerGroupId = $this->getRequest()->getParam('id');
        if (!empty($customerGroupId)) {
            $csgCollection = $this->_csgroupproduct->create();
            $csgCollection->addFieldToFilter('group_id', $customerGroupId);
            
            foreach ($csgCollection as $product) {
                $products[] = $product->getProductId();
            }
            return $products;
        }
    }
    
    public function isReadonly()
    {
        return false;
    }
}

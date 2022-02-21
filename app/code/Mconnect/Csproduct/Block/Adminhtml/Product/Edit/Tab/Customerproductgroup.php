<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mconnect\Csproduct\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Customerproductgroup extends \Magento\Backend\Block\Widget\Grid\Extended
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
    
    protected $_csrgroupproduct;
    
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
        \Mconnect\Csproduct\Model\ResourceModel\Csrestricgroupproduct\CollectionFactory $csrgroupproduct,
        \Mconnect\Csproduct\Model\ResourceModel\Csgroupproduct\CollectionFactory $csgroupproduct,
        \Magento\Framework\App\ResourceConnection $resource,
		\Magento\Customer\Model\Customer $customer,
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
        $this->_csrgroupproduct = $csrgroupproduct;
        $this->_csgroupproduct = $csgroupproduct;
        $this->_resource = $resource;
		$this->_customer = $customer;		
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
            
        parent::_construct();
        $this->setId('customergroupproductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        //$this->setDestElementId('edit_form');
        $this->setSaveParametersInSession(true);
        //$this->setFilterVisibility(true);
        $this->setUseAjax(true);
        
        //$this->setVarNameFilter('product_filter');
        
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_product' => 1]);
        }
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
       // $collection = $this->productCollectionFactory->create();

		$customerId = $this->getRequest()->getParam('id');	   
	    $customer =  $this->_customer->load($customerId);
		$storeId=$customer->getStoreId();
		
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$categoriesIds= $this->_scopeConfig->getValue('mconnect_csproduct/general/cs_category',$storeScope,$storeId);
        
        //$categoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
        $categoriesIdsArray = explode(',', $categoriesIds);
        
        
        
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $groupId=$customer->getGroupId();
        
        $groupcollection =$this->_csgroupproduct->create();
        $groupcollection->addFieldToFilter('group_id', $groupId);
        
        $groupproductIds = [];
        foreach ($groupcollection as $obj) {
            $groupproductIds[] = $obj->getProductId();
        }
            
        
        
       
        $collection = $this->_productFactory->create();
        
        $collection->addFieldToFilter('entity_id', ['in' => $groupproductIds]);
        
        $collection->addCategoriesFilter(['in' => $categoriesIdsArray]);
        
        
        //$collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        //$collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        
        
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        
        $customerId = $this->getRequest()->getParam('id');
         
    
    
        $mconnect_csgroupproduct = $this->_resource->getTableName('mconnect_csgroupproduct');
    
        $collection->getSelect()->joinLeft([$mconnect_csgroupproduct=> $mconnect_csgroupproduct], "$mconnect_csgroupproduct.product_id = e.entity_id  AND $mconnect_csgroupproduct.group_id = $groupId", ["$mconnect_csgroupproduct.csgp_price as customgroupprice"]);
        
        
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
            'customgroupprice',
            [
                'header' => __('Custom Group Price'),
                'index' => 'customgroupprice',
                'type' => 'currency',
                'filter' => false,
                'width' => '50px',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/customergroupproductgrid', ['_current' => true]);
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
        
        $tmpProducts = $this->getRequest()->getParam('csg_selected_products');
        if (isset($tmpProducts) && is_array($tmpProducts)) {
            return $tmpProducts;
        }
        
        $customerId = $this->getRequest()->getParam('id');
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        $groupId=$customer->getGroupId();
        
        $csrgCollection = $this->_csrgroupproduct->create();
        $csrgCollection->addFieldToFilter('customer_id', $customerId);
        $csrgCollection->addFieldToFilter('group_id', $groupId);
        
        $availableGroupProductIds = $csrgCollection->getColumnValues('product_id');
        
        $categoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
        $categoriesIdsArray = explode(',', $categoriesIds);
        
        $collection = $this->_productFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $categoriesIdsArray]);
        $collection->addAttributeToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE]);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        
        if ($availableGroupProductIds) {
            $collection->addAttributeToFilter('entity_id', ['nin' => $availableGroupProductIds]);
        }
        
        foreach ($collection as $product) {
            $products[] = $product->getEntityId();
        }
        return $products;
    }
    
    public function isReadonly(){
        return false;
    }
}

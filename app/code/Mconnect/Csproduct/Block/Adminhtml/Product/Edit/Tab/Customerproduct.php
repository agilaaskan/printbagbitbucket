<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mconnect\Csproduct\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Customerproduct extends \Magento\Backend\Block\Widget\Grid\Extended
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
    
    protected $_ScopeConfigInterface;
    
    protected $_csproduct;
    
    protected $_helper;
    
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
        $this->setId('productGrid');
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
        $categoriesIds= $this->_scopeConfig->getValue('mconnect_csproduct/general/cs_category', $storeScope, $storeId);
       
        //$categoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
        $categoriesIdsArray = explode(',', $categoriesIds);
       
        $collection = $this->_productFactory->create();
        
        $collection->addCategoriesFilter(['in' => $categoriesIdsArray]);
        
       // $collection->addAttributeToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE]);
       // $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        
        
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToSelect('price_type');
        
       // $customerId = $this->getRequest()->getParam('id');
        
        $mconnect_csprice = $this->_resource->getTableName('mconnect_csprice');
         
        $collection->getSelect()->joinLeft([$mconnect_csprice=> $mconnect_csprice], "$mconnect_csprice.product_id = e.entity_id AND $mconnect_csprice.customer_id = $customerId", ["$mconnect_csprice.cs_price as cs_price"]);
             
        $collection->addStoreFilter($storeId);
             
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    
    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        
        //$form = $this->_formFactory->create();
       // $form->setHtmlIdPrefix('_csproduct');
        
        //$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('CS Products')]);
        
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
        
        //$this->setForm($form);
        
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
            'cs_price',
            [
                'header' => __('Custom Price'),
                'index' => 'cs_price',
                //'type' => 'currency',
                'filter' => false,
                'width' => '50px',
                'renderer'  => 'Mconnect\Csproduct\Block\Adminhtml\Product\Edit\Renderer\Cprice',
            
            ]
        );
        
        
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/customerproductgrid', ['_current' => true]);
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
        
    //  print_r($contact->getProducts($contact));
        
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
        
        $tmpProducts = $this->getRequest()->getParam('cs_selected_products');
        if (isset($tmpProducts) && is_array($tmpProducts)) {
            return $tmpProducts;
        }
        
        
        $customerId = $this->getRequest()->getParam('id');
        if ($customerId) {
            $contacts = $this->_csproduct->create()->getCollection();
            $contacts->addFieldToSelect('*')->addFieldToFilter("customer_id", ['eq' =>$customerId ]);
        }
        
        foreach ($contacts as $product) {
            $products[] = $product->getProductId();
        }
        return $products;
    }
    
    public function isReadonly()
    {
        return false;
    }
}

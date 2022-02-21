<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mconnect\Csproduct\Block\Adminhtml\Order\Create\Search;

class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid
{
    /**
     * Sales config
     *
     * @var \Magento\Sales\Model\Config
     */
    protected $_salesConfig;
    /**
     * Session quote
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;
    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;
    /**
     * Product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    protected $_request;

    protected $_resource;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_catalogConfig = $catalogConfig;
        $this->_sessionQuote = $sessionQuote;
        $this->_salesConfig = $salesConfig;
        $this->_request = $request;
        $this->_resource = $resource;
        parent::__construct(
            $context,
            $backendHelper,
            $productFactory,
            $catalogConfig,
            $sessionQuote,
            $salesConfig,
            $data
        );
    }
    
    public function setCollection($collection)
    {
        $_customer = $this->_sessionQuote->getQuote()->getCustomer();
        $customerId = $_customer->getId();
        if (!$customerId) {
            return parent::setCollection($collection);
        }
        
        $action = $this->_request->getActionName();
        $route = $this->_request->getRouteName();
        if ($action == 'index' && $route == 'sales') {
            return parent::setCollection($collection);
        } else {
            return parent::_prepareCollection();
        }
        
        $attributes = $this->_catalogConfig->getProductAttributes();
        $collection = $this->_productFactory->create()->getCollection();
        $collection->setStore(
            $this->getStore()
        )->addAttributeToSelect(
            $attributes
        )->addAttributeToSelect(
            'sku'
        )->addStoreFilter()->addAttributeToFilter(
            'type_id',
            $this->_salesConfig->getAvailableProductTypes()
        )->addAttributeToSelect(
            'gift_message_available'
        );
        
        if (!empty($this->_sessionQuote->getQuote()->getCustomer())) {
            $_customer = $this->_sessionQuote->getQuote()->getCustomer();
            $customerId = $_customer->getId();
            $groupId=$_customer->getGroupId();
            $mconnect_csgroupproduct = $this->_resource->getTableName('mconnect_csgroupproduct');
            $mconnect_csprice = $this->_resource->getTableName('mconnect_csprice');
                     
            if (!empty($_customer->getCustomAttribute('followproductsfromgroup'))
                && $_customer->getCustomAttribute('followproductsfromgroup')->getValue()==1) {
                $collection->getSelect()
                ->joinLeft(
                    [$mconnect_csgroupproduct=> $mconnect_csgroupproduct],
                    "$mconnect_csgroupproduct.product_id = e.entity_id  
					AND $mconnect_csgroupproduct.group_id = $groupId",
                    ["$mconnect_csgroupproduct.csgp_price as cs_price"]
                );
            } else {
                $collection->getSelect()
                ->joinLeft(
                    [$mconnect_csprice=> $mconnect_csprice],
                    "$mconnect_csprice.product_id = e.entity_id 
					AND $mconnect_csprice.customer_id = $customerId",
                    ["$mconnect_csprice.cs_price as cs_price"]
                );
            }
        }
        
        parent::setCollection($collection);
    }
    protected function _prepareColumns()
    {
        $action = $this->_request->getActionName();
        $route = $this->_request->getRouteName();
        
        if ($action == 'index' && $route == 'sales') {
            return parent::_prepareColumns();
        }
        
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Product'),
                'renderer' => 'Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Product',
                'index' => 'name'
            ]
        );
        
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
        
        if (!empty($this->_sessionQuote->getQuote()->getCustomer())) {
            $this->addColumn(
                'cs_price',
                [
                    'header' => __('Custom Price'),
                    'index' => 'cs_price',
                    'type' => 'currency',
                    'column_css_class' => 'price',
                ]
            );
        }
        
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'column_css_class' => 'price',
                'type' => 'currency',
                'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
                'rate' => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
                'index' => 'price',
                'renderer' => 'Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Price'
            ]
        );

        $this->addColumn(
            'in_products',
            [
                'header' => __('Select'),
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'index' => 'entity_id',
                'sortable' => false,
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
            ]
        );

        $this->addColumn(
            'qty',
            [
                'filter' => false,
                'sortable' => false,
                'header' => __('Quantity'),
                'renderer' => 'Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid\Renderer\Qty',
                'name' => 'qty',
                'inline_css' => 'qty',
                'type' => 'input',
                'validate_class' => 'validate-number',
                'index' => 'qty'
            ]
        );
        
        $action = $this->_request->getActionName();
        $route = $this->_request->getRouteName();
        return parent::_prepareColumns();
    }
}

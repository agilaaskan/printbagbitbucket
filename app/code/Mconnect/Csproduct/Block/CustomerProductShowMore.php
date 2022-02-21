<?php

namespace Mconnect\Csproduct\Block;

use Magento\Framework\View\Element\Template;

//use Mconnectsolutions\Featuredproducts\Model\ResourceModel\Featuredproducts\CollectionFactory;

class CustomerProductShowMore extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    private $scopeConfig;
    private $featuredCollectionFactory;
    private $storeManager;
    
    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Mconnect\Csproduct\Model\CsgroupproductFactory $csgroupproduct,
        \Mconnect\Csproduct\Observer\CatalogProductCollectionApplyLimitationsAfter $cpcalaObj,
        \Mconnect\Csproduct\Helper\Data $helper,
        array $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->scopeConfig = $context->getScopeConfig();
        $this->urlInterface = $context->getUrlBuilder();
        $this->csgroupproductModel = $csgroupproduct;
        $this->cpcalaObj = $cpcalaObj;
        $this->helper = $helper;
        $this->httpContext = $httpContext;
        $this->_isScopePrivate = true;
        
        
        
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);

        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => [\Magento\Catalog\Model\Product::CACHE_TAG,
            ], ]);
    }
    
    public function getHelper()
    {
        return $this->helper;
    }
   
    public function createCollection()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest(
        )->getParam('limit') :4 ;
        
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
            
        $collection = $this->_addProductAttributesAndPrices($collection);
                
        if ($this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            $getCustomerSpecificProductIds=$this->cpcalaObj->getCustomerSpecificProductIds();
        } else {
            $collectionGroup = $this->csgroupproductModel->create()->getCollection()->addFieldToFilter('group_id', 0);
            $getCustomerSpecificProductIds = $collectionGroup->getColumnValues('product_id');
        }
            
        $collection->addAttributeToFilter('entity_id', ['in' => $getCustomerSpecificProductIds]);

        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }
    
    public function getShowMoreLink()
    {
        return $this->urlInterface->getUrl('csproduct/customer/products');
    }
    
    public function getCurrentStore()
    {
        return $this->storeManager->getStore(); // give the information about current store
    }
    
    public function getStoreConfigValues($inputData)
    {
        return  $this->scopeConfig->getValue($inputData);
    }
   
    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsCount()
    {
        if ($this->hasData('products_count')) {
            return $this->getData('products_count');
        }

        if (null === $this->getData('products_count')) {
            $this->setData('products_count', self::DEFAULT_PRODUCTS_COUNT);
        }

        return $this->getData('products_count');
    }
  
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Customer Products'));

        if ($this->createCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'reward.history.pager'
            )->setAvailableLimit([4=>4,8=>8,16=>16,20=>20])
                ->setShowPerPage(true)->setCollection(
                    $this->createCollection()
                );
            $this->setChild('pager', $pager);
            $this->createCollection()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}

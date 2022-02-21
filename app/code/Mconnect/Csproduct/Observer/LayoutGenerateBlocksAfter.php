<?php

namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Action\Action;

class LayoutGenerateBlocksAfter implements ObserverInterface
{

    
    protected $_storeManager;
        
    protected $_objectManager = null;
        
    protected $_helper;
        
    protected $_request;
        
    protected $_registry;
        
    protected $_scopeConfig;
        
    protected $_layoutFactory;
        
        
        
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Mconnect\Csproduct\Helper\Data $helper
    ) {
            
        $this->context = $context;
        $this->_request =$context->getRequest();
        $this->_registry = $registry;
        $this->_layoutFactory = $context->getPageConfig();
        $this->_helper = $helper;
    }
   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
                    
    
        if ($this->_helper->getConfig('mconnect_csproduct/general/no_index_no_follow_for_restricted_products')) {
            $fullActionName = $this->_request->getFullActionName();
            if ($fullActionName == 'catalog_category_view' || $fullActionName == 'catalog_product_view') {
                $category = $this->_registry->registry('current_category');
                $currentCategoryId =($category) ? $category->getId():'';
            
                                
                $CustomCategoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
                $CustomCategoriesIdsArray = explode(',', $CustomCategoriesIds);
                    
                    
                if (in_array($currentCategoryId, $CustomCategoriesIdsArray)) {
                    $this->_layoutFactory->setRobots('NOINDEX,NOFOLLOW');
                }
            }
        }
    }
}

<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class CatalogsearchResetSearchResult implements ObserverInterface
{

    protected $_catalogProductHelper;
        
    protected $_scopeConfigObject;
        
    protected $_request;
        
    protected $_customerSession;
        
    protected $_jsHelper;
        
    protected $_helper;
        
    protected $_logger;
        
    protected $_storeManager;
        
    
        
    
    public function __construct(
        \Magento\Catalog\Helper\Product $catalogProductHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Helper\Js $jsHelper,
        \Mconnect\Csproduct\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        StoreManagerInterface $storeManager
    ) {
            
        $this->_catalogProductHelper = $catalogProductHelper;
        $this->_scopeConfigObject = $scopeConfigObject;
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_jsHelper = $jsHelper;
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
    }
    
      
   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
           $collection = $observer->getEvent()->getCollection();
        
           $this->_logger->debug('testdsata');
    }
}

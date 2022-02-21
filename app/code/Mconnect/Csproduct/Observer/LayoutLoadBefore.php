<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class LayoutLoadBefore implements ObserverInterface
{

    protected $_catalogProductHelper;
        
    protected $_scopeConfigObject;
    
    protected $_storeManager;
        
    protected $_request;
        
    protected $_customerSession;
        
    protected $csmodel;
        
    protected $cscollection;
        
    
    public function __construct(
        \Magento\Catalog\Helper\Product $catalogProductHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Mconnect\Csproduct\Model\CsproductFactory $csproduct,
        \Mconnect\Csproduct\Model\ResourceModel\Csproduct\CollectionFactory $cscollection,
        \Magento\Customer\Model\Session $customerSession
    ) {
            
        $this->_catalogProductHelper = $catalogProductHelper;
        $this->_scopeConfigObject = $scopeConfigObject;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->csmodel = $csproduct;
        $this->cscollection = $cscollection;
        $this->_customerSession = $customerSession;
    }
    
   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    }
}

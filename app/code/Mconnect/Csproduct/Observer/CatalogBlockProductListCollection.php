<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class CatalogBlockProductListCollection implements ObserverInterface
{

    protected $_customerSession;
        
    protected $csmodel;
        
    protected $cscollection;
        
    protected $_objectManager = null;
        
    protected $_registry;
        
    protected $_helper;
        
    protected $_redirect;
        
    protected $_messageManager;
        
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mconnect\Csproduct\Model\CsproductFactory $csproduct,
        \Mconnect\Csproduct\Model\ResourceModel\Csproduct\CollectionFactory $cscollection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Mconnect\Csproduct\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Mconnect\Csproduct\Helper\McsHelper $mcsHelper
    ) {
            
        $this->context = $context;
        $this->_urlInterface = $context->getUrlBuilder();
        $this->mcsHelper = $mcsHelper;
        $this->csmodel = $csproduct;
        $this->cscollection = $cscollection;
        $this->_customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->_messageManager = $messageManager;
    }
    
   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        if (!$this->mcsHelper->checkLicenceKeyActivation()) {
            return;
        }
        if (!($this->_helper->getConfig('mconnect_csproduct/general/active'))) {
            return;
        }
        
        $category = $this->_registry->registry('current_category');
        $currentCategoryId =($category) ? $category->getId():'';
        
        $CustomCategoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
        $CustomCategoriesIds = explode(',', $CustomCategoriesIds);
    
        if (in_array($currentCategoryId, $CustomCategoriesIds)) {
            $login_customer=$this->_helper->getConfig('mconnect_csproduct/general/login_customer');
            
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $httpContext=$objectManager->get('Magento\Framework\App\Http\Context');
            $isLoggedIn=$httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
                        
            if (!$isLoggedIn && $login_customer==1) {
                $after_login=$this->_helper->getConfig('mconnect_csproduct/general/after_login');
                if ($after_login=='account_page') {
                    $redirectUrl=$this->_urlInterface->getUrl('customer/account/index');
                } else {
                    $redirectUrl=$this->_urlInterface->getCurrentUrl();
                }
                        
                $message=$this->_helper->getConfig('mconnect_csproduct/general/login_redirect_message');
                $this->_messageManager->addError($message);
            
                $this->_customerSession->setAfterAuthUrl($redirectUrl);
                $this->_customerSession->authenticate();
            }
        }
    }
}

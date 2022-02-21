<?php
namespace Mconnect\Csproduct\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;



use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;

class CatalogControllerProductView implements ObserverInterface
{

    protected $_catalogProductHelper;
            

        
    protected $_request;
        
    protected $_customerSession;
        
    protected $csmodel;
        
    protected $cscollection;
        
    protected $_objectManager = null;
        
    protected $_helper;
        
    protected $_redirect;
        
    protected $_registry;
        
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product $catalogProductHelper,
        \Mconnect\Csproduct\Model\CsproductFactory $csproduct,
        \Mconnect\Csproduct\Model\ResourceModel\Csproduct\CollectionFactory $cscollection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirect,
        \Mconnect\Csproduct\Helper\Data $helper,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Registry $registry,
        \Mconnect\Csproduct\Helper\McsHelper $mcsHelper
    ) {
            
        $this->context = $context;
        $this->_urlInterface = $context->getUrlBuilder();
        $this->mcsHelper = $mcsHelper;
        $this->_catalogProductHelper = $catalogProductHelper;
        $this->csmodel = $csproduct;
        $this->cscollection = $cscollection;
        $this->_customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->resultRedirectFactory=$resultRedirect;
        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_redirect = $redirect;
    }
        
   
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
            
        if (!$this->mcsHelper->checkLicenceKeyActivation()) {
            return;
        }
        if (!($this->_helper->getConfig('mconnect_csproduct/general/active'))) {
            return;
        }
            
        $login_customer=$this->_helper->getConfig('mconnect_csproduct/general/login_customer');
            
        $httpContext=$this->_objectManager->get('Magento\Framework\App\Http\Context');
        $isLoggedIn=$httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
            
        $category = $this->_registry->registry('current_category');
        $currentCategoryId =($category) ? $category->getId():'';
            
            
        $CustomCategoriesIds = $this->_helper->getConfig('mconnect_csproduct/general/cs_category');
        $CustomCategoriesIdsArray = explode(',', $CustomCategoriesIds);
            
        if (in_array($currentCategoryId, $CustomCategoriesIdsArray)) {
            if (!$isLoggedIn && $login_customer==1) {
                $after_login=$this->_helper->getConfig('mconnect_csproduct/general/after_login');
                if ($after_login=='account_page') {
                    $redirectUrl=$this->_urlInterface->getUrl('customer/account/index');
                } else {
                    $redirectUrl=$this->_urlInterface->getCurrentUrl();
                }
                    
                $this->_customerSession->setAfterAuthUrl($redirectUrl);
                    
                $this->_customerSession->authenticate();
            }
        }
    }
}

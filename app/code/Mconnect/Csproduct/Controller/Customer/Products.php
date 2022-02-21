<?php
namespace Mconnect\Csproduct\Controller\Customer;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Products extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * Index action
     *
     * @return $this
     */
    public function execute()
    {
        
        $this->_view->loadLayout();
        
         $pageFactory = $this->resultPageFactory->create();
 
        // Add title which is got by the configuration via backend
        $pageFactory->getConfig()->getTitle()->set('Customer Products');
        
        $this->_view->renderLayout();
    }
}

<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AttrBaseSplitcart
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\AttrBaseSplitcart\Controller\Cartover;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;

/**
 *  Webkul AttrBaseSplitcart Cartover Proceedtocheckout controller
 */
class Proceedtocheckout extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Webkul\AttrBaseSplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger
     */
    private $attrBaseLogger;

    /**
     * @param Context                         $context
     * @param Session                         $customerSession
     * @param FormKeyValidator                $formKeyValidator
     * @param \Webkul\AttrBaseSplitcart\Helper\Data $helper
     * @param \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        \Webkul\AttrBaseSplitcart\Helper\Data $helper,
        \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger
    ) {
        $this->session = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->helper = $helper;
        $this->attrBaseLogger = $attrBaseLogger;
        parent::__construct($context);
    }

    /**
     * To proceed to checkout a selected cart
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $isEnable = $this->helper->checkAttributesplitcartStatus();
        
        if ($this->getRequest()->isPost() && $isEnable) {
            try {
                $this->helper->addQuoteToVirtualCart();
                
                if (!$this->formKeyValidator->validate($this->getRequest())) {
                    
                    $this->messageManager->addError(__("Something Went Wrong !!!"));
                    return $this->resultRedirectFactory->create()->setPath(
                        'checkout/cart',
                        [
                            '_secure' => $this->getRequest()->isSecure()
                        ]
                    );
                }
                
                $fields = $this->getRequest()->getParams();
                if (isset($fields['attrsplitcart_value'])
                    && $fields['attrsplitcart_value']!==""
                    && $fields['attrsplitcart_attribute']
                    && $fields['attrsplitcart_attribute']!==""
                ) {
                    
                    $this->helper->getUpdatedQuote($fields);
                    
                    return $this->resultRedirectFactory->create()->setPath(
                        'checkout',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } catch (\Exception $e) {
                
                $this->attrBaseLogger->info("Controller_Proceedtocheckout execute : ".$e->getMessage());
                $this->messageManager->addError($e->getMessage());
                return $this->resultRedirectFactory->create()->setPath(
                    'checkout/cart',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            
            $this->messageManager->addError(__("Something Went Wrong !!!"));
            return $this->resultRedirectFactory->create()->setPath(
                'checkout/cart',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
    
}

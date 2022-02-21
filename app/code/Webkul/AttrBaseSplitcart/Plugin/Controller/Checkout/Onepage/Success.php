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
namespace Webkul\AttrBaseSplitcart\Plugin\Controller\Checkout\Onepage;

class Success
{
    
    /**
     * @var \Webkul\AttrBaseSplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger
     */
    private $attrBaseLogger;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    private $eventManager;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Checkout\Model\Session\SuccessValidator
     */
    private $successValidator;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * Construct.
     *
     * @param \Webkul\AttrBaseSplitcart\Helper\Data $helper
     * @param \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory,
     * @param \Magento\Framework\Event\Manager $eventManager,
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory,
     * @param \Magento\Checkout\Model\Session\SuccessValidator   $successValidator,
     * @param \Magento\Framework\Message\ManagerInterface $messageManager,
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Webkul\AttrBaseSplitcart\Helper\Data $helper,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session\SuccessValidator $successValidator,
        \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->helper = $helper;
        $this->attrBaseLogger = $attrBaseLogger;
        $this->quoteFactory = $quoteFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->eventManager = $eventManager;
        $this->messageManager = $messageManager;
        $this->successValidator = $successValidator;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Checkout\Block\Link $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Onepage\Success $subject,
        \Closure $proceed
    ) {
    
        try {
            $session = $subject->getOnepage()->getCheckout();
            $isEnable = $this->helper->checkAttributesplitcartStatus();
            $oldQuoteId = $this->helper->getAttributeVirtualCart();
            $oldQuoteItem = $this->quoteFactory->create()->load($oldQuoteId)->getItemsCount();
            $currentQuoteId = $this->checkoutSession->getQuote()->getId();
            $currentQuoteItem = $this->checkoutSession->getQuote()->getItemsCount();
            if ($isEnable && $oldQuoteItem >0) {
                if (!$this->successValidator->isValid()) {
                    return $this->resultRedirectFactory->create()->setPath('checkout/cart');
                }
                $resultPage = $this->resultPageFactory->create();
                $this->eventManager->dispatch(
                    'checkout_onepage_controller_success_action',
                    ['order_ids' => [$session->getLastOrderId()]]
                );
                $this->checkoutSession->setLastSuccessQuoteId(null);
                return $resultPage;
            } else {
                $this->helper->setAttributeVirtualCart(0);
            }
            return $proceed();
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Controller_OnepageSuccess execute : ".$e->getMessage());
            $this->messageManager->addError($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
    }
}

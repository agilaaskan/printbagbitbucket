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
namespace Webkul\AttrBaseSplitcart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class MergeOldQuote implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger
     */
    private $attrBaseLogger;

    /**
     * @var \Webkul\AttrBaseSplitcart\Helper\Data
     */
    private $helper;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession,
     * @param \Magento\Framework\App\RequestInterface $request,
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory,
     * @param \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
     * @param \Webkul\AttrBaseSplitcart\Helper\Data $helper,
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
        \Webkul\AttrBaseSplitcart\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
        $this->quoteFactory = $quoteFactory;
        $this->attrBaseLogger = $attrBaseLogger;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
    }

    /**
     * [executes when controller_action_predispatch event hit,
     * and used to active original quote and remove split quote]
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $module = $this->request->getControllerModule();
            $controller = $this->request->getControllerName();
            $action = $this->request->getActionName();
            $fullname = strtolower($module).'_'.$controller.'_'.$action;
            $isEnable = $this->helper->checkAttributesplitcartStatus();
            
            $array = ['magento_checkout_index_index',
            'webkul_attrbasesplitcart_cartover_proceedtocheckout',
            'magento_customer_section_load','magento_checkout_cart_add',
            'magento_checkout_sidebar_removeItem','magento_checkout_sidebar_updateItemQty',
            'magento_checkout_cart_delete','magento_checkout_cart_updatePost',
            'magento_checkout_cart_updateItemOptions'];
            if (!in_array($fullname, $array) && $isEnable) 
            {
                $currentQuoteId = $this->checkoutSession->getQuote()->getId();
                $oldQuoteId = $this->helper->getAttributeVirtualCart();
                $currentQuoteItem = $this->checkoutSession->getQuote()->getItemsCount();
                $oldQuoteItem = $this->quoteFactory->create()->load($oldQuoteId)->getItemsCount();
                
                if ($oldQuoteId && $currentQuoteItem >0 && $oldQuoteItem >0 && $oldQuoteId!=$currentQuoteId) {
                    $newQuote = $this->quoteFactory->create()->load($currentQuoteId);
                    $newQuote->delete();
                    
                    $oldQuote = $this->quoteFactory->create()->load($oldQuoteId);
                    $oldQuote->setIsActive(1);
                    $oldQuote->collectTotals()->save();
                    $this->checkoutSession->replaceQuote($oldQuote);
                    $this->checkoutSession->setCartWasUpdated(true);
                    
                    if ($this->customerSession->isLoggedIn()) {
                        $this->helper->setAttributeVirtualCart(0);
                    }
                } elseif ($oldQuoteId && $oldQuoteItem >0) {
                    $oldQuote = $this->quoteFactory->create()->load($oldQuoteId);
                    $oldQuote->setIsActive(1);
                    $oldQuote->collectTotals()->save();
                    $this->checkoutSession->replaceQuote($oldQuote);
                    $this->checkoutSession->setCartWasUpdated(true);
                }
            }
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("MergeOldQuote execute : ".$e->getMessage());
        }
    }
}

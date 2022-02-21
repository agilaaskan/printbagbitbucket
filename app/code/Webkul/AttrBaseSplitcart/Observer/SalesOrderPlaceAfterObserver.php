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

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\Exception\LocalizedException;

class SalesOrderPlaceAfterObserver implements ObserverInterface
{
    /**
     * @var \Webkul\AttrBaseSplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger
     */
    private $attrBaseLogger;

    /**
     * @var Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @param \Webkul\AttrBaseSplitcart\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Webkul\AttrBaseSplitcart\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->quoteFactory = $quoteFactory;
        $this->attrBaseLogger = $attrBaseLogger;
        $this->productRepository = $productRepository;
        $this->product = $product;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * [executes when checkout_submit_all_after event hit,
     * and used to remove split quote item from original quote]
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $quoteId = $this->helper->getAttributeVirtualCart();
            $attrcartData = $this->checkoutSession->getAttrsplitcartData();
            $isEnable = $this->helper->checkAttributesplitcartStatus();
            if ($isEnable && isset($attrcartData['attrsplitcart_attribute'])
                && isset($attrcartData['attrsplitcart_value'])
                && $quoteId!==0
            ) {
                $oldQuote = $this->quoteFactory->create()->load($quoteId);
                $oldQuote->setIsActive(1)->save();
                $this->checkoutSession->replaceQuote($oldQuote);
                $this->checkoutSession->setCartWasUpdated(true);
                foreach ($oldQuote->getAllItems() as $item) {
                    $TypeId= $item->getProduct()->getTypeId();
                    if (!$item->hasParentItemId()) {
                        if($TypeId =='configurable'){
                            $ItemId =  $item->getItemId();
                            $quoteId =  $item->getQuoteId();
                            $productId= $item->getProduct()->getEntityId();
                            
                            $quoteItem = $this->quoteItemFactory->create()->getCollection()
                            ->addFieldToFilter('quote_id',['eq'=>$quoteId])
                            ->addFieldToFilter('parent_item_id',['eq'=>$ItemId])
                            ->getFirstItem();
                            $productSku = $this->product->create()->load($quoteItem->getProductId())->getSku();                    
                            $product = $this->productRepository->get($productSku);
                            $attrValueId = $product->getData($attrcartData['attrsplitcart_attribute']);
                        } else {
                            $productId= $item->getProduct()->getEntityId();
                                
                            $productSku = $this->product->create()->load($productId)->getSku();                    
                            $product = $this->productRepository->get($productSku);
                            $attrValueId = $product->getData($attrcartData['attrsplitcart_attribute']);
                        }
    
                        if ($attrValueId===0) {
                            $attrValueId = 0;
                        } 
                        if ($attrValueId=="") {
                            $attrValueId = -1;
                        } 
                        if ($attrcartData['attrsplitcart_value'] == $attrValueId) {
                            $oldQuote->deleteItem($item);
                        }
                    }
                }
                $oldQuote->collectTotals()->save();
                $this->checkoutSession->replaceQuote($oldQuote);
                $this->checkoutSession->setCartWasUpdated(true);
            }
            $this->checkoutSession->unsAttrsplitcartData();
            
            if ($this->customerSession->isLoggedIn()) {
                $this->helper->setAttributeVirtualCart(0);
            }
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("SalesOrderPlaceAfterObserver execute : ".$e->getMessage());
        }
    }
}

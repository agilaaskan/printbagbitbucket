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

namespace Webkul\AttrBaseSplitcart\Plugin\Controller\Checkout\Index;

class Index
{
    /**
     * @var \Webkul\AttrBaseSplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    private $cartHelper;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @var \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger
     */
    private $attrBaseLogger;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @param \Webkul\AttrBaseSplitcart\Helper\Data $helper
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Webkul\AttrBaseSplitcart\Helper\Data $helper,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->productRepository = $productRepository;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->attrBaseLogger = $attrBaseLogger;
        $this->messageManager = $messageManager;
        $this->product = $product;
    }

    /**
     * @param \Magento\Checkout\Block\Link $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Index\Index $subject,
        \Closure $proceed
    ) {
        $isEnable = $this->helper->checkAttributesplitcartStatus();
        $oldQuoteId = $this->helper->getAttributeVirtualCart();
        $attrCode = $this->helper->getSelectedAttribute();
        $cart = $this->cartHelper->getQuote();
        $count = 0;
        $cartArray = [];
        
        foreach ($cart->getAllItems() as $item) {
            if (!$item->hasParentItemId()) {   
                $TypeId= $item->getProduct()->getTypeId(); 
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
                    $attrValueId = $product->getData($attrCode);
                } else {
                    $productId= $item->getProduct()->getEntityId();
                        
                    $productSku = $this->product->create()->load($productId)->getSku();                    
                    $product = $this->productRepository->get($productSku);
                    $attrValueId = $product->getData($attrCode);
                }
                if ($attrValueId===0) {
                    $attrValueId = 0;
                } 
                if ($attrValueId=="") {
                    $attrValueId = -1;
                }             
                $cartArray[]=$attrValueId;           
            }
        }
        
        if ($isEnable && count(array_unique($cartArray)) >1) {
            
            $this->messageManager->addError(__('Please select one attribute to proceed !!'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
       
        return $proceed();
    }
}

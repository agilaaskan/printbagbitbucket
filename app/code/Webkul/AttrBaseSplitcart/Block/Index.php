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
namespace Webkul\AttrBaseSplitcart\Block;

/**
 * AttrBaseSplitcart Block
 */
class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\AttrBaseSplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cartModel;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger
     */
    private $attrBaseLogger;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Webkul\AttrBaseSplitcart\Helper\Data                  $helper
     * @param \Magento\Checkout\Model\Cart                     $cart
     * @param \Magento\Framework\Pricing\Helper\Data           $priceHelper
     * @param array                                            $data
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\AttrBaseSplitcart\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item $itemResourceModel,
        \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->cartModel = $cart;
        $this->priceHelper = $priceHelper;
        $this->productRepository = $productRepository;
        $this->product = $product;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemResourceModel = $itemResourceModel;
        $this->attrBaseLogger = $attrBaseLogger;
    }

    /**
     * [getCartDataByAttribute
     * show items at shopping cart accr. to attribute]
     *
     * @return [array]
     */
    public function getCartDataByAttribute()
    {
        $cartArray = [];
        try {
            $cart = $this->cartModel->getQuote();
            $attrCode = $this->getSelectedAttribute();

            foreach ($cart->getAllItems() as $item) {
                
                $TypeId= $item->getProduct()->getTypeId();

                if (!$item->hasParentItemId()){
                    
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
                
                    $price =  $item->getRowTotal();
                    if ($this->helper->getCatalogPriceIncludingTax()) {
                        $price = $item->getRowTotalInclTax();
                    }
                    $formattedPrice = $this->priceHelper->currency($price, true, false);
                    if (!isset($cartArray[$attrValueId]['attr_detail'])) {
                        $attrDetail = $this->helper->getAttributeValue($attrCode, $attrValueId,$TypeId);
                        $cartArray[$attrValueId]['attr_detail'] = $attrDetail;
                    }
                    $cartArray[$attrValueId][$item->getId()] = $formattedPrice;

                    if (!isset($cartArray[$attrValueId]['total'])
                        || $cartArray[$attrValueId]['total']==null
                    ) {
                        $cartArray[$attrValueId]['total'] = $price;
                    } else {
                        $cartArray[$attrValueId]['total'] += $price;
                    }
                    $formattedPrice = $this->priceHelper->currency($cartArray[$attrValueId]['total'], true, false);
                    $cartArray[$attrValueId]['formatted_total'] = $formattedPrice;
                } 
            }
            
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Block_Index getCartDataByAttribute : ".$e->getMessage());
        }
        return $cartArray;
    }

    /**
     * [getMpsplitcartEnable get splitcart is enable or not]
     *
     * @return void
     */
    public function getAttributesplitcartEnable()
    {
        try {
            return $this->helper->checkAttributesplitcartStatus();
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Block_Index getMpsplitcartEnable : ".$e->getMessage());
        }
    }

    /**
     * [getSelectedAttribute get Attribute code selected in config]
     *
     * @return string
     */
    public function getSelectedAttribute()
    {
        return $this->helper->getSelectedAttribute();
    }

}

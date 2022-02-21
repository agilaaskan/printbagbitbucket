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

namespace Webkul\AttrBaseSplitcart\Helper;

use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * AttrBaseSplitcart data helper.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    private $context;

    /**
     * @var \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger
     */
    private $attrBaseLogger;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @var Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var Magento\Checkout\Model\Cart
     */
    private $cart;

    /**
     * @var Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    private $customerDataFactory;

    /**
     * @var \Magento\Customer\Model\Customer\Mapper
     */
    private $customerMapper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Webkul\AttrBaseSplitcart\Cookie\Guestcart
     */
    private $guestCart;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    private $quoteItem;

    /**
     * @param \Magento\Framework\App\Helper\Context   $context
     * @param \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger   $attrBaseLogger
     * @param AttributeFactory   $attributeFactory
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param Magento\Checkout\Model\Cart $cart
     * @param CustomerRepositoryInterface               $customerRepository
     * @param CustomerInterfaceFactory                  $customerDataFactory
     * @param \Magento\Customer\Model\Customer\Mapper   $customerMapper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Webkul\AttrBaseSplitcart\Cookie\Guestcart $guestCart
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger $attrBaseLogger,
        AttributeFactory $attributeFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Checkout\Model\Cart $cart,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\AttrBaseSplitcart\Cookie\Guestcart $guestCart,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        DataObjectHelper $dataObjectHelper,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Quote\Model\Quote\Item $quoteItem
    ) {
        $this->attrBaseLogger = $attrBaseLogger;
        $this->attributeFactory = $attributeFactory;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->customerRepository = $customerRepository;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerMapper = $customerMapper;
        $this->jsonHelper = $jsonHelper;
        $this->guestCart = $guestCart;
        $this->quoteFactory = $quoteFactory;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->product = $product;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->quoteItem = $quoteItem;
        parent::__construct($context);
    }

    /**
     * [getEnableAttributeSplitcartSettings used to get spitcart is enable or not].
     *
     * @return [integer] [returns 0 if disable else return 1]
     */
    public function getEnableAttributeSplitcartSettings()
    {
        try {
            return $this->scopeConfig->getValue(
                'attrbasesplitcart/general_settings/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data getEnableAttributeSplitcartSettings : ".$e->getMessage());
        }
    }

    /**
     * [getSelectedAttribute used to get selected attribute].
     *
     * @return [string] [returns attribute code]
     */
    public function getSelectedAttribute()
    {
        try {
            return $this->scopeConfig->getValue(
                'attrbasesplitcart/general_settings/attribute',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data getSelectedAttribute : ".$e->getMessage());
        }
    }

    /**
     * isModuleEnabled checks a given module is enabled or not
     *
     * @param  string $moduleName
     * @return boolean
     */
    public function isModuleEnabled($moduleName)
    {
        try {
            return $this->_moduleManager->isEnabled($moduleName);
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data isModuleEnabled : ".$e->getMessage());
        }
    }

    /**
     * isOutputEnabled checks a given module is enabled or not
     *
     * @param  string $moduleName
     * @return boolean
     */
    public function isOutputEnabled($moduleName)
    {
        try {
            return $this->_moduleManager->isOutputEnabled($moduleName);
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data isOutputEnabled : ".$e->getMessage());
        }
    }

    /**
     * checkAttributesplitcartStatus checks a given module status
     *
     * @return boolean
     */
    public function checkAttributesplitcartStatus()
    {
        try {
            $moduleEnabled = $this->isModuleEnabled('Webkul_AttrBaseSplitcart');
            $moduleOutputEnabled = $this->isOutputEnabled('Webkul_AttrBaseSplitcart');
            $flag = ($this->getEnableAttributeSplitcartSettings()
                && $moduleEnabled
                && $moduleOutputEnabled
            ) ? true : false;
            return $flag;
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data checkAttributesplitcartStatus : ".$e->getMessage());
            return false;
        }
    }

    /**
     * getCatalogPriceIncludingTax get value of shipping include tax
     *
     * @return boolean
     */
    public function getCatalogPriceIncludingTax()
    {
        try {
            return $this->scopeConfig->getValue(
                'tax/calculation/shipping_includes_tax',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data getCatalogPriceIncludingTax : ".$e->getMessage());
            return false;
        }
    }

    /**
     * getAttributeValue get value of selected Attribute
     *
     * @param string $attrValue
     * @param string $attrCode
     * @return string
     */
    public function getAttributeValue($attrCode, $attrValue = "",$productType="")
    {
        try {
            $attrDetailArr = ['label'=>"", 'value'=>"N/A"];
            $attributeInfoColl = $this->attributeFactory->create()->getCollection()
                                ->addFieldToFilter('attribute_code', ['eq' => $attrCode]);
            $attributeInfo = false;
            foreach ($attributeInfoColl as $attrInfoData) {
                $attrDetailArr['label'] = $attrInfoData->getFrontendLabel();
                $options = $attrInfoData->getSource()->getAllOptions();
                foreach ($options as $option) {
                    if ($option['value']==$attrValue) {
                        $attrDetailArr['value'] = $option['label'];
                    }
                }    
            }
            return $attrDetailArr;
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data getCatalogPriceIncludingTax : ".$e->getMessage());
            return $attrDetailArr;
        }
    }

    /**
     * [getUpdatedQuote used to remove items of other attribute value from the quote].
     *
     * @param [array] $attrsplitcartData [current attribute data]
     *
     * @return void
     */
    public function getUpdatedQuote($attrsplitcartData)
    {
        try {
            $this->checkoutSession->setAttrsplitcartData($attrsplitcartData);
            $oldQuote = $this->cart->getQuote();
            $newQuote = $this->quoteFactory->create();
            $newQuote->setStoreId($oldQuote->getStoreId());
            if ($oldQuote->getCustomerId()) {
                $newQuote->setCustomerId($oldQuote->getCustomerId());
                $newQuote->setCustomerGroupId($oldQuote->getCustomerGroupId());
                $newQuote->setCustomerEmail($oldQuote->getCustomerEmail());
                $newQuote->setCustomerFirstname($oldQuote->getCustomerFirstname());
                $newQuote->setCustomerLastname($oldQuote->getCustomerLastname());
                $newQuote->setCustomerDob($oldQuote->getCustomerDob());
                $newQuote->setCustomerGender($oldQuote->getCustomerGender());
            }
            $newQuote->merge($oldQuote);
            $newQuote->collectTotals()->save();
            $oldQuote->setIsActive(0)->save();
            $this->checkoutSession->replaceQuote($newQuote);
            $this->checkoutSession->setCartWasUpdated(true);
            $flag = false;
            $attrCode = $this->getSelectedAttribute();
            
            foreach ($newQuote->getAllItems() as $item) {
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
                   
                    if ($attrsplitcartData['attrsplitcart_value'] != $attrValueId) {                     
                        $newQuote->deleteItem($item);
                    }
                }
            }
            
            $newQuote->collectTotals()->save();
            
            $this->checkoutSession->replaceQuote($newQuote);
            $this->checkoutSession->setCartWasUpdated(true);
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data getUpdatedQuote : ".$e->getMessage());
        }
    }

    /**
     * [addQuoteToVirtualCart used to set cart was updated true].
     *
     * @return void
     */
    public function addQuoteToVirtualCart()
    {
        try {
            $quote = $this->cart->getQuote();
            $attrvirtualQuoteId = $quote->getId();
            
            $this->setAttributeVirtualCart($attrvirtualQuoteId);
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data addQuoteToVirtualCart : ".$e->getMessage());
        }
    }

    /**
     * [setAttributeVirtualCart used to set virtual cart of user in customer session].
     *
     * @param string $attrvirtualQuoteId [contains virtual cart data]
     *
     * @return void
     */
    public function setAttributeVirtualCart($attrvirtualQuoteId)
    {
        try {
            if ($this->customerSession->isLoggedIn()) {
                $customerId  = $this->customerSession->getId();
                $customerData = [];
                $savedCustomerData = $this->customerRepository
                    ->getById($customerId);

                $customer = $this->customerDataFactory->create();
                //merge saved customer data with new values
                $customerData = array_merge(
                    $this->customerMapper->toFlatArray($savedCustomerData),
                    $customerData
                );

                $customerData['attr_virtual_cart'] = $attrvirtualQuoteId;
                $this->dataObjectHelper->populateWithArray(
                    $customer,
                    $customerData,
                    '\Magento\Customer\Api\Data\CustomerInterface'
                );
                //save customer
                $this->customerRepository->save($customer);
            } else {
                $this->guestCart->delete();
                $this->guestCart->set($attrvirtualQuoteId, 3600);
            }
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data setAttributeVirtualCart : ".$e->getMessage());
        }
    }

    /**
     * [getAttributeVirtualCart used to get virtual cart of user].
     *
     * @return string [returns attribute virtual cart data]
     */
    public function getAttributeVirtualCart()
    {
        try {
            if ($this->customerSession->isLoggedIn()) {
                $customerId = $this->customerSession->getId();
                $customerData = [];
                $savedCustomerData = $this->customerRepository
                    ->getById($customerId);
                $customer = $this->customerDataFactory->create();
                //merge saved customer data with new values
                
                $customerData = array_merge(
                    $this->customerMapper->toFlatArray($savedCustomerData),
                    $customerData
                );
                if (array_key_exists('attr_virtual_cart', $customerData)) {
                    $attrvirtualCart = $customerData['attr_virtual_cart'];
                } else {
                    $attrvirtualCart = "";
                }
            } else {
                $attrvirtualCart = $this->guestCart->get();
            }
            return $attrvirtualCart;
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("Helper_Data getAttributeVirtualCart : ".$e->getMessage());
        }
    }
}

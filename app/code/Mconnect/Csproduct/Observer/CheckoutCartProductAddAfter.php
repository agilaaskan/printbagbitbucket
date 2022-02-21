<?php
namespace Mconnect\Csproduct\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product\Type;
 
class CheckoutCartProductAddAfter implements ObserverInterface
{
    
    protected $_helper;
    
    protected $logger;
        
        
    public function __construct(

        \Mconnect\Csproduct\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
            
        $this->_helper = $helper;
        $this->logger = $logger;
        $this->_storeManager = $storeManager;
    }
        
        
        
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
            
            
        $typeId=$item->getProduct()->getTypeId();
        //$item = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();
            
        //$this->logger->debug($typeId);
            
        
        if ($item->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
            foreach ($item->getQuote()->getAllItems() as $bundleitems) {
                if ($bundleitems->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
                    $getprice =$this->_helper->getpricebyprdId($bundleitems->getProduct()->getId());
                    if (!empty($getprice) && ($getprice > 0)) {
                        $getprice = $bundleitems->getProduct()->getFinalPrice();
                        $bundleitems->setCustomPrice($getprice);
                        $bundleitems->setOriginalCustomPrice($getprice);
                        $bundleitems->getProduct()->setIsSuperMode(true);
                    }
                        
                    continue;
                }
                        
                   $getprice =$this->_helper->getpricebyprdId($bundleitems->getProduct()->getId());
                if (!empty($getprice) && ($getprice > 0)) {
                    $getprice = $bundleitems->getProduct()->getFinalPrice();
                    $bundleitems->setCustomPrice($getprice);
                    $bundleitems->setOriginalCustomPrice($getprice);
                    $bundleitems->getProduct()->setIsSuperMode(true);
                }
            }
                $item->getProduct()->setIsSuperMode(true);
        } else {
            foreach ($item->getQuote()->getAllItems() as $pItem) {
                //$this->logger->debug($bundleitems->getProduct()->getId());
                    
                $getprice =$this->_helper->getpricebyprdId($pItem->getProduct()->getId());
                    
                $getprice = $pItem->getProduct()->getFinalPrice();
                    
                if (!empty($getprice) && ($getprice > 0)) {
                    $pItem->setCustomPrice($getprice);
                    $pItem->setOriginalCustomPrice($getprice);
                    $pItem->getProduct()->setIsSuperMode(true);
                }
                    
                $this->logger->debug($pItem->getProduct()->getId());
            }
                
            $item->getProduct()->setIsSuperMode(true);
        }
        return $this;
    }
}

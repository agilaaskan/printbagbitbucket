<?php
/*
* Mconnect
*/
namespace Mconnect\Csproduct\Block\Adminhtml\Product\Edit\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Store\Model\StoreManagerInterface;

class Cgroupprice extends AbstractRenderer
{
    
    private $_storeManager;
    
    private $_priceHelper;
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        StoreManagerInterface $storemanager,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
    ) {
        $this->_storeManager = $storemanager;
        $this->_priceHelper = $priceHelper;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }
    /**
     * Renders grid column
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        
        $custom_price = $row->getData('cs_price');
        $product_id =  $row->getEntityId();
        $data='';
       
        
        if (($row->getTypeId() == 'bundle') || ($row->getTypeId() == 'grouped')) {
            return '<span> N/A </span>';
        }
               
        
        if (!empty($custom_price)) {
            $dis_price = $this->_priceHelper->currency($custom_price, true, false);
            $data.= $dis_price;
        }
        
                
        return $data;
    }
}

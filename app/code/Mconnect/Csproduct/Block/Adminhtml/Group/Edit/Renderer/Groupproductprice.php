<?php
/*
* Mconnect
*/
namespace Mconnect\Csproduct\Block\Adminhtml\Group\Edit\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Store\Model\StoreManagerInterface;

class Groupproductprice extends AbstractRenderer
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
     *
     * @param Object $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $custom_price = $row->getData('customprice');
        $product_id =  $row->getEntityId();
        $price_type=$row->getData('price_type');
        
        $data='';
        
        if (($row->getTypeId() == 'grouped') || ($row->getTypeId() == 'bundle' && $price_type==0)) {
            return '<span> N/A </span>';
        }
                
        if ($row->getTypeId() == 'configurable') {
             return '<span> N/A </span>';
        }
        
        
        if (!empty($custom_price)) {
            $dis_price = $this->_priceHelper->currency($custom_price, true, false);
            $data.= $dis_price;
        }
        
        $data.= '<input type="text" size="5" class="input-text validate-number" id="groupproductId' .$product_id .'"  name="customer_group_product['.$product_id.']" value="'.$custom_price.'"  />';
        
        return $data;
    }
}

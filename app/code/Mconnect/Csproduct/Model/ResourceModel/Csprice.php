<?php
namespace Mconnect\Csproduct\Model\ResourceModel;
 
class Csprice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    
    protected $_storeManager;
     
    protected $_store = null;
    
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }
     
    /**
     * Initialize resource model
     *
     * @return void Csprice
     */
    protected function _construct()
    {
        $this->_init('mconnect_csprice', 'csprice_id');
    }
}

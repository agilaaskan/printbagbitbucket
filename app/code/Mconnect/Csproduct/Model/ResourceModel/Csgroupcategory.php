<?php
namespace Mconnect\Csproduct\Model\ResourceModel;
 
class Csgroupcategory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * @return void
     *
     */
     
    protected function _construct()
    {
        $this->_init("mconnect_csgroupcategory", 'csgcategory_id');
    }
}

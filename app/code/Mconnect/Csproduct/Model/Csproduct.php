<?php
namespace Mconnect\Csproduct\Model;
 
class Csproduct extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    
    protected function _construct()
    {
        $this->_init('Mconnect\Csproduct\Model\ResourceModel\Csproduct');
    }
}

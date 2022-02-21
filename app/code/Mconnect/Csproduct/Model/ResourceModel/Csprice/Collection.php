<?php
namespace Mconnect\Csproduct\Model\ResourceModel\Csprice;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'csprice_id';
   
    protected function _construct()
    {
        $this->_init('Mconnect\Csproduct\Model\Csprice', 'Mconnect\Csproduct\Model\ResourceModel\Csprice');
    }
}

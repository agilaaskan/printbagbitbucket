<?php
namespace Mconnect\Csproduct\Model\ResourceModel\Csproduct;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'csproduct_id';
   
    protected function _construct()
    {
        $this->_init('Mconnect\Csproduct\Model\Csproduct', 'Mconnect\Csproduct\Model\ResourceModel\Csproduct');
    }
}

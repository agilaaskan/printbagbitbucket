<?php
namespace Mconnect\Csproduct\Model\ResourceModel\Csgroupproduct;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'csgroupproduct_id';
   
    protected function _construct()
    {
        $this->_init('Mconnect\Csproduct\Model\Csgroupproduct', 'Mconnect\Csproduct\Model\ResourceModel\Csgroupproduct');
    }
}

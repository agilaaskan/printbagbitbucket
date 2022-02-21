<?php
namespace Mconnect\Csproduct\Model\ResourceModel\Csrestricgroupproduct;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'csrestricgroupproduct_id';
   
    protected function _construct()
    {
        $this->_init('Mconnect\Csproduct\Model\Csrestricgroupproduct', 'Mconnect\Csproduct\Model\ResourceModel\Csrestricgroupproduct');
    }
}

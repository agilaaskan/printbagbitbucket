<?php
namespace Mconnect\Csproduct\Model\ResourceModel\Csgroupcategory;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'csgcategory_id';
   
    protected function _construct()
    {
        $this->_init('Mconnect\Csproduct\Model\Csgroupcategory', 'Mconnect\Csproduct\Model\ResourceModel\Csgroupcategory');
    }
}

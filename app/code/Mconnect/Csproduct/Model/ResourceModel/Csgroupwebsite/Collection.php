<?php
namespace Mconnect\Csproduct\Model\ResourceModel\Csgroupwebsite;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'csgwebsite_id';
   
    protected function _construct()
    {
        $this->_init('Mconnect\Csproduct\Model\Csgroupwebsite', 'Mconnect\Csproduct\Model\ResourceModel\Csgroupwebsite');
    }
}

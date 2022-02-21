<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomerAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerAttributes\Model\ResourceModel;

use Magento\Customer\Model\Attribute;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class AbstractSales
 * @package Mageplaza\CustomerAttributes\Model\ResourceModel
 */
abstract class AbstractSales extends AbstractDb
{
    /**
     * Used us prefix to name of column table
     *
     * @var null | string
     */
    protected $_columnPrefix = 'customer';

    /**
     * Primary key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Return column name for attribute
     *
     * @param Attribute $attribute
     *
     * @return string
     */
    protected function _getColumnName(Attribute $attribute)
    {
        $columnName = $attribute->getAttributeCode();
        if ($this->_columnPrefix) {
            $columnName = sprintf('%s_%s', $this->_columnPrefix, $columnName);
        }

        return $columnName;
    }

    /**
     * @param Attribute $attribute
     *
     * @return $this
     * @throws LocalizedException
     */
    public function createAttribute(Attribute $attribute)
    {
        $backendType = $attribute->getBackendType();
        if ($backendType == Attribute::TYPE_STATIC) {
            return $this;
        }

        switch ($backendType) {
            case 'datetime':
                $definition = ['type' => Table::TYPE_DATE];
                break;
            case 'decimal':
                $definition = ['type' => Table::TYPE_DECIMAL, 'length' => '12,4'];
                break;
            case 'int':
                $definition = ['type' => Table::TYPE_INTEGER];
                break;
            case 'text':
                $definition = ['type' => Table::TYPE_TEXT];
                break;
            case 'varchar':
                $definition = ['type' => Table::TYPE_TEXT, 'length' => 255];
                break;
            default:
                return $this;
        }

        $columnName            = $this->_getColumnName($attribute);
        $definition['comment'] = ucwords(str_replace('_', ' ', $columnName));

        $this->getConnection()->addColumn($this->getMainTable(), $columnName, $definition);

        return $this;
    }

    /**
     * @param Attribute $attribute
     *
     * @return $this
     * @throws LocalizedException
     */
    public function deleteAttribute(Attribute $attribute)
    {
        $this->getConnection()->dropColumn($this->getMainTable(), $this->_getColumnName($attribute));

        return $this;
    }

    /**
     * @param DataObject[] $entities
     *
     * @return array
     * @throws LocalizedException
     */
    public function attachDataToSalesOrder(array $entities)
    {
        $items   = [];
        $itemIds = [];
        foreach ($entities as $entity) {
            $itemIds[]                   = $entity['entity_id'];
            $items[$entity['entity_id']] = new DataObject();
        }

        if ($itemIds) {
            $select = $this->getConnection()->select()->from(
                $this->getMainTable()
            )->where(
                "{$this->getIdFieldName()} IN (?)",
                $itemIds
            );
            $rowSet = $this->getConnection()->fetchAll($select);
            foreach ($rowSet as $row) {
                $items[$row[$this->getIdFieldName()]]->addData($row);
            }
        }

        return $items;
    }
}

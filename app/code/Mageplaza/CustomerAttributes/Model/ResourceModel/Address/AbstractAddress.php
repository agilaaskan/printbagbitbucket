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

namespace Mageplaza\CustomerAttributes\Model\ResourceModel\Address;

use Magento\Customer\Model\Attribute;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mageplaza\CustomerAttributes\Model\ResourceModel\AbstractSales;

/**
 * Class AbstractAddress
 * @package Mageplaza\CustomerAttributes\Model\ResourceModel\Address
 */
abstract class AbstractAddress extends AbstractSales
{
    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * AbstractAddress constructor.
     *
     * @param Context $context
     * @param Attribute $attribute
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        Attribute $attribute,
        $connectionName = null
    ) {
        $this->attribute = $attribute;

        parent::__construct($context, $connectionName);
    }

    /**
     * Used us prefix to name of column table
     *
     * @var null | string
     */
    protected $_columnPrefix = null;

    /**
     * Attach data to models
     *
     * @param DataObject[] $entities
     *
     * @return $this
     * @throws LocalizedException
     */
    public function attachDataToEntities(array $entities)
    {
        $items   = [];
        $itemIds = [];
        foreach ($entities as $item) {
            /** @var $item DataObject */
            $itemIds[]             = $item->getId();
            $items[$item->getId()] = $item;
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

        return $this;
    }

    /**
     * @param array $entities
     * @param string $prefix
     *
     * @return array
     */
    public function attachDataToCustomerAddress(array $entities, $prefix = '')
    {
        $items   = [];
        $itemIds = [];
        foreach ($entities as $entity) {
            $itemIds[]                   = $entity['entity_id'];
            $items[$entity['entity_id']] = new DataObject();
        }

        if ($itemIds) {
            $select    = $this->getConnection()->select()->from(
                $this->getTable('customer_entity')
            )->where(
                'entity_id IN (?)',
                $itemIds
            );
            $customers = $this->getConnection()->fetchAll($select);
            foreach ($customers as $customer) {
                $select  = $this->getConnection()->select()->from(
                    $this->getTable('customer_address_entity')
                )->where(
                    'entity_id = ?',
                    $customer['default_billing']
                );
                $address = $this->getConnection()->fetchRow($select);

                if ($address) {
                    $attrTables = [
                        'customer_address_entity_datetime',
                        'customer_address_entity_decimal',
                        'customer_address_entity_int',
                        'customer_address_entity_text',
                        'customer_address_entity_varchar'
                    ];
                    foreach ($attrTables as $table) {
                        $select = $this->getConnection()->select()->from(
                            $this->getTable($table)
                        )->where(
                            "entity_id = ?",
                            $address['entity_id']
                        );

                        $rowSet = $this->getConnection()->fetchAll($select);

                        foreach ($rowSet as $row) {
                            $attr                                         = $this->attribute->load($row['attribute_id']);
                            $address[$prefix . $attr->getAttributeCode()] = $row['value'];
                        }
                    }

                    $items[$customer['entity_id']]->addData($address);
                }
            }
        }

        return $items;
    }
}

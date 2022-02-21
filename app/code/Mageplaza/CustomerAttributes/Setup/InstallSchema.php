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

namespace Mageplaza\CustomerAttributes\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\CustomerAttributes\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection     = $setup->getConnection();
        $attributeTable = $setup->getTable('customer_eav_attribute');

        if (!$setup->getConnection()->tableColumnExists($attributeTable, 'is_used_in_sales_order_grid')) {
            $connection->addColumn($attributeTable, 'is_used_in_sales_order_grid', [
                'type'     => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default'  => 0,
                'comment'  => 'Is Used in Sales Order Grid'
            ]);
        }
        if (!$setup->getConnection()->tableColumnExists($attributeTable, 'field_depend')) {
            $connection->addColumn($attributeTable, 'field_depend', [
                'type'     => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default'  => 0,
                'comment'  => 'Field to depend on'
            ]);
        }
        if (!$setup->getConnection()->tableColumnExists($attributeTable, 'value_depend')) {
            $connection->addColumn($attributeTable, 'value_depend', [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'length'   => 255,
                'comment'  => 'Value to depend on'
            ]);
        }
        if (!$setup->getConnection()->tableColumnExists($attributeTable, 'customer_can_edit')) {
            $connection->addColumn($attributeTable, 'customer_can_edit', [
                'type'     => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default'  => 1,
                'comment'  => 'Customer can edit'
            ]);
        }
        if (!$setup->getConnection()->tableColumnExists($attributeTable, 'mp_store_id')) {
            $connection->addColumn($attributeTable, 'mp_store_id', [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'length'   => 255,
                'comment'  => 'Store Id'
            ]);
        }
        if (!$setup->getConnection()->tableColumnExists($attributeTable, 'mp_customer_group')) {
            $connection->addColumn($attributeTable, 'mp_customer_group', [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'length'   => 255,
                'comment'  => 'Customer Group'
            ]);
        }
        if (!$setup->getConnection()->tableColumnExists($attributeTable, 'mp_created_date')) {
            $connection->addColumn($attributeTable, 'mp_created_date', [
                'type'     => Table::TYPE_TIMESTAMP,
                'nullable' => false,
                'default'  => Table::TIMESTAMP_INIT,
                'comment'  => 'Attribute Created Date'
            ]);
        }
        if (!$setup->getConnection()->tableColumnExists($attributeTable, 'additional_data')) {
            $connection->addColumn($attributeTable, 'additional_data', [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'default'  => null,
                'comment'  => 'Additional Data',
            ]);
        }

        $table = $connection->newTable(
            $setup->getTable('mageplaza_customer_attribute_sales_order')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $setup->getFkName(
                'mageplaza_customer_attribute_sales_order',
                'entity_id',
                'sales_order',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment('Customer Attribute Sales Order');
        $connection->createTable($table);

        $table = $connection->newTable(
            $setup->getTable('mageplaza_customer_attribute_sales_order_address')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $setup->getFkName(
                'mageplaza_customer_attribute_sales_order_address',
                'entity_id',
                'sales_order_address',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('sales_order_address'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment('Customer Attribute Sales Order Address');
        $connection->createTable($table);

        $table = $connection->newTable(
            $setup->getTable('mageplaza_customer_attribute_quote')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $setup->getFkName(
                'mageplaza_customer_attribute_quote',
                'entity_id',
                'quote',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('quote'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment('Customer Attribute Quote');
        $connection->createTable($table);

        $table = $connection->newTable(
            $setup->getTable('mageplaza_customer_attribute_quote_address')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $setup->getFkName(
                'mageplaza_customer_attribute_quote_address',
                'entity_id',
                'quote_address',
                'address_id'
            ),
            'entity_id',
            $setup->getTable('quote_address'),
            'address_id',
            Table::ACTION_CASCADE
        )->setComment('Customer Attribute Quote Address');
        $connection->createTable($table);

        $setup->endSetup();
    }
}

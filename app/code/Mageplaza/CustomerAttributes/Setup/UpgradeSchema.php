<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\CustomerAttributes\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $attributeTable = $setup->getTable('customer_eav_attribute');
            if (!$setup->getConnection()->tableColumnExists($attributeTable, 'max_file_size')) {
                $connection->addColumn($attributeTable, 'max_file_size', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment'  => 'Maximum File Size'
                ]);
            }
            if (!$setup->getConnection()->tableColumnExists($attributeTable, 'file_extensions')) {
                $connection->addColumn($attributeTable, 'file_extensions', [
                    'type'     => Table::TYPE_TEXT,
                    'size'     => 255,
                    'nullable' => true,
                    'comment'  => 'File extensions'
                ]);
            }
        }

        $mcaso = $setup->getTable('mageplaza_customer_attribute_sales_order');
        if (($connection->tableColumnExists($mcaso, 'entity_id'))) {
            $connection->changeColumn(
                $mcaso,
                'entity_id',
                'order_id',
                [
                    'type'     => Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'default'  => '0'
                ]
            );
        }

        $setup->endSetup();
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mconnect\Csproduct\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'mconnect_csproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mconnect_csproduct')
        )->addColumn(
            'csproduct_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Csproduct Id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            32,
            [],
            'Product Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            32,
            [],
            'Customer Id'
        )->addColumn(
            'csp_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            ['unsigned'=> true, 'nullable'=> false, 'primary'=> false],
            'Csp Price'
        );
        
        $installer->getConnection()->createTable($table);
    }
}

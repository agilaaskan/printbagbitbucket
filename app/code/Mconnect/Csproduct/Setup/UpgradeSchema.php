<?php
namespace Mconnect\Csproduct\Setup;
 
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
 
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
 
        // Action to do if module version is less than 1.0.0
         
 
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('mconnect_csgroupproduct')
            )->addColumn(
                'csgroupproduct_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'CS Product Group ID'
            )->addColumn(
                'group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Customer Group ID'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Product ID'
            )->addColumn(
                'csgp_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                ['unsigned'=> true, 'nullable'=> false, 'primary'=> false],
                'Csp Price'
            );
 
          
            $installer->getConnection()->createTable($table);
        }
        
        
        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('mconnect_csrestricgroupproduct')
            )->addColumn(
                'csrestricgroupproduct_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'CS Product Restric ID'
            )->addColumn(
                'group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Customer Group ID'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Product ID'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Customer ID'
            )->addColumn(
                'csrp_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                ['unsigned'=> true, 'nullable'=> false, 'primary'=> false],
                'Csrp Price'
            );
 
          
            $installer->getConnection()->createTable($table);
        }
        
        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('mconnect_csprice')
            )->addColumn(
                'csprice_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'CS Product Price ID'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Customer Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Product ID'
            )->addColumn(
                'cs_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                null,
                ['unsigned'=> true, 'nullable'=> false, 'primary'=> false],
                'Cs Price'
            );
 
          
            $installer->getConnection()->createTable($table);
        }
        
        if (version_compare($context->getVersion(), '2.0.5') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('mconnect_csgroupcategory')
            )->addColumn(
                'csgcategory_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'CS Gouup Category ID'
            )->addColumn(
                'group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Group Id'
            )->addColumn(
                'category_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Category Ids'
            );
            $installer->getConnection()->createTable($table);
        }
        
        if (version_compare($context->getVersion(), '2.0.6') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('mconnect_csgroupwebsite')
            )->addColumn(
                'csgwebsite_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'CS Gouup Website ID'
            )->addColumn(
                'group_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Group Id'
            )->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                32,
                [],
                'Website ID'
            );
            $installer->getConnection()->createTable($table);
        }
        
 
        $installer->endSetup();
    }
}

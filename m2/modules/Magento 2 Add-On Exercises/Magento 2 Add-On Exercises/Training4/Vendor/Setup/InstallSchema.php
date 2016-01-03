<?php

namespace Training4\Vendor\Setup;

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

        $table = $installer->getConnection()->newTable(
            $installer->getTable('training4_vendor')
        )->addColumn(
            'vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true, 'nullable' => false, 'primary' => true),
            'vendor_id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'name'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            array(),
            'Active Status'
        )->setComment(
            'Vendor Table'
        );
        $installer->getConnection()->createTable($table);

		$table = $installer->getConnection()->newTable(
            $installer->getTable('training4_vendor2product')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true, 'nullable' => false, 'primary' => true),
            'entity_id '
        )->addColumn(
            'vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false, 'primary' => false),
            'vendor_id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false, 'primary' => false),
            'product_id'
        )->setComment(
            'Vendor Product Table'
        );
        $installer->getConnection()->createTable($table);
		
        $installer->endSetup();

    }
}

<?php
namespace Fredo\Nexty\Setup;

//drop table nexty_payment_transactions; drop table nexty_payment_order_in_coin; drop table nexty_payment_blocks
//delete setup_module
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public $db_prefix='nexty_payment_';
    public function create_table_order_in_coin($installer){
      $table_name  = $this->db_prefix."order_in_coin";

      $installer->startSetup();

      $table = $installer->getConnection()
          ->newTable($installer->getTable($table_name))
          ->addColumn(
              'order_id',
              \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              null,
              ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
              'Order ID'
          )
          ->addColumn(
              'store_currency',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false],
              'accepted Currency by Store'
          )
          ->addColumn(
              'order_total',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false],
              'Order Price in the Store Currency'
          )
    /*      ->addColumn(
              'order_total_usd',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false],
              'Order Price in USD'
          )*/
          ->addColumn(
              'order_total_in_coin',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false],
              'Order Price in Nexty coin'
          )
          ->addColumn(
              'placed_time',
              \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
              null,
              ['nullable' => false, 'unsigned' => true],
              'Order placed time,exchanging at the same time'
          )
          ;
      $installer->getConnection()->createTable($table);


      $installer->endSetup();

    }

    public function create_table_blocks($installer){
      $blocks_table_name  = $this->db_prefix."blocks";

      $installer->startSetup();

      $table = $installer->getConnection()
          ->newTable($installer->getTable($blocks_table_name))
          ->addColumn(
              'id',
              \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              null,
              ['identity' => true, 'auto_increment' => true , 'unsigned' => true, 'nullable' => false, 'primary' => true],
              'ID'
          )
          ->addColumn(
              'number',
              \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              null,
              ['unsigned' => true, 'nullable' => false],
              'Block_number'
          )
          ->addColumn(
              'hash',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false],
              'Hash'
          )
          ->addColumn(
              'header',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              null,
              ['nullable' => true],
              'Block_header'
          )
          ->addColumn(
              'prev_header',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => true],
              'Prev_Block_header'
          )
          ->addColumn(
              'time',
              \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
              null,
              ['nullable' => false, 'unsigned' => true],
              'Block_created_time'
          )
          ;
      $installer->getConnection()->createTable($table);


      $installer->endSetup();

    }

    public function create_table_transactions($installer){
      $transactions_table_name  = $this->db_prefix."transactions";

      $installer->startSetup();

      $table = $installer->getConnection()
          ->newTable($installer->getTable($transactions_table_name))
          ->addColumn(
              'id',
              \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              null,
              ['identity' => true, 'auto_increment' => true , 'unsigned' => true, 'nullable' => false, 'primary' => true],
              'ID'
          )
          ->addColumn(
              'block_number',
              \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              null,
              ['unsigned' => true, 'nullable' => false],
              'Block_number'
          )
          ->addColumn(
              'block_hash',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false],
              'Block_Hash'
          )
          ->addColumn(
              'hash',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false],
              'Hash'
          )
          ->addColumn(
              'from_wallet',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              null,
              ['nullable' => true],
              'From_WalletAddress'
          )
          ->addColumn(
              'to_wallet',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => true],
              'To_WalletAddress'
          )
          ->addColumn(
              'value',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => true],
              'Extra_Data'
          )
          ->addColumn(
              'time',
              \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
              null,
              ['nullable' => false, 'unsigned' => true],
              'Block_created_time'
          )
          ->addColumn(
              'order_id',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => true],
              'Order_id_from_extra_data'
          )
          ;
      $installer->getConnection()->createTable($table);


      $installer->endSetup();

    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $this->create_table_blocks($installer);
        $this->create_table_transactions($installer);
        $this->create_table_order_in_coin($installer);
        //$this->init_blocks_table_db();

    }
}
?>

<?php
namespace Fredo\Nexty\Setup;

//drop table nexty_payment_transactions; drop table nexty_payment_order_in_coin; drop table nexty_payment_blocks
//delete setup_module
use Magento\Framework\Setup\UninstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UninstallSchema implements UninstallSchemaInterface
{
    public $db_prefix='nexty_payment_';

    public function drop_table_order_in_coin($uninstaller){
      $table_name  = $this->db_prefix."order_in_coin";

      $uninstaller->startSetup();

      if ($uninstaller->tableExists($table_name)) {
          $uninstaller->getConnection()->dropTable($table_name);
      }

    }

    public function drop_table_transactions($uninstaller){
      $table_name  = $this->db_prefix."transactions";

      $uninstaller->startSetup();

      if ($uninstaller->tableExists($table_name)) {
          $uninstaller->getConnection()->dropTable($table_name);
      }

    }

    public function drop_table_blocks($uninstaller){
      $table_name  = $this->db_prefix."blocks";

      $uninstaller->startSetup();

      if ($uninstaller->tableExists($table_name)) {
          $uninstaller->getConnection()->dropTable($table_name);
      }

    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $uninstaller = $setup;

        $this->drop_table_blocks($uninstaller);
        $this->drop_table_transactions($uninstaller);
        $this->drop_table_order_in_coin($uninstaller);
        //$this->init_blocks_table_db();

    }
}
?>

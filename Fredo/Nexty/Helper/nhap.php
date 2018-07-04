<?php
namespace Fredo\Nexty\Helper;

class UpdateDB extends \Magento\Framework\App\Helper\AbstractHelper
{
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    public $connection = $resource->getConnection();
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
      $this->_scopeConfig = $scopeConfig;
    }

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
                $config_path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
    }

    ///////////////////////////////////////////////////
    public function strToHex($string){

    	$hex = '';
    	for ($i=0; $i<strlen($string); $i++){
    		$ord = ord($string[$i]);
    		$hexCode = dechex($ord);
    		$hex .= substr('0'.$hexCode, -2);
    	}
    	return strToLower($hex);

    }

    public function hexToStr($hex){

        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;

    }

    public function get_order_id_from_input($input_hash){

    	//{“walletaddress”: “0x841A13DDE9581067115F7d9D838E5BA44B537A42″,”uoid”: “46”,”amount”: “80000”}
    	$input=(hexToStr($input_hash))."<br>";
    	//echo $input;
    	$input = str_replace(' ', '', $input);
    	$input = str_replace('{', '', $input);
    	$input = str_replace('}', '', $input);
    	$input_arr=(explode(",",$input));

    	$key='uoid';

    	foreach($input_arr as $str)
    	{
    		//echo $str."<br>";
    		$tmp= explode(":",$str);
    		$delete_list=array('"','“','″','”');
    		$get_key=str_replace($delete_list, '',$tmp[0]);
    		//echo $get_key."<br>";
    		if (str_replace($delete_list,'',$tmp[0])==$key) return str_replace($delete_list, '',$tmp[1]);
    	}
    	return false;

    }

    public function transaction_exist($connection,$transactions_table_name,$hash){
    	$sql= "SELECT hash FROM $transactions_table_name
    			WHERE hash='$hash'";
    	//$result = $wpdb->get_var($sql);
      $result = $connection->fetchAll($sql);
    	if ($result==$hash) return true;
    	return false;
    }

    public function insert_transactions_db($connection,$transactions,$transactions_table_name,$admin_wallet_address,$block_time){

    	foreach ($transactions as $transaction)
    	if (strtolower($transaction['to'])==strtolower($admin_wallet_address))
    	{
    		$block_hash=$transaction['blockHash'];
    		$block_number=$transaction['blockNumber'];
    		$from_wallet=$transaction['from'];
    		$to_wallet=$transaction['to'];
    		$value=$transaction['value'];
    		$time=$block_time;
    		$hash=$transaction['hash'];
    		$extra_data=$transaction['input'];
    		$order_id=get_order_id_from_input($transaction['input']);
        $block_number_dec= hexdec($block_number);

    		if (!transaction_exist($connection,$transactions_table_name,$hash)){
          $sql = "INSERT INTO " . $blocks_table_name . "(block_number, block_hash, hash, from_wallet, to_wallet, value, time, order_id) VALUES
            ('$block_number_dec', '$block_hash', '$hash', '$from_wallet', '$to_wallet', '$value', '$time', '$order_id')";
          $connection->query($sql);
    		}
    	}

    }

    public function insert_block_db($connection,$block_content,$blocks_table_name,$transactions_table_name,$admin_wallet_address){

    	//if block still unavaiable
    	if (!$block_content) return;
    	$block_number=hexdec($block_content['number']);
    	$block_hash=$block_content['hash'];
    	$block_header="";	/////////////////////////////////TODO
    	$block_prev_header=$block_content['parentHash'];
    	$block_time=hexdec($block_content['timestamp']);
    	$block_time= date("Y-m-d H:i:s", $block_time);
    	$transactions=$block_content['transactions'];

      $sql = "INSERT INTO " . $blocks_table_name . "(number, hash, header, prev_header, time) VALUES
      ('$block_number', '$block_hash', '$block_header', '$block_prev_header','$block_time')";
      $connection->query($sql);

    	insert_transactions_db($connection,$transactions,$transactions_table_name,$admin_wallet_address,$block_time);

    }

    public function count_total_blocks_db($connection,$blocks_table_name){

    	$table_name = $blocks_table_name;
    	$sql="SELECT COUNT('id') AS count FROM $table_name";
      $result = $connection->fetchAll($sql);
        //$result = $wpdb->get_var($sql);
    	return $result;

    }

    public function delete_old_blocks_db($connection,$blocks_table_name,$bottom_limit,$top_limit){

    	$total_blocks=count_total_blocks_db($connection,$blocks_table_name);
    	$total_blocks_to_delete=$total_blocks-$bottom_limit;
    	//echo $total_blocks;
    	if ($top_limit>$total_blocks) return;
    	$sql="DELETE FROM $blocks_table_name LIMIT $total_blocks_to_delete";
    	$connection->query($sql);

    }

    public function is_table_empty_db($connection,$table_name){

    	$sql="SELECT * FROM $table_name";
        $result = $connection->fetchAll($sql);
        return(count($result) == 0);

    }

    public function get_max_block_number_db($connection,$blocks_table_name){

    	$table_name = $blocks_table_name;
    	$sql="SELECT MAX(number) AS max FROM $table_name";
        $result = $connection->fetchAll($sql);
    	return $result;

    }

    public function get_paid_sum_by_order_id($connection,$transactions_table_name,$order_id){
    	$sql = "SELECT value FROM $transactions_table_name
    			WHERE order_id='$order_id'";
    	$result = $connection->fetchAll($sql);
    	$sum=0;
    	foreach ($results as $key){
    		$value=hexdec($key->value);
    		//echo $value;
    		$sum=$sum+$value;
    	}
    return $sum;
    }

    public function get_last_update_exchange_db($connection,$wc_currency,$exchange_table_name){

    	$table_name = $exchange_table_name;
    	$sql="SELECT MAX(time) AS max FROM $table_name";
        $result = $connection->fetchAll($sql);
    	return $result;
    }

    public function update_exchange_to_usd_table_db($connection,$wc_currency,$exchange_table_name,$interval_in_min){
    	$last_update=get_last_update_exchange_db($wpdb,$wc_currency,$exchange_table_name);
    	$from_time = strtotime($last_update);
    	$to_time = date("Y-m-d H:i:s");
    	$time_diff_in_min=round(abs($to_time - $from_time) / 60,2);
    	if ($time_diff_in_min>$interval_in_min){ // update every interval_in_min
    		$value=number_format(wc_currency_to_usd($wc_currency,1),10);
    		$sql="UPDATE $exchange_table_name
    			SET value=$value
    			WHERE from_currency='$wc_currency'";
    		$connection->query($sql);
    		//echo $sql;
    	}
    }

    public function get_exchange_to_usd_db($connection,$wc_currency,$exchange_table_name){
    	$sql= "SELECT value FROM $exchange_table_name
    			WHERE from_currency='$wc_currency'";
    	$result = $connection->fetchAll($sql);
    	return $result;

    }

    public function update_nexty_db(){
    	//global $wpdb;
    	//$nexty_payment_url			= dirname(__FILE__);
    	//$nexty_payment_js_url		= $nexty_payment_url.'/assets/js/';
    	//$nexty_payment_css_url		= $nexty_payment_url.'/assets/css/';
    	//$nexty_payment_includes_url = $nexty_payment_url.'/includes/' ;
    	//include_once $nexty_payment_includes_url.'blockchain.php';
    	//include_once $nexty_payment_includes_url.'db_functions.php';
    	//include_once $nexty_payment_includes_url.'exchange.php';
    	//echo coinmarketcap_id_to_usd(2714,2000);
    	//echo '<br>';
    	//$wc_currency = get_woocommerce_currency();
    	//echo wc_currency_to_usd($wc_currency,80000);
      $admin_wallet_address=$this->getConfig('payment/sample_gateway/wallet_address');
      $min_blocks_saved_db=$this->getConfig('payment/sample_gateway/min_blocks_saved_db');
      $max_blocks_saved_db=$this->getConfig('payment/sample_gateway/max_blocks_saved_db');
      $blocks_loaded_each_request=$this->getConfig('payment/sample_gateway/blocks_loaded_each_request');

      require_once __DIR__ ."/../Helper/blockchain.php";

    	$blocks_table_name 		= 'nexty_payment_blocks';
    	$transactions_table_name= 'nexty_payment_transactions';
    	$exchange_table_name	= 'nexty_payment_exchange_to_usd';
    	//echo strToHex($string);

    	//Create table to save Blocks on the first loading of Admin
    	//create_blocks_table_db($wpdb,$blocks_table_name);
    	//Create table to save Transactions on the first loading of Admin
    	//create_transactions_table_db($wpdb,$transactions_table_name);
    	//Create table to save currency exchange from http://free.currencyconverterapi.com/api/v5/convert?q=EUR_USD&compact=y
    	//create_exchange_to_usd_table_db($wpdb,$wc_currency,$exchange_table_name);

    	//update_exchange_to_usd_table_db($wpdb,$wc_currency,$exchange_table_name,1);
    	//API to get Informations of Blocks, Transactions
    	$url = 'https://rinkeby.infura.io/fNuraoH3vBZU8d4MTqdt';

    	//insert latest Block on the first loading of Admin, ignore all Blocks before
    	//init_blocks_table_db($wpdb,$url,$blocks_table_name,$transactions_table_name,$admin_wallet_address);

    	//scan from this block number
    	$start_block_number=get_max_block_number_db($wpdb,$blocks_table_name) +1;
    	$start_block_number=2285550; //testing transaction at 2285555
    	for ($scan_block_number=$start_block_number;
    		//$scan_block_number<=$start_block_number+$blocks_loaded_each_request;
    		$scan_block_number<=$start_block_number+$blocks_loaded_each_request; //test
    		$scan_block_number++)
    	{
    		$hex_scan_block_number="0x".strval(dechex($scan_block_number)); //convert to hex
    		$block=get_block_by_number($url,$hex_scan_block_number);	//get Block by number with API
    		$block_content=$block['result'];
    		if (!$block_content) break;	//Stop scanning at a empty block, still not avaiable
    		//put Block to Database, table $blocks_table_name
    		insert_block_db($this->connection,$block_content,$blocks_table_name,$transactions_table_name,$admin_wallet_address);
    	}

    	// keep $min_blocks_saved_db Blocks, and delete the oldest blocks, in Admin Setting
    	delete_old_blocks_db($this->connection,$blocks_table_name,$min_blocks_saved_db,$max_blocks_saved_db);
    }

    ///////////////////////////////////////////////////
}
?>

<?php
namespace Fredo\Nexty\Helper;
class Exchange extends \Magento\Framework\App\Helper\AbstractHelper{
	//Ether 1027 Nexty 2714
	public $coin_id=1027;
	public function coinmarketcap_exchange($text_to,$amount){
		$id_from="1027";
		$str="https://api.coinmarketcap.com/v2/ticker/".$id_from."/?convert=".$text_to;
		$result=json_decode((file_get_contents($str)),true);
		$upper_text_to=strtoupper($text_to);

		return $amount/$result['data']['quotes'][$upper_text_to]['price'];
	}

}
?>

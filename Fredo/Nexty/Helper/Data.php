<?php
namespace Fredo\Nexty\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
      $this->_scopeConfig = $scopeConfig;
    }

    public function strToHex($string){

      	$hex = '';
      	for ($i=0; $i<strlen($string); $i++){
      		$ord = ord($string[$i]);
      		$hexCode = dechex($ord);
      		$hex .= substr('0'.$hexCode, -2);
      	}
      	return strToLower($hex);

    }

    public function getQRCode($walletAddress,$order_id,$order_total)
    {
        $QRtext='{"walletaddress": "'.$walletAddress.'","uoid": "'.$order_id.'","amount": "'.$order_total.'"}  ';
        $QRtext_hex="0x".$this->strToHex($QRtext);
        $QRtextencode= urlencode ( $QRtext_hex );
        return $QRtextencode;
    }

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
                $config_path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
    }
}
?>

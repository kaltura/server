<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( __DIR__ . "/kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class helperAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		$secret = "";
		$str = $this->getP ( "str" );
		$algo = $this->getP ( "algo" , "wiki_decode_no_serialize" );
		$res = "";
		$key = null;
		
		if ( $algo == "wiki_encode" )
		{
			$res = str_replace ( array ( "|" , "/") , array ( "|01" , "|02" ) , base64_encode ( serialize ( $str ) ) ) ; 
		}
		elseif ( $algo == "wiki_decode_no_serialize" )
		{
			$res = base64_decode (str_replace ( array ( "|02" , "|01" ) , array ( "/" , "|" ) , $str ) ) ;
		}
		elseif ( $algo == "base64_encode" )
		{
			$res = base64_encode($str )		;
		}
		elseif ( $algo == "base64_decode" )
		{
			$res = base64_decode($str )		;
		}
		elseif ( $algo == "base64_3des_encode" )
		{
			$key = $this->getP ( "des_key" );
			$encrypted_data = KCryptoWrapper::encrypt_3des($str, $key);
	    
			$res = base64_encode($encrypted_data)		;
			$this->des_key = $key;
		}
		elseif ( $algo == "base64_3des_decode" )
		{
			$key = $this->getP ( "des_key" );
			$input = base64_decode ( $str );
			$decrypted_data = KCryptoWrapper::decrypt_3des($input, $key);
	    
			$res = ($decrypted_data )		;
			$this->des_key = $key;
		}
		elseif ( $algo == "ks" )
		{
			$ks = ks::fromSecureString ( $str );
			$res = print_r ( $ks , true );
			if ( $ks != null )
			{
				$expired = $ks->valid_until;
				$expired_str = self::formatThisData($expired);
				$now = time();
				$now_str = self::formatThisData($now);
				$res .= "<br>" . "valid until: " . $expired_str . "<br>now: $now ($now_str)";
			} 
		}
		elseif ( $algo == "kwid" )
		{
			$kwid_str = @base64_decode( $str );
			if ( ! $kwid_str)
			{
				// invalid string
				return "";
			}
/*			$kwid = new kwid();
			list ( $kwid->kshow_id , $kwid->partner_id , $kwid->subp_id ,$kwid->article_name  ,$kwid->widget_id , $kwid->hash  ) =
				 @explode ( self::KWID_SEPARATOR , $str );
*/
			$cracked = @explode ( "|" , $kwid_str );
			$names = array ( "kshow_id" , "partner_id" , "subp_id" , "article_name" , "widget_id" , "hash" );
			$combined = array_combine( $names , $cracked );
			
			$secret = $this->getP ( "secret" );
			$md5 = md5 ( $combined["kshow_id"]  . $combined["partner_id"]  . $combined["subp_id"] . $combined["article_name"] . 
				$combined["widget_id"] .  $secret );
				
			$combined["secret"] = $secret;
			$combined["calculated hash"] = substr ( $md5 , 1 , 10 );
			
			$res = print_r ( $combined , true );
		}
		elseif ( $algo == "ip" )
		{
			$ip_geo = new myIPGeocoder();
			if ( $str )
				$remote_addr = $str;
			else
				$remote_addr = requestUtils::getRemoteAddress();
			$res = $ip_geo->iptocountry( $remote_addr );
		}
		
				
		$this->key = $key;
		$this->secret = $secret;
		$this->str = $str;
		$this->res = $res;
		$this->algo = $algo;
	}
	
	private static function formatThisData ( $time )
	{
		return strftime( "%d/%m %H:%M:%S" , $time );	
	}
}
?>

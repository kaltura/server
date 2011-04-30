<?php
class KalturaInternalToolsPluginSystemHelperAction extends KalturaAdminConsolePlugin
{
	
	public function __construct()
	{
		$this->action = 'KalturaInternalToolsPluginSystemHelper';
		$this->label = 'System Helper';
		$this->rootLabel = 'Developer';
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_INTERNAL);
	}

	
	public function doAction(Zend_Controller_Action $action)
	{
		
		$request = $action->getRequest();
		
		$SystemHelperForm = new Form_SystemHelper();
		$SystemHelperFormResult = new Form_SystemHelperResult();
		
		$algo ="";
		$secret = "";
		$str = $request->getParam('StringToManipulate', false);
		$algo = $request->getParam('Algorithm', false);
		$key = $request->getParam('des_key',false);
		$secret = $request->getParam('secret',false);
		
		$res = "";
		
		
		if ( $algo == "wiki_encode" )
		{
			$res = str_replace ( array ( "|" , "/") , array ( "|01" , "|02" ) , base64_encode ( serialize ( $str ) ) ) ; 
		}
		elseif ( $algo == "wiki_decode" )
		{
			$res = @unserialize ( base64_decode (str_replace ( array ( "|02" , "|01" ) , array ( "/" , "|" ) , $str ) ) ) ;
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
			$input = $str ;
			$td = mcrypt_module_open('tripledes', '', 'ecb', '');
	    	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	    	$key = substr($key, 0, mcrypt_enc_get_key_size($td));
	    	
	    	mcrypt_generic_init($td, $key, $iv);
	    	$encrypted_data = mcrypt_generic($td, $input);
	    	mcrypt_generic_deinit($td);
	    	mcrypt_module_close($td);
	    
			$res = base64_encode($encrypted_data )		;
			$this->des_key = $key;
		}
		elseif ( $algo == "base64_3des_decode" )
		{
			//echo "[$key]";
			$input = base64_decode ( $str );
			$td = mcrypt_module_open('tripledes', '', 'ecb', '');
	    	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	    	$key = substr($key, 0, mcrypt_enc_get_key_size($td));
	    	
	    	mcrypt_generic_init($td, $key, $iv);
	    	$encrypted_data = mdecrypt_generic($td, $input);
	    	mcrypt_generic_deinit($td);
	    	mcrypt_module_close($td);
	    
			$res = ($encrypted_data )		;
			$this->des_key = $key;
		}
		elseif ( $algo == "ks" )
		{			
			//$ks = ks::fromSecureString ( $str ); // to do ->api Extension
			$client = Infra_ClientHelper::getClient();
			$internalToolsPlugin = Kaltura_Client_KalturaInternalTools_Plugin::get($client);
			$ks = null;
			
			try{
				$ks = $internalToolsPlugin->kalturaInternalToolsSystemHelper->fromSecureString($str);
				$res = print_r ( $ks , true );
			}
			catch(Kaltura_Client_Exception $e){
				$res = $e->getMessage();
			}
			 
			if (!is_null($ks))
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
			$cracked = @explode ( "|" , $kwid_str );
			$names = array ( "kshow_id" , "partner_id" , "subp_id" , "article_name" , "widget_id" , "hash" );
			$combined = array_combine( $names , $cracked );
			
			$md5 = md5 ( $combined["kshow_id"]  . $combined["partner_id"]  . $combined["subp_id"] . $combined["article_name"] . 
				$combined["widget_id"] .  $secret );
				
			$combined["secret"] = $secret;
			$combined["calculated hash"] = substr ( $md5 , 1 , 10 );
			
			$res = print_r ( $combined , true );
		}
		elseif ( $algo == "ip" )
		{
			//$ip_geo = new myIPGeocoder();// to do ->api Extension
			$client = Infra_ClientHelper::getClient();
			$internalToolsPlugin = Kaltura_Client_KalturaInternalTools_Plugin::get($client);
			if ( $str )
				$remote_addr = $str;
			else
			{
				//$remote_addr = requestUtils::getRemoteAddress();// to do ->api Extension
				$remote_addr = $internalToolsPlugin->KalturaInternalToolsSystemHelper->getRemoteAddress();
			} 
			//$res = $ip_geo->iptocountry( $remote_addr );
			$res = $internalToolsPlugin->KalturaInternalToolsSystemHelper->iptocountry($remote_addr);
		}
		
				
		$action->view->key = $key;
		$action->view->secret = $secret;
		$action->view->str = $str;
		$SystemHelperFormResult->getElement('results')->setValue($res);
		$action->view->SystemHelperFormResult = $SystemHelperFormResult;
		$action->view->algo = $algo;
		
		
	}
	
	private static function formatThisData ( $time )
	{
		return strftime( "%d/%m %H:%M:%S" , $time );	
	}
}


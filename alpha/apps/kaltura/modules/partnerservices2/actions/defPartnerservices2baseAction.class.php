<?php
require_once ( "myMultiRequest.class.php");
class defPartnerservices2baseAction extends kalturaAction
{
	protected static $_useCache = true;	
	
	public static function disableCache()
	{
		self::$_useCache = false;
	}
	
	public function execute()
	{
		// can't read using $_REQUEST because the 'myaction' paramter is created in a routing.yml rule
		$service_name = $this->getRequestParameter( "myaction" );

		// remove all '_' and set to lowercase
		$myaction_name = strtolower( str_replace ( "_" , "" , $service_name ) );
		$clazz_name = $myaction_name . "Action";
//		echo "[$myaction_name] [$clazz_name]<br>";

//		$clazz = get_class ( $clazz_name );

		//$multi_request = $this->getRequestParameter( "multirequest" , null );
		$multi_request = $myaction_name ==  "multirequest" ;
		if ( $multi_request  )
		{
			$multi_request = new myMultiRequest ( $_REQUEST, $this );
			$response = $multi_request->execute();
		}
		else
		{
			$include_result = include_once ( "{$clazz_name}.class.php");
			if ( $include_result )
			{
				$myaction = new $clazz_name( $this );
				$myaction->setInputParams ( $_REQUEST );
				$response = $myaction->execute( );
			}
			else
			{
				$format = $this->getP ( "format" );
				$response = "Error: Invalid service [$service_name]";
			}
		}

		$format = $this->getP ( "format" );
		if ( $format == kalturaWebserviceRenderer::RESPONSE_TYPE_PHP_ARRAY || $format == kalturaWebserviceRenderer::RESPONSE_TYPE_PHP_OBJECT )
		{
			//$this->setHttpHeader ( "Content-Type" , "text/html; charset=utf-8" );
			$response = "<pre>" . print_r ( $response , true ) . "</pre>" ;
		}

		// uncomment in order to cache api responses
	if(kConf::get('enable_cache'))
	{
		$this->cacheResponse($response);
	}

        return $this->renderText( $response );
	}

	protected function shouldCacheResonse()
	{
		return self::$_useCache;	 
	}
	
	public function cacheResponse($response)
	{
		if (!$this->shouldCacheResonse() )
		{
			return;	
		}
		$isStartSession = (@$params['service'] == 'startsession' || strpos($_SERVER['PATH_INFO'],'startsession'));		

		$params = $_GET + $_POST;
		
		$ks = isset($params['ks']) ? $params['ks'] : '';
		if ($ks)
		{ 
			$ksData = $this->getKsData($ks);
			$uid = @$ksData["userId"];
			$validUntil = @$ksData["validUntil"];
		}
		else
		{
			$uid = @$params['uid'];
			$validUntil = 0;
		}
		
		if ($validUntil && $validUntil < time())
			return;
			
		if ($uid != "0" && $uid != "" && !$isStartSession)
			return;
	
		unset($params['ks']);
		unset($params['kalsig']);
		$params['uri'] = $_SERVER['PATH_INFO'];
		ksort($params);
		
		$keys = array_keys($params);
		$key = md5(implode("|", $params).implode("|", $keys));
	
		file_put_contents("/tmp/cache-$key.log", "cachekey: $key\n".print_r($params, true)."\n".$response); // sync - OK
		file_put_contents("/tmp/cache-$key.headers", $this->getResponse()->getHttpHeader  ( "Content-Type" )); // sync - OK
		file_put_contents("/tmp/cache-$key", $response); // sync - OK
	}

	public function setHttpHeader ( $hdr_name , $hdr_value  )
	{
		$this->getResponse()->setHttpHeader ( $hdr_name , $hdr_value  );
	}
	
	private function getKsData($ks)
	{
		$str = base64_decode($ks, true);
		
		if (strpos($str, "|") === false)
		{
			$partnerId = null;
			$userId = null;
			$validUntil = null;
		}
		else
		{
			@list($hash, $realStr) = @explode("|", $str, 2);
			@list($partnerId, $dummy, $validUntil, $dummy, $dummy, $userId, $dummy) = @explode (";", $realStr);
		}
		return array("partnerId" => $partnerId, "userId" => $userId, "validUntil" => $validUntil );
	}
}


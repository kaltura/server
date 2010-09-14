<?php

// TODO - maybe combine with requestUtils ?
class myResponseUtils
{
	const A_LONG_TIME_FROM_NOW = 100000000;
	 
	static public function setCacheHeadersNoCache ( $response)
	{
		$d = gmdate ( "D, d M Y h:i:s" );
		$response->setHttpHeader ( "Last-Modified" , $d . "GMT" );
		$response->setHttpHeader ( "Expires" ,  $d . "GMT" );
		$response->setHttpHeader ( "Cache-Control" , "no-store, no-cache, must-revalidate ");
		$response->setHttpHeader ( "Cache-Control" , " post-check=0, pre-check=0" );
		$response->setHttpHeader ( "Pragma" , "no-cache" );
	}
	
	static public function setCacheHeaders ( $response,  $last_modified , $expirey_in_seconds )
	{
		$response->setHttpHeader ( "Last-Modified" , gmdate ( "D, d M Y h:i:s" , $last_modified ) . "GMT" );
		$expires = time() + $expirey_in_seconds;
		$response->setHttpHeader ( "Expires" ,  gmdate ( "D, d M Y h:i:s" , $expires ) . "GMT" );
		$response->setHttpHeader ( "Cache-Control" , "max-age=" . $expirey_in_seconds);
		$response->setHttpHeader ( "Pragma" , "" );  // important !! - does not work without this line !
	}
	
	static public function neverExpire ( $response )
	{
		self::setCacheHeaders ( $response , time(), self::A_LONG_TIME_FROM_NOW );
	}
	

	static public function hasPageExpired ( $response )
	{
		$if_modified_since = $response->getHttpHeader  ( "If-Modified-Since" );
		if ( $if_modified_since )
		{
         	$response->setStatusCode ('304', 'Not Modified'); 
			$response->setHttpHeader('Status', 'Not Modified - 304'); 
			return false;
		}
		return true;
	}
	
	static public function createRedirectUrl( $url )
	{
		if ($_SERVER["HTTP_HOST"] == kConf::get ( "apphome_url_no_protocol" ) )
			return $url;
		else
			return kConf::get ( "apphome_url" ) . "/index.php/extservices/redirect?url=".urlencode($url)."&return_to=".$_SERVER["HTTP_HOST"];
	}
}
?>
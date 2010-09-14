<?php

/**
 * Will hold helper functions and conventions for working with the HttpRequest object
 *
 */
class requestUtils
{
	const SECURE_COOKIE_PREFIX = "___";
	
	private static $s_cookies_to_be_set = array();
	
	static public function isPost ( $context )
	{
		return ($context->getRequest()->getMethod() == sfRequest::POST) ;
	}

	static public function getParameter ( $param_name , $value_if_missing = NULL , $update_request_with_value = false )
	{
		if ( array_key_exists( $param_name , $_REQUEST ) )
		{
			return $search_mode = $_REQUEST[$param_name];
		}
	 	else
	 	{
	 		if ( isset ( $value_if_missing) )
	 		{
	 			if ( $update_request_with_value )
	 			{
	 				// in this case - from this point onwards - the value will be the new value
					// TODO - this is a nasty solution - should remove ?? 
					// modifying such a parameter as if recieved from the user is very error-prone !! 
	 				$_REQUEST[$param_name] = $value_if_missing;
	 			}
	 			return $value_if_missing;
	 		}
	 		else
	 		{
	 			// the parameter does not exist and there is no default value - 
	 			// return what the trivial method would ...
	 			// TODO - do we wnat some better default value to return  ?? 
	 			return @$_REQUEST[$param_name];
	 		}
	 	}
	}
	
	// TODO - implement a generic method to be used by getGetParam, getPostParam , getCookie ...
	static private function getWithDefault ( )
	{

	}


	public static function getHost ( )
	{
		$url = 'http';
		$url .= isset ( $_SERVER['HTTPS'] ) ? ( @$_SERVER['HTTPS']=='on' ? 's' : '' ) : "";
		$url .= '://' ;
		// $url .= .$_SERVER['SERVER_PORT'];

		$host =  @$_SERVER['HTTP_HOST'];
		if ( ! $host )
			$host = @$_SERVER['argv'][1];
		$url .= $host;
		
		return $url;
	}
	
	public static function getCdnHost ($protocol = 'http')
	{
		return "$protocol://".kConf::get("cdn_host");
	}
	
	public static function getRtmpUrl ( )
	{
		return kConf::get("rtmp_url");
	}
	
	public static function getIisHost ($protocol = 'http')
	{
		return "$protocol://".kConf::get("iis_host");
	}
	
	// TODO - see how can rewrite better code for the doc-root of the application !!
	public static function  getWebRootUrl( $include_host = true )
	{
		$url = "";
		if ( $include_host )
		{
			$url = self::getHost();
			$url = preg_replace("/www\d\.kaltura\.com/", "www.kaltura.com", $url);
			$url = preg_replace("/kaldev\d\.kaltura\.com/", "kaldev.kaltura.com", $url);
			$url = preg_replace("/sandbox\d\.kaltura\.com/", "sandbox.kaltura.com", $url);
		}

		$request_url = self::requestUri();
		$pos = strpos( $request_url, '/');
		// find the second slash - that's the end of the rood dir
		$pos = strpos( $request_url, '/' , $pos +1 );
		if ( $pos > 0 )
		{
			return $url .= substr($request_url,0,$pos+1);
		}
		else
		{
			return $url . "/" ;
		}
	}

	// found bits and peaces from Ling's code (the Contact-Importer author) and http://il2.php.net/reserved.variables
	// for $_SERVER['HTTP_HOST'] to work - have to add to apache's httpd.conf : ExtendedStatus On
	private static function requestUri()
	{
		if (isset($_SERVER['REQUEST_URI']))
		{
			$uri = $_SERVER['REQUEST_URI'];
		}
		else
		{
			if (isset($_SERVER['argv']))
			{
				$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
			}
			else
			{
				$uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
			}
		}
		return $uri;
	}
	
	
	public static function setSecureCookie ( $name , $value , $iv , $expiry)
	{
/* TODO - SECURITY - encrypt data !		
		$td = mcrypt_module_open ('tripledes', '', 'ecb', '');
		mcrypt_generic_init ($td, $key, $iv);
		$c_t = mcrypt_generic ($td, $value);
		mcrypt_module_close ($td);
		$token=base64_encode($c_t);
*/		
		$token = $value;
		
		// set the value of the cookie in the static map to be found if searched in current request
		self::$s_cookies_to_be_set[$name] = $value;
		setcookie( self::getHashedName($name) , $token , time() + $expiry , "/");
	}
	
	
	public static function  getSecureCookie ( $name , $iv  )
	{
		$raw_val = @self::$s_cookies_to_be_set[$name];
		if ( empty ( $raw_val ) )
		{
			$raw_val = @$_COOKIE[self::getHashedName($name)];
			if ( empty ( $raw_val ) )
				return NULL;
		}
		
		return $raw_val;
		
/* TODO - SECURITY - encrypt data !			
		$td = mcrypt_module_open ('tripledes', '', 'ecb', '');
		mcrypt_generic_init ($td, $key, $iv);
		$c_t = mdecrypt_generic ($td, $value);
		mcrypt_module_close ($td);
		$token=base64_decode($c_t);
			
		return $token;
*/
	}
	
	public static function getSecureCookieName ( $name )
	{
		 return self::getHashedName($name) ;
	}

	public static function removeAllSecureCookies ( )
	{
		$cookies = $_COOKIE;
		$name = null;
		foreach ( $cookies as $name => $value )
		{
			if ( kString::beginsWith( $name , self::SECURE_COOKIE_PREFIX ) )
			{
				self::removeSecureCookieByName ( $name );
			}
		}
		if ( $name )
		{
			setcookie( self::getHashedName($name) , "" , 0 , "/" );
		}		
	}
	
	public static function removeSecureCookie ( $name )
	{
		setcookie( self::getHashedName($name) , "" , 0 , "/" );		
	}
	
	public static function removeSecureCookieByName ( $real_name )
	{
		setcookie( $real_name , "" , 0 , "/" );		
	}
	
	private static function getHashedName( $name ) 
	{
		// TODO- security
		return self::SECURE_COOKIE_PREFIX . $name;
//		return self::SECURE_COOKIE_PREFIX . md5("bigbag$name);
	}
	
	public static function getRequestHost()
	{
		return "http://".kConf::get("www_host");
	}
	
	public static function getRequestHostId()
	{
		$domainId = kConf::get("www_host");
		
		if ( $domainId == 'localhost')
			$domainId = 2;
		elseif ($domainId ==  'kaldev.kaltura.com')
			$domainId = 0;
		elseif ($domainId ==  'sandbox.kaltura.com')
			$domainId = 3;
		elseif ($domainId ==  'www.kaltura.com')
			$domainId = 1;
			
		return $domainId;
	}
	
	public static function getStreamingServerUrl()
	{
		$domain = self::getRequestHost();
		
		$rtmp_host = str_replace ( "http:" , "rtmp:" , $domain );
		return "$rtmp_host/oflaDemo"; 
	}
	
	public static function handleConditionalGet()
	{
		// limelight sends conditional gets even after receiving errors on previous call so we cant assume they already have a good cached content
		/*
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
		{
				while(FALSE !== ob_get_clean());
			header('HTTP/1.0 304 Not Modified');
			header("Cache-Control: public, max-age=604800");
			die;
		}
		*/
	}
	
	//
	// the function check the http range header and sets http response headers accordingly
	// an array of the start, end and length of the requested range is returned.
	// multiple ranges are not allowed
	public static function handleRangeRequest($full_content_length, $set_content_length_header = false)
	{
		$size = $full_content_length;
        $length = $size;           // Content length
        $start  = 0;               // Start byte
        $end    = $size - 1;       // End byte
        // Now that we've gotten so far without errors we send the accept range header
        /* At the moment we only support single ranges.
         * Multiple ranges requires some more work to ensure it works correctly
         * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
         *
         * Multirange support annouces itself with:
         * header('Accept-Ranges: bytes');
         *
         * Multirange content must be sent with multipart/byteranges mediatype,
         * (mediatype = mimetype)
         * as well as a boundry header to indicate the various chunks of data.
         */
        // header('Accept-Ranges: bytes');
        // multipart/byteranges
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
        if (isset($_SERVER['HTTP_RANGE']))
        {
			header("Accept-Ranges: 0-$length");
      	
			$c_start = $start;
			$c_end   = $end;
			// Extract the range string
			list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
			// Make sure the client hasn't sent us a multibyte range
			if (strpos($range, ',') !== false)
			{
				// (?) Shoud this be issued here, or should the first
				// range be used? Or should the header be ignored and
				// we output the whole content?
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				// (?) Echo some info to the client?
				exit;
			}
			// If the range starts with an '-' we start from the beginning
			// If not, we forward the file pointer
			// And make sure to get the end byte if spesified
			if ($range{0} == '-')
			{
				// The n-number of the last bytes is requested
				$c_start = $size - substr($range, 1);
			}
			else
			{
				$range  = explode('-', $range);
				$c_start = $range[0];
				$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
			}
			/* Check the range and make sure it's treated according to the specs.
			 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
			 */
			// End bytes can not be larger than $end.
			$c_end = ($c_end > $end) ? $end : $c_end;
			// Validate the requested range and return an error if it's not correct.
			if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size)
			{
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				header("Content-Range: bytes $start-$end/$size");
				// (?) Echo some info to the client?
		        exit;
			}
			$start  = $c_start;
			$end    = $c_end;
			$length = $end - $start + 1; // Calculate new content length
			header('HTTP/1.1 206 Partial Content');
			header("Content-Range: bytes $start-$end/$size");
		}
		// Notify the client the byte range we'll be outputting
		if ($set_content_length_header)
			header("Content-Length: $length");
        
		return array($start, $end, $length);
	}                  
	
	public static function sendCdnHeaders($ext, $content_length, $max_age = 8640000 , $mime = null, $private = false)
	{
		if ( $max_age === null ) $max_age = 8640000;
		while(FALSE !== ob_get_clean());
		
		if ( $mime == null )
		{
			if ($ext == "flv")
				$content_type ="video/x-flv";
			elseif ($ext == "mp4")
				$content_type ="video/mp4";
			elseif ($ext == "ogg")
				$content_type ="video/ogg";
			elseif ($ext == "mp3")
				$content_type ="audio/mpeg";
			elseif ($ext == "jpg")
				$content_type ="image/jpeg";
			elseif ($ext == "swf")
				$content_type ="application/x-shockwave-flash";
			else
				$content_type ="image/$ext";
		}
		else
		{
			$content_type = $mime ;
		}
		
		if ($max_age)
		{
			$cache_scope = $private ? "private" : "public";
			header("Cache-Control: $cache_scope, max-age=$max_age max-stale=0");
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . 'GMT'); 
			header('Last-Modified: Thu, 19 Nov 2000 08:52:00 GMT');
		}
		else
		{
			header("Cache-Control:");
			header("Expires: Thu, 19 Nov 2000 08:52:00 GMT");
		}
		
		header("Content-Length: $content_length ");
		header("Pragma:");
		header("Content-Type: $content_type");
	}
	

	public static function getRemoteAddress()
	{
		$remote_addr = null;
		if ( isset ( $_SERVER['HTTP_X_REAL_IP'] ))
		{
			$remote_addr = @$_SERVER['HTTP_X_REAL_IP'];
		}
			
		if (!$remote_addr && isset ( $_SERVER['HTTP_X_KALTURA_REMOTE_ADDR'] ) )
		{
			$remote_addr = @$_SERVER['HTTP_X_KALTURA_REMOTE_ADDR'];
		}
		
		// if still mepty .... 
		if (!$remote_addr)
			$remote_addr = $_SERVER['REMOTE_ADDR'];
		
		return $remote_addr;
	}
	
	public static function validateIp( $required_ip , &$remote_addr)
	{
		$remote_addr = self::getRemoteAddress();
		$longIP = ip2long( $remote_addr );// to convert back, use long2ip
		return  ( $required_ip == $remote_addr || $required_ip == $longIP );
	}
	
	public static function getIpCountry ( )
	{
		$remote_addr = self::getRemoteAddress();
		$ip_geo = new myIPGeocoder();
		$country = $ip_geo->iptocountry( $remote_addr );
		return $country;
	}

	// $ip_country_list - string separated by ','.
	// the current ip should be one of the countries in the list for the ip to be vlaid
	public static function matchIpCountry ( $ip_country_list_str , &$current_country )
	{
		$ip_country_list = explode ( "," , $ip_country_list_str );
		$current_country = self::getIpCountry() ;
		return ( in_array ( $current_country , $ip_country_list ) );
	}
	
	//
	// allow access only via cdn or via proxy from secondary datacenter
	//
	public static function enforceCdnDelivery($partnerId)
	{
		$host = requestUtils::getHost();
		$cdnHost = myPartnerUtils::getCdnHost($partnerId);

		$dc = kDataCenterMgr::getCurrentDc();
		$external_url = $dc["external_url"];

		// allow access only via cdn or via proxy from secondary datacenter
		if ($host != $cdnHost && $host != $external_url)
		{
			$uri = $_SERVER["REQUEST_URI"];
			if (strpos($uri, "/forceproxy/true") === false)
				$uri .= "/forceproxy/true/";
			
			header('Location:'.$cdnHost.$uri);
			header("X-Kaltura:enforce-cdn");
			
			die;
		}
	}
	
}

<?php
/**
 * IMPORTANT !!! This class should not depend on anything other than kConf (e.g. NOT KalturaLog)
 *
 * Will hold helper functions and conventions for working with the HttpRequest object
 *
 * @package server-infra
 * @subpackage request
 */
class infraRequestUtils
{
	const PROTOCOL_HTTP = 'http';
	const PROTOCOL_HTTPS = 'https';
	
	protected static $isInGetRemoteAddress = false;
	protected static $remoteAddress = null;
	protected static $requestParams = null;
	protected static $hostname = null;

	//
	// the function check the http range header and sets http response headers accordingly
	// an array of the start, end and length of the requested range is returned.
	// multiple ranges are not allowed
	public static function handleRangeRequest($full_content_length, $set_content_length_header = false)
	{
		$size = $full_content_length;
		$length = $size;		   // Content length
		$start  = 0;			   // Start byte
		$end	= $size - 1;	   // End byte
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
			$end	= $c_end;
			$length = $end - $start + 1; // Calculate new content length
			header('HTTP/1.1 206 Partial Content');
			header("Content-Range: bytes $start-$end/$size");
		}
		// Notify the client the byte range we'll be outputting
		if ($set_content_length_header)
			header("Content-Length: $length");
		
		return array($start, $end, $length);
	}

	public static function sendCachingHeaders($max_age = 864000, $private = false, $last_modified = null)
	{
		if ($max_age)
		{
			// added max-stale=0 to fight evil proxies
			$cache_scope = $private ? "private" : "public";
			header("Cache-Control: $cache_scope, max-age=$max_age, max-stale=0");
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . 'GMT');
			if ($last_modified)
				header('Last-modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . 'GMT');
			else
				header('Last-Modified: Sun, 19 Nov 2000 08:52:00 GMT');
		}
		else
		{
			header("Cache-Control:");
			header("Expires: Sun, 19 Nov 2000 08:52:00 GMT");
		}
	}
	
	public static function sendCdnHeaders($ext, $content_length, $max_age = 8640000 , $mime = null, $private = false, $last_modified = null)
	{
		if ( $max_age === null ) $max_age = 8640000;
		while(FALSE !== ob_get_clean());
		
		if ( $mime == null )
		{
			switch ($ext)
			{
				case "css":
					$content_type ="text/css";
					break;
				case "flv":
					$content_type ="video/x-flv";
					break;
				case "mp4":
					$content_type ="video/mp4";
					break;
				case "mov":
				case "qt":
					$content_type ="video/quicktime";
					break;
				case "webm":
					$content_type ="video/webm";
					break;
				case "ogg":
					$content_type ="video/ogg";
					break;
				case "mp3":
					$content_type ="audio/mpeg";
					break;
				case "jpg":
					$content_type ="image/jpeg";
					break;
				case "swf":
					$content_type ="application/x-shockwave-flash";
					break;
				case "m3u8":
					$content_type ="application/x-mpegURL";
					break;
				case "ts":
					$content_type ="video/MP2T";
					break;
						case "3gp":
								$content_type ="video/3gpp";
								break;
				default:
					$content_type ="image/$ext";
					break;
			}
		}
		else
		{
			$content_type = $mime ;
		}

		self::sendCachingHeaders($max_age, $private, $last_modified);
		
		header("Content-Length: $content_length ");
		header("Pragma:");
		header("Content-Type: $content_type");
	}

	public static function getSignedIpAddressHeader($ip = null)
	{
		if (!kConf::hasParam('remote_addr_header_salt'))
			return null;
			
		if (!$ip)
			$ip = self::getRemoteAddress();

		$salt = kConf::get('remote_addr_header_salt');
		$baseHeader = array(trim($ip), time(), microtime(true));
		$baseHeader = implode(',', $baseHeader);
		$ipHeader = $baseHeader . ',' . md5($baseHeader . ',' . $salt);
		return array('X_KALTURA_REMOTE_ADDR', $ipHeader);
	}
	
	public static function isIpPrivate($ip)
	{
		$privateRanges = array(
			'10.0.0.0|10.255.255.255',
			'172.16.0.0|172.31.255.255',
			'192.168.0.0|192.168.255.255',
			'169.254.0.0|169.254.255.255',
			'127.0.0.0|127.255.255.255',
		);
		
		$longIp = ip2long($ip);
		if ($longIp && $longIp != -1)
		{
			foreach ($privateRanges as $range)
			{
				list($start, $end) = explode('|', $range);
				if ($longIp >= ip2long($start) && $longIp <= ip2long($end)) {
					return true;
				}
			}
		}
		
		return false;
	}

	public static function getHostname()
	{
		if(self::$hostname)
			return self::$hostname;

		if(isset($_SERVER['HOSTNAME']))
			self::$hostname = $_SERVER['HOSTNAME'];

		if(is_null(self::$hostname))
			self::$hostname = gethostname();

		if(is_null(self::$hostname))
			self::$hostname = $_SERVER['SERVER_NAME'];

		if(is_null(self::$hostname))
			error_log('Host name is not defined, please define environment variable named HOSTNAME');

		return self::$hostname;
	}
	
	public static function getRemoteAddress()
	{
		if(self::$remoteAddress)
			return self::$remoteAddress;
			
		// Prevent call cycles in case KalturaLog will be used in internalGetRemoteAddress
		if (self::$isInGetRemoteAddress)
			return null;
		
		self::$isInGetRemoteAddress = true;
		self::$remoteAddress = self::internalGetRemoteAddress();
		self::$isInGetRemoteAddress = false;
		return self::$remoteAddress;
	}
	
	protected static function internalGetRemoteAddress()
	{
		if(self::$remoteAddress)
			return self::$remoteAddress;
			
		// enable access control debug
		if(isset($_POST['debug_ip']) && kConf::hasParam('debug_ip_enabled') && kConf::get('debug_ip_enabled'))
		{
			header('Debug IP: ' . $_POST['debug_ip']);
			return $_POST['debug_ip'];
		}
			
		$remote_addr = null;

		if (!$remote_addr && isset ( $_SERVER['HTTP_X_KALTURA_REMOTE_ADDR'] ) )
		{
			list($remote_addr, $time, $uniqueId, $hash) = @explode(",", $_SERVER['HTTP_X_KALTURA_REMOTE_ADDR']);
			
			if (kConf::hasParam('remote_addr_header_salt') && kConf::hasParam("remote_addr_header_timeout"))
			{
				$salt = kConf::get('remote_addr_header_salt');
				$timeout = kConf::get("remote_addr_header_timeout");
				
				if ($timeout) {
					// Compare the absolute value of the difference between the current time
					// and the "token" time.
					if (abs(time() - $time) > $timeout )
						die("REMOTE_ADDR header invalid time");
				}
				
				if ($hash !== md5("$remote_addr,$time,$uniqueId,$salt"))
				{
					die("REMOTE_ADDR header invalid signature");
				}
			}
		}
		
		// support getting the original ip address of the client when using the cdn for API calls (cdnapi)
		// validate either HTTP_HOST or HTTP_X_FORWARDED_HOST in case of a proxy
		if (!$remote_addr &&
			isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
			(isset($_SERVER['HTTP_HOST']) && 
			in_array($_SERVER['HTTP_HOST'], kConf::get('remote_addr_whitelisted_hosts') ) ||
			isset($_SERVER['HTTP_X_FORWARDED_HOST']) &&
			in_array($_SERVER['HTTP_X_FORWARDED_HOST'], kConf::get('remote_addr_whitelisted_hosts') ) ) )
		{
			// pick the first non private ip
			$headerIPs = trim($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
			$headerIPs = explode(',', $headerIPs);
			foreach ($headerIPs as $ip)
			{
				preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', trim($ip), $matches); // ignore any string after the ip address
				if (!isset($matches[0]))
					continue;
					
	 			$tempAddr = trim($matches[0]);
	 			if (self::isIpPrivate($tempAddr))	// verify that ip is not from a private range
	 				continue;
	 			
	 			$remote_addr = $tempAddr;
	 			break;
		 	}
		}

		// support passing ip when proxying through apache. check the proxying server is indeed an internal server
		if (!$remote_addr &&
				isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
				isset($_SERVER['HTTP_X_FORWARDED_SERVER']) &&
				kConf::hasParam('remote_addr_header_server') &&
				$_SERVER['HTTP_X_FORWARDED_SERVER'] == kConf::get('remote_addr_header_server') )
		{
			// pick the last ip
			$headerIPs = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
			$remote_addr = trim($headerIPs[count($headerIPs) - 1]);
		}
		
		// if still empty ....
		if (!$remote_addr)
			$remote_addr = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
		
		return $remote_addr;
	}
	
	public static function getProtocol()
	{
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			return self::PROTOCOL_HTTPS;
		
		$params = self::getRequestParams();
		if (isset($params['apiProtocol']) &&
			kConf::hasParam('https_param_salt') &&
			$params['apiProtocol'] == 'https_' . kConf::get('https_param_salt'))
			return self::PROTOCOL_HTTPS;
		
		return self::PROTOCOL_HTTP;
	}
	
	public static function parseUrlHost($url)
	{
		$urlDetails = parse_url($url);
		if(isset($urlDetails['host']))
		{
			$result = $urlDetails['host'];
		}
		elseif(isset($urlDetails['path']))
		{
			// parse_url could not extract domain, but returned path
			// we validate that this path could be considered a domain
			$result = rtrim($urlDetails['path'], '/'); // trim trailing slashes. example: www.kaltura.com/test.php
			
			// stop string at first slash. example: httpssss/google.com - malformed url...
			if (strpos($result, "/") !== false)
			{
				$result = substr($result, 0, strpos($result, "/"));
			}
		}
		else // empty path and host, cannot parse the URL
		{
			return null;
		}
		
		// some urls might return host or path which is not yet clean for comparison with user's input
		if (strpos($result, "?") !== false)
		{
			$result = substr($result, 0, strpos($result, "?"));
		}
		if (strpos($result, "#") !== false)
		{
			$result = substr($result, 0, strpos($result, "#"));
		}
		if (strpos($result, "&") !== false)
		{
			$result = substr($result, 0, strpos($result, "&"));
		}
		return $result;
	}

	public static function getRequestParams()
	{
		if (!is_null(self::$requestParams))
			return self::$requestParams;
		
		$scriptParts = explode('/', $_SERVER['SCRIPT_NAME']);
		$pathParts = array();
		if (isset($_SERVER['PHP_SELF']))
			$pathParts = explode('/', $_SERVER['PHP_SELF']);
		$pathParts = array_diff($pathParts, $scriptParts);
		
		$params = array();
		reset($pathParts);
		while(current($pathParts))
		{
			$key = each($pathParts);
			$value = each($pathParts);
			$params[$key['value']] = $value['value'];
		}
			
		self::$requestParams = array_merge($params, $_GET, $_POST, $_FILES);
		return self::$requestParams;
	}

	public static function dumpFilePart($file_name, $range_from, $range_length)
	{
		$chunk_size = 100000;
		$fh = fopen($file_name, "rb");
		if($fh)
		{
			$pos = 0;
			fseek($fh, $range_from);
			while($range_length > 0)
			{
				$content = fread($fh, min($chunk_size, $range_length));
				echo $content;
				$range_length -= $chunk_size;
			}
			fclose($fh);
		}
	}
}

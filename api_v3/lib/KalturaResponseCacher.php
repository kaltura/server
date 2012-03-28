<?php
require_once(dirname(__FILE__) . '/../../infra/kConf.php');
require_once(dirname(__FILE__) . '/../../alpha/apps/kaltura/lib/requestUtils.class.php');

class KalturaResponseCacher
{
	// warm cache constatns
	// cache warming is used to maintain continous use of the request caching while preventing a load once the cache expires
	// during WARM_CACHE_INTERVAL before the cache expiry a single request will be allowed to get through and renew the cache
	// this request named warm cache request will block other such requests for WARM_CACHE_TTL seconds

	// header to mark the request is due to cache warming. the header holds the original request protocol http/https
	const WARM_CACHE_HEADER = "X-KALTURA-WARM-CACHE";

	// interval before cache expiry in which to try and warm the cache
	const WARM_CACHE_INTERVAL = 60;

	// time in which a warm cache request will block another request from warming the cache
	const WARM_CACHE_TTL = 10;

	protected $_params = array();
	protected $_cacheFilePrefix = "cache_v3-";
	protected $_cacheDirectory = "/tmp/";
	protected $_cacheKey = "";
	protected $_cacheDataFilePath = "";
	protected $_cacheHeadersFilePath = "";
	protected $_cacheLogFilePath = "";
	protected $_cacheExpiryFilePath = "";
	protected $_ks = "";
	protected $_defaultExpiry = 600;
	protected $_expiry = 600;
	protected $_cacheHeadersExpiry = 60; // cache headers for CDN & browser - used  for GET request with kalsig param
	
	protected $_instanceId = 0;
	
	protected static $_useCache = array();		// contains instance ids that will use the cache
	protected static $_nextInstanceId = 0;
		
	public function __construct($params = null, $cacheDirectory = null, $expiry = 0)
	{
		$this->_instanceId = self::$_nextInstanceId;  
		self::$_nextInstanceId++;
		
		if ($expiry)
			$this->_defaultExpiry = $this->_expiry = $expiry;
			
		$this->_cacheDirectory = $cacheDirectory ? $cacheDirectory : 
			rtrim(kConf::get('response_cache_dir'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		
		$this->_cacheDirectory .= "cache_v3-".$this->_expiry . DIRECTORY_SEPARATOR;
		
		if (!kConf::get('enable_cache'))
			return;
			
		if (!$params) {
			$params = requestUtils::getRequestParams();
		}
		
		foreach(kConf::get('v3cache_ignore_params') as $name)
			unset($params[$name]);
		
		// check the clientTag parameter for a cache start time (cache_st:<time>) directive
		if (isset($params['clientTag']))
		{
			$clientTag = $params['clientTag'];
			$matches = null;
			if (preg_match("/cache_st:(\\d+)/", $clientTag, $matches))
			{
				if ($matches[1] > time())
				{
					self::disableCache();
					return;
				}
			}
		}
				
		$isAdminLogin = isset($params['service']) && isset($params['action']) && $params['service'] == 'adminuser' && $params['action'] == 'login';
		if ($isAdminLogin || isset($params['nocache']))
		{
			self::disableCache();
			return;
		}
		
		$ks = isset($params['ks']) ? $params['ks'] : '';
		foreach($params as $key => $value)
		{
			if(preg_match('/[\d]+:ks/', $key))
			{
				if (!$ks && strpos($value, ':result') === false)
					$ks = $value;
				unset($params[$key]);
			}
		}
			
		unset($params['ks']);
		unset($params['kalsig']);
		unset($params['clientTag']);
		unset($params['callback']);
		
		$this->_params = $params;
		$this->setKS($ks);

		self::$_useCache[] = $this->_instanceId;	
	}
	
	public function setKS($ks)
	{
		$this->_ks = $ks;
		
		$ksData = $this->getKsData();
		$this->_params["___cache___partnerId"] = $ksData["partnerId"];
		$this->_params["___cache___userId"] = $ksData["userId"];
		$this->_params['___cache___uri'] = $_SERVER['PHP_SELF'];
		$this->_params['___cache___protocol'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";

		// take only the hostname part of the referrer parameter of baseEntry.getContextData
		foreach ($this->_params as $key => $value)
		{
			if (strpos($key, 'contextDataParams:referrer') === false)
				continue;

			if (in_array($ksData["partnerId"], kConf::get('v3cache_include_referrer_in_key')))
				$this->_params[$key] = parse_url($value, PHP_URL_HOST);
			else
				unset($this->_params[$key]);
				
			break;
		}
		
		ksort($this->_params);

		$this->_cacheKey = md5( http_build_query($this->_params) );

		// split cache over 16 folders using the cachekey first character
		// this will reduce the amount of files per cache folder
		$pathWithFilePrefix = $this->_cacheDirectory . DIRECTORY_SEPARATOR . substr($this->_cacheKey, 0, 1) . DIRECTORY_SEPARATOR . $this->_cacheFilePrefix;
		$this->_cacheDataFilePath 		= $pathWithFilePrefix . $this->_cacheKey;
		$this->_cacheHeadersFilePath 	= $pathWithFilePrefix . $this->_cacheKey . ".headers";
		$this->_cacheLogFilePath 		= $pathWithFilePrefix . $this->_cacheKey . ".log";
		$this->_cacheExpiryFilePath 		= $pathWithFilePrefix . $this->_cacheKey . ".expiry";
	}
	
	public static function disableCache()
	{
		self::$_useCache = array();
	}

	/**
	 * This function checks whether the cache is disabled and returns the result.
	 */	
	public static function isCacheEnabled()
	{
		return count(self::$_useCache);
	}
	
	/**
	 * This functions checks if a certain response resides in cache.
	 * In case it dose, the response is returned from cache and a response header is added.
	 * There are two possibilities on which this function is called:
	 * 1)	The request is a single 'stand alone' request (maybe this request is a multi request containing several sub-requests)
	 * 2)	The request is a single request that is part of a multi request (sub-request in a multi request)
	 * 
	 * in case this function is called when handling a sub-request (single request as part of a multirequest) it
	 * is preferable to change the default $cacheHeaderName
	 * 
	 * @param $cacheHeaderName - the header name to add
	 * @param $cacheHeader - the header value to add
	 */	 
	public function checkCache($cacheHeaderName = 'X-Kaltura', $cacheHeader = 'cached-dispatcher')
	{
		if (!in_array($this->_instanceId, self::$_useCache))
			return false;
		
		$startTime = microtime(true);
		if ($this->hasCache())
		{
			$response = @file_get_contents($this->_cacheDataFilePath);
			if ($response)
			{
				$processingTime = microtime(true) - $startTime;
				header("$cacheHeaderName:$cacheHeader,$this->_cacheKey,$processingTime", false);
				return $response;
			}
		}
		
		return false;				
	}
	
		
	public function checkOrStart()
	{
		if (!in_array($this->_instanceId, self::$_useCache))
			return;
					
		$response = $this->checkCache();
		
		if ($response)
		{
			// TODO add kaltura warnings
			$contentTypeHdr = @file_get_contents($this->_cacheHeadersFilePath);
			if ($contentTypeHdr) {
				header($contentTypeHdr, true);
			}	

			// for GET requests with kalsig (signature of call params) return cdn/browser caching headers
			if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_REQUEST["kalsig"]))
			{
				$max_age = $this->_cacheHeadersExpiry;
				header("Cache-Control: private, max-age=$max_age max-stale=0");
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . 'GMT'); 
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . 'GMT');
			}
			else
			{
				header("Expires: Sun, 19 Nov 2000 08:52:00 GMT", true);
				header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0", true);
				header("Pragma: no-cache", true);
			}
	
			// for jsonp ignore the callback argument and replace it in result (e.g. callback_4([{...}]);
			if (@$_REQUEST["format"] == 9)
			{
				$callback = @$_REQUEST["callback"];
				$pos = strpos($response, "(");
				if ($pos)
				{
					$response = $callback.substr($response, $pos);
				}
			}

			echo $response;
			die;
		}
		else
		{
			ob_start();
		}
	}
	
		
	public function end()
	{
		if (!$this->shouldCache())
			return;
	
		$response = ob_get_contents();
		$headers = headers_list();
		$contentType = "";
		foreach($headers as $headerStr)
		{
			$header = explode(":", $headerStr);
			if (isset($header[0]) && strtolower($header[0]) == "content-type")
			{
				$contentType = $headerStr;
				break;	
			}
		}
		
		$this->storeCache($response, $contentType);
		
		ob_end_flush();
	}
	
	
	public function storeCache($response, $contentType = null)
	{
		if (!$this->shouldCache())
			return;

		// provide cache key in header unless the X-Kaltura header was already set with a value
		// such as an error code. the header is used for debugging but it also appears in the access_log
		// and there we rather show the error than the cach key

		$headers = headers_list();
		$foundHeader = false;
		foreach($headers as $header)
		{
			if (strpos($header, 'X-Kaltura') === 0)
			{
				$foundHeader = true;
				break;
			}
		}

		if (!$foundHeader)
			header("X-Kaltura:cache-key,".$this->_cacheKey);
	
		$this->createDirForPath($this->_cacheLogFilePath);
		$this->createDirForPath($this->_cacheDataFilePath);
			
		file_put_contents($this->_cacheLogFilePath, "cachekey: $this->_cacheKey\n".print_r($this->_params, true)."\n".$response);
		file_put_contents($this->_cacheDataFilePath, $response);
		if(!is_null($contentType)) {
			$this->createDirForPath($this->_cacheHeadersFilePath);
			file_put_contents($this->_cacheHeadersFilePath, $contentType);
		}

		// store specific expiry shorter than the default one
		if ($this->_expiry == $this->_defaultExpiry)
		{
			if (kConf::hasParam("v3cache_expiry"))
			{
				$partnerId = $this->_params["___cache___partnerId"];
				$expiryArr = kConf::get("v3cache_expiry");
				if (array_key_exists($partnerId, $expiryArr))
					$this->_expiry = $expiryArr[$partnerId];
			}
		}

		if ($this->_expiry != $this->_defaultExpiry)
			file_put_contents($this->_cacheExpiryFilePath, time() + $this->_expiry);
	}

	public function setExpiry($expiry)
	{
		$this->_expiry = $expiry;
	}
	
	private function createDirForPath($filePath)
	{
		$dirname = dirname($filePath);
		if (!is_dir($dirname))
		{
			mkdir($dirname, 0777, true);
		}
	}
	

	private function hasCache()
	{
                // if the request is for warming the cache, disregard the cache and run the request
		$warmCacheHeader = self::getRequestHeaderValue(self::WARM_CACHE_HEADER);

                if ($warmCacheHeader !== false)
                {
			// if the request triggering the cache warmup was an https request, fool the code to treat the current request as https as well 
			if ($warmCacheHeader == "https")
				$_SERVER["HTTPS"] = "on";
						
			// make a trace in the access log of this being a warmup call
			header("X-Kaltura:cached-warmup-$warmCacheHeader,".$this->_cacheKey);
			return false;
                }

		if (file_exists($this->_cacheDataFilePath))
		{
			// check for a specific expiry 
			$fileExpiry = @file_get_contents($this->_cacheExpiryFilePath);
			if ($fileExpiry)
			{
				$cacheTTL = $fileExpiry - time(); 
			}
			else // otherwise check for the "default" expiry
			{
				$cacheTTL = filemtime($this->_cacheDataFilePath) + $this->_expiry - time(); 
			}

			if($cacheTTL > 0)
			{
				if ($cacheTTL < self::WARM_CACHE_INTERVAL) // 1 minute left for cache, lets warm it
				{
					self::warmCache($this->_cacheDataFilePath);	
				}
				
				return true;
			}

			@unlink($this->_cacheDataFilePath);
			@unlink($this->_cacheHeadersFilePath);
			@unlink($this->_cacheLogFilePath);
			@unlink($this->_cacheExpiryFilePath);
			return false;
		}
		return false;
	}
	
	private function shouldCache()
	{
		if (!in_array($this->_instanceId, self::$_useCache))
			return false;
			
		$ks = null;
		try{
			$ks = kSessionUtils::crackKs($this->_ks);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			self::disableCache();
			return false;
		}
		
		if(!$ks)
			return true;
		
		// force caching of actions listed in kConf even if admin ks is used
		if(kConf::hasParam('v3cache_ignore_admin_ks'))
		{
			foreach(kConf::get('v3cache_ignore_admin_ks') as $partnerId => $params)
			{
				if ($ks->partner_id != $partnerId)
					continue;
					
				$ignoreParams = null;
				parse_str($params, $ignoreParams);

				$matches = 0;
				foreach($ignoreParams as $key => $value)
					if (isset($this->_params[$key]) && $this->_params[$key] == $value)
						$matches++;
				
				if ($matches == count($ignoreParams))
					return true;
			}
		}
        
        
		if ($ks->isAdmin() ||
			($ks->valid_until && $ks->valid_until < time()) ||	 // if ks has expired dont cache response
			($ks->user !== "0" && $ks->user !== null)) // $uid will be null when no session
		{
			self::disableCache();
			return false;
		}
		
		return true;
	}
	
	private function getKsData()
	{
		$str = base64_decode($this->_ks, true);
		
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

	private static function getRequestHeaderValue($headerName)
	{
		$headerName = "HTTP_".str_replace("-", "_", strtoupper($headerName));

		if (!isset($_SERVER[$headerName]))
			return false;

		return $_SERVER[$headerName];
	}


	private static function getRequestHeaders()
	{
		if(function_exists('apache_request_headers'))
			return apache_request_headers();
		
		foreach($_SERVER as $key => $value)
		{
			if(substr($key, 0, 5) == "HTTP_")
			{
				$key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
				$out[$key] = $value;
			}
		}
		return $out;
	}

	// warm cache by sending the current request asynchronously via a socket to localhost
	// apc is used to flag that an existing warmup request is already running. The flag has a TTL of 10 seconds, 
	// so in the case the warmup request failed another one can be ran after 10 seconds.
	// finalize IP passing (use getRemoteAddr code)
	// can the warm cache header get received via a warm request passed from the other DC?
	private function warmCache($key)
	{
		// require apc for checking whether warmup is already in progress
		if (!function_exists('apc_fetch'))
		return;

		$key = "cache-warmup-$key";

		// abort warming if a previous warmup started less than 10 seconds ago
		if (apc_fetch($key) !== false)
			return;

		// flag we are running a warmup for the current request
		apc_store($key, true, self::WARM_CACHE_TTL);

		$uri = $_SERVER["REQUEST_URI"];

		$fp = fsockopen('127.0.0.1', 80, $errno, $errstr, 1);

		if ($fp === false)
		{
			error_log("warmCache - Couldn't open a socket [".$uri."]", 0);
			return;
		}

		$method = $_SERVER["REQUEST_METHOD"];

		$out = "$method $uri HTTP/1.1\r\n";

		$sentHeaders = self::getRequestHeaders();
		$sentHeaders["Connection"] = "Close";

		// mark request as a warm cache request in order to disable caching and pass the http/https protocol (the warmup always uses http)
		$sentHeaders[self::WARM_CACHE_HEADER] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";

		// if the request wasn't proxied pass the ip on the X-FORWARDED-FOR header
		if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$sentHeaders["X-FORWARDED-FOR"] = $_SERVER['REMOTE_ADDR'];
			$sentHeaders["X-FORWARDED-SERVER"] = kConf::get('remote_addr_header_server');
		}

		foreach($sentHeaders as $header => $value)
		{
			$out .= "$header:$value\r\n";
		}

		$out .= "\r\n";

		if ($method == "POST")
		{
			$postParams = array();
			foreach ($_POST as $key => &$val) {
				if (is_array($val)) $val = implode(',', $val);
				{
					$postParams[] = $key.'='.urlencode($val);
				}
			}

			$out .= implode('&', $postParams);
		}

		fwrite($fp, $out);
		fclose($fp);
	}

}

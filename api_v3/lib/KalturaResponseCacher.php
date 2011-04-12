<?php
require_once(dirname(__FILE__) . '/../../alpha/config/kConf.php');
require_once(dirname(__FILE__) . '/../../alpha/apps/kaltura/lib/requestUtils.class.php');

class KalturaResponseCacher
{
	protected $_params = array();
	protected $_cacheFilePrefix = "cache_v3-";
	protected $_cacheDirectory = "/tmp/";
	protected $_cacheKey = "";
	protected $_cacheDataFilePath = "";
	protected $_cacheHeadersFilePath = "";
	protected $_cacheLogFilePath = "";
	protected $_ks = "";
	protected $_expiry = 600;
	
	protected static $_useCache = true;
	
	public function __construct($params = null, $cacheDirectory = null, $expiry = 0)
	{
		self::$_useCache = kConf::get('enable_cache');

		if ($expiry)
			$this->_expiry = $expiry;
			
		$this->_cacheDirectory = $cacheDirectory ? $cacheDirectory : 
			rtrim(kConf::get('response_cache_dir'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		
		$this->_cacheDirectory .= "cache_v3-$this->_expiry . DIRECTORY_SEPARATOR;
		
		if (!self::$_useCache)
			return;
			
		if (!$params) {
			$params = requestUtils::getRequestParams();
		}
		
		// check the clientTag parameter for a cache start time (cache_st:<time>) directive
		if (isset($params['clientTag']))
		{
			$clientTag = $params['clientTag'];
			$matches = null;
			if (preg_match("/cache_st:(\\d+)/", $clientTag, $matches))
			{
				if ($matches[1] > time())
				{
					self::$_useCache = false;
					return;
				}
			}
		}
				
		$isAdminLogin = isset($params['service']) && isset($params['action']) && $params['service'] == 'adminuser' && $params['action'] == 'login';
		if ($isAdminLogin || isset($params['nocache']))
		{
			self::$_useCache = false;
			return;
		}
		
		$ks = isset($params['ks']) ? $params['ks'] : '';
		foreach($params as $key => $value)
		{
			if(preg_match('/[\d]+:ks/', $key))
			{
				$ks = $value;
				unset($params[$key]);
			}
		}
			
		unset($params['ks']);
		unset($params['kalsig']);
		unset($params['clientTag']);
		
		$this->_params = $params;
		$this->setKS($ks);
	}
	
	public function setKS($ks)
	{
		$this->_ks = $ks;
		
		$ksData = $this->getKsData();
		$this->_params["___cache___partnerId"] = $ksData["partnerId"];
		$this->_params["___cache___userId"] = $ksData["userId"];
		$this->_params['___cache___uri'] = $_SERVER['PHP_SELF'];
		$this->_params['___cache___protocol'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";
		ksort($this->_params);

		$this->_cacheKey = md5( http_build_query($this->_params) );

		$pathWithFilePrefix = $this->_cacheDirectory . DIRECTORY_SEPARATOR . $this->_cacheFilePrefix;
		$this->_cacheDataFilePath 		= $pathWithFilePrefix . $this->_cacheKey;
		$this->_cacheHeadersFilePath 	= $pathWithFilePrefix . $this->_cacheKey . ".headers";
		$this->_cacheLogFilePath 		= $pathWithFilePrefix . $this->_cacheKey . ".log";
	}
	
	public static function disableCache()
	{
		self::$_useCache = false;
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
		if (!self::$_useCache)
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
		if (!self::$_useCache)
			return;
					
		$response = $this->checkCache();
		
		if ($response)
		{
			$contentTypeHdr = @file_get_contents($this->_cacheHeadersFilePath);
			if ($contentTypeHdr) {
				header($contentTypeHdr, true);
			}	
			header("Expires: Sun, 19 Nov 2000 08:52:00 GMT", true);
			header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0", true);
			header("Pragma: no-cache", true);
			
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
			if (isset($header[0]) && $header[0] == "Content-Type")
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
	
		$this->createDirForPath($this->_cacheLogFilePath);
		$this->createDirForPath($this->_cacheDataFilePath);
			
		file_put_contents($this->_cacheLogFilePath, "cachekey: $this->_cacheKey\n".print_r($this->_params, true)."\n".$response);
		file_put_contents($this->_cacheDataFilePath, $response);
		if(!is_null($contentType)) {
			$this->createDirForPath($this->_cacheHeadersFilePath);
			file_put_contents($this->_cacheHeadersFilePath, $contentType);
		}
	}
	
	private function createDirForPath($filePath)
	{
		$dirname = dirname($filePath);
		if (!is_dir($dirname))
		{
			mkdir($dirname, 0777);
		}
	}
	
	
	private function hasCache()
	{
		if (file_exists($this->_cacheDataFilePath))
		{
			if (filemtime($this->_cacheDataFilePath) + $this->_expiry < time())
			{
				@unlink($this->_cacheDataFilePath);
				@unlink($this->_cacheHeadersFilePath);
				@unlink($this->_cacheLogFilePath);
				return false;
			}
			else
			{
				return true;
			}
		}
		return false;
	}
	
	private function shouldCache()
	{
		if (!self::$_useCache)
			return false;
			
		$ks = null;
		try{
			$ks = kSessionUtils::crackKs($this->_ks);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			return false;
		}
		
		if(!$ks)
			return true;
		
		if ($ks->isAdmin())
			return false;
	
		if ($ks->valid_until && $ks->valid_until < time()) // if ks has expired dont cache response
			return false;
	
		if ($ks->user === "0" || $ks->user === null) // $uid will be null when no session
			return true;
			
		return false;
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
}

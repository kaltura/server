<?php
/**
 * @package api
 * @subpackage cache
 */
require_once(dirname(__FILE__) . '/../../infra/kConf.php');
require_once(dirname(__FILE__) . '/../../infra/cache/kApiCache.php');
require_once(dirname(__FILE__) . '/../../alpha/apps/kaltura/lib/requestUtils.class.php');

/**
 * @package api
 * @subpackage cache
 */
class KalturaResponseCacher extends kApiCache
{
	// copied from KalturaResponseType
	const RESPONSE_TYPE_XML = 2;
	const RESPONSE_TYPE_PHP = 3;

	// warm cache constants
	// cache warming is used to maintain continous use of the request caching while preventing a load once the cache expires
	// during WARM_CACHE_INTERVAL before the cache expiry a single request will be allowed to get through and renew the cache
	// this request named warm cache request will block other such requests for WARM_CACHE_TTL seconds

	// header to mark the request is due to cache warming. the header holds the original request protocol http/https
	const WARM_CACHE_HEADER = "X-KALTURA-WARM-CACHE";

	// interval before cache expiry in which to try and warm the cache
	const WARM_CACHE_INTERVAL = 60;

	// time in which a warm cache request will block another request from warming the cache
	const WARM_CACHE_TTL = 10;
		
	// cache modes
	const CACHE_MODE_ANONYMOUS = 1;				// anonymous caching should be performed - the cached response will not be associated with any conditions
	const CACHE_MODE_CONDITIONAL = 2;			// cache the response along with its matching conditions
		
	const EXPIRY_MARGIN = 300;

	const CACHE_DELIMITER = "\r\n\r\n";
	
	const SUFFIX_DATA =  '.cache';
	const SUFFIX_RULES = '.rules';
	const SUFFIX_LOG = '.log';
	
	protected $_cacheStore = null;
	protected $_defaultExpiry = 600;
	protected $_cacheHeadersExpiry = 60; // cache headers for CDN & browser - used  for GET request with kalsig param
	protected $_contentType = null;
		
	protected $_cacheId = null;							// the cache id ensures that the conditions are in sync with the response buffer
	
	protected $_wouldHaveUsedCondCache = false;			// XXXXXXX TODO: remove this
	
	protected static $_cacheWarmupInitiated = false;
	
	public function __construct($params = null, $cacheType = kCacheManager::FS_API_V3, $expiry = 0)
	{
		$this->_cacheKeyPrefix = 'cache_v3-';
		
		parent::__construct();
	
		if ($expiry)
			$this->_defaultExpiry = $this->_expiry = $expiry;
			
		if (!kConf::get('enable_cache'))
			return;
			
		if (!$params) {
			$params = requestUtils::getRequestParams();
		}
		
		self::handleSessionStart($params);
		
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
				
		if (isset($params['nocache']))
		{
			self::disableCache();
			return;
		}
		
		$this->_cacheStore = kCacheManager::getCache($cacheType);
		if (!$this->_cacheStore)
		{
			self::disableCache();
			return;
		}
		
		$ks = isset($params['ks']) ? $params['ks'] : '';
		foreach($params as $key => $value)
		{
			if(!preg_match('/[\d]+:ks/', $key))
				continue;				// not a ks

			if (strpos($value, ':result') !== false)
				continue;				// the ks is the result of some sub request

			if ($ks && $ks != $value)
			{
				self::disableCache();	// several different ks's in a multirequest - don't use cache
				return;
			}

			$ks = $value;
			unset($params[$key]);
		}
			
		unset($params['ks']);
		unset($params['kalsig']);
		unset($params['clientTag']);
		unset($params['callback']);
		
		$this->_params = $params;
		$this->setKS($ks);

		$this->enableCache();
	}
	
	public function setKS($ks)
	{
		// if the request triggering the cache warmup was an https request, fool the code to treat the current request as https as well 
		$warmCacheHeader = self::getRequestHeaderValue(self::WARM_CACHE_HEADER);
		if ($warmCacheHeader == "https")
			$_SERVER['HTTPS'] = "on";
	
		$this->addKSData($ks);
		$this->_params['___cache___uri'] = $_SERVER['PHP_SELF'];
		$this->_params['___cache___protocol'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";
		$this->_params['___cache___host'] = @$_SERVER['HTTP_HOST'];

		// extract any baseEntry.getContentData referrer parameters
		$addExtraFields = false;
		$contextDataObjectType = 'contextDataParams:objectType';
		foreach ($this->_params as $key => $value)
		{
			if (substr($key, -strlen($contextDataObjectType)) !== $contextDataObjectType)
				continue;

			$addExtraFields = true;
				
			$keyPrefix = substr($key, 0, -strlen($contextDataObjectType));
			$referrerKey = $keyPrefix . 'contextDataParams:referrer';

			if (isset($this->_params[$referrerKey]))
			{
				$referrer = $this->_params[$referrerKey];
				unset($this->_params[$referrerKey]);
			}
			else
				$referrer = self::getHttpReferrer();
				
			$this->_referrers[] = $referrer;
		}
		
		$this->finalizeCacheKey();
		
		if ($addExtraFields)
			$this->addExtraFields();
	}
	
	/**
	 * This function checks whether the cache is disabled and returns the result.
	 */	
	public static function isCacheEnabled()
	{
		return count(self::$_activeInstances);
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
		if ($this->_cacheStatus == self::CACHE_STATUS_DISABLED)
			return false;
		
		$startTime = microtime(true);
		if (!$this->hasCache())
		{
			return false;
		}
		
		$cacheResult = $this->_cacheStore->get($this->_cacheKey . self::SUFFIX_DATA);
		if (!$cacheResult)
		{
			return false;
		}

		list($cacheId, $contentType, $response) = explode(self::CACHE_DELIMITER, $cacheResult, 3);
		if ($this->_cacheId && $this->_cacheId != $cacheId)
		{
			return false;
		}

		$this->_contentType = $contentType;
		
		// in case of multirequest, we must not condtionally cache the multirequest when a sub request comes from cache
		// for single requests, the next line has no effect
		self::disableConditionalCache();

		$processingTime = microtime(true) - $startTime;
		header("$cacheHeaderName:$cacheHeader,$this->_cacheKey,$processingTime", false);

		return $response;
	}
	
	protected function sendCachingHeaders($usingCache)
	{
		// for GET requests with kalsig (signature of call params) return cdn/browser caching headers
		if ($usingCache && $_SERVER["REQUEST_METHOD"] == "GET" && isset($_REQUEST["kalsig"]) && !self::hasExtraFields())
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
	}
		
	public function checkOrStart()
	{
		if ($this->_cacheStatus == self::CACHE_STATUS_DISABLED)
		{
			$this->sendCachingHeaders(false);
			return;
		}
					
		$response = $this->checkCache();		
		if (!$response)
		{
			$this->sendCachingHeaders(false);
			ob_start();
			return;
		}
		
		if ($this->_contentType) 
		{
			header($this->_contentType, true);
		}	

		$this->sendCachingHeaders(true);

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
	
		
	public function end()
	{
		if (!$this->getCacheModes())
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
	
	
	public function storeCache($response, $contentType = "", $serializeResponse = false)
	{
		// remove $this from the list of active instances - the request is complete
		$this->removeFromActiveList();
	
		$cacheModes = $this->getCacheModes();
		if (!$cacheModes)
			return;
			
		if ($serializeResponse)
			$response = serialize($response);
			
		$this->storeExtraFields();

		// set the X-Kaltura header only if it does not exist or contains 'cache-key'
		// the header is overwritten for cache-key so that for a multirequest we'll get the key of
		// the entire request and not just the last request
		$headers = headers_list();
		$foundHeader = false;
		foreach($headers as $header)
		{
			if (strpos($header, 'X-Kaltura') === 0 && strpos($header, 'cache-key') === false)
			{
				$foundHeader = true;
				break;
			}
		}

		if (!$foundHeader)
			header("X-Kaltura: cache-key,".$this->_cacheKey);
		
		$cacheId = microtime(true) . '_' . getmypid();
		
		$cacheRules = array();
		$maxExpiry = 0;
		foreach ($cacheModes as $cacheMode)
		{
			$expiry = $this->_expiry;
			$conditions = null;
			
			switch ($cacheMode)
			{
			case self::CACHE_MODE_CONDITIONAL:
				$conditions = array($cacheId, array_unique($this->_invalidationKeys), $this->_invalidationTime);
				if ($this->_conditionalCacheExpiry)
					$expiry = $this->_conditionalCacheExpiry;
				else
					$expiry = self::CONDITIONAL_CACHE_EXPIRY;
				break;

			case self::CACHE_MODE_ANONYMOUS:
				if ($expiry == $this->_defaultExpiry)
				{
					if (kConf::hasParam("v3cache_expiry"))
					{
						$expiryArr = kConf::get("v3cache_expiry");
						if (array_key_exists($this->_ksPartnerId, $expiryArr))
							$expiry = $expiryArr[$this->_ksPartnerId];
					}
				}
				break;
			}
			
			$maxExpiry = max($maxExpiry, $expiry);
			$cacheRules[$cacheMode] = array(time() + $expiry, $expiry, $conditions);
		}
	
		$cachedResponse = null;						// XXXXXXX TODO: remove this
		if ($this->_wouldHaveUsedCondCache)			// XXXXXXX TODO: remove this
		{
			$cachedResponse = $this->_cacheStore->get($this->_cacheKey . self::SUFFIX_DATA);
		}

		// write to the cache
		//$this->_cacheStore->set($this->_cacheKey . self::SUFFIX_LOG, print_r($this->_params, true), $maxExpiry + self::EXPIRY_MARGIN);

		$this->_cacheStore->set($this->_cacheKey . self::SUFFIX_RULES, serialize($cacheRules), $maxExpiry + self::EXPIRY_MARGIN);
		
		$this->_cacheStore->set($this->_cacheKey . self::SUFFIX_DATA, implode(self::CACHE_DELIMITER, array($cacheId, $contentType, $response)), $maxExpiry);

		// compare the calculated $response to the previously stored $cachedResponse
		if ($cachedResponse)			// XXXXXXX TODO: remove this
		{
			list($dummy, $cachedContentType, $cachedResponse) = explode(self::CACHE_DELIMITER, $cachedResponse, 3);
		
			$pattern = '/\/ks\/[a-zA-Z0-9=]+/';
			$response = preg_replace($pattern, 'KS', $response);
			$cachedResponse = preg_replace($pattern, 'KS', $cachedResponse);

			$pattern = '/s:\d+:/';
			$response = preg_replace($pattern, 's::', $response);
			$cachedResponse = preg_replace($pattern, 's::', $cachedResponse);

			$pattern = '/kaltura_player_\d+/';
			$response = preg_replace($pattern, 'KP', $response);
			$cachedResponse = preg_replace($pattern, 'KP', $cachedResponse);
			
			$format = isset($_REQUEST["format"]) ? $_REQUEST["format"] : KalturaResponseType::RESPONSE_TYPE_XML;				
			switch($format)
			{
				case KalturaResponseType::RESPONSE_TYPE_XML:
					$pattern = '/<executionTime>[0-9\.]+<\/executionTime>/';
					$testResult = (preg_replace($pattern, 'ET', $cachedResponse) == preg_replace($pattern, 'ET', $response));
					break;
					
				case KalturaResponseType::RESPONSE_TYPE_JSONP:
					$pattern = '/^[^\(]+/';
					$testResult = (preg_replace($pattern, 'CB', $cachedResponse) == preg_replace($pattern, 'CB', $response));
					break;
				
				default:
					$testResult = ($cachedResponse == $response);
					break;
			}
			
			if ($cachedContentType != $contentType)
			{
				$testResult = false;
			}
			
			if ($testResult)
				KalturaLog::log('conditional cache check: OK');			// we would have used the cache, and the response buffer do match
			else
			{
				KalturaLog::log('conditional cache check: FAILED key: '.$this->_cacheKey);		// we would have used the cache, but the response buffers do not match
				
				$outputFileBase = '/tmp/condCache/' . $this->_cacheKey;
				if (!is_dir('/tmp/condCache/'))
				{
					mkdir('/tmp/condCache/', 0777, true);
				}				
				$cachedFileName = $outputFileBase . '-cached';
				$nonCachedFileName = $outputFileBase . '-new';
				@file_put_contents($cachedFileName, $cachedResponse);
				@file_put_contents($nonCachedFileName, $response);
			}
		}
	}

	private function hasCache()
	{
		if ($this->_ks && (!$this->_ksObj || !$this->_ksObj->tryToValidateKS()))
			return false;					// ks not valid, do not return from cache
	
		// if the request is for warming the cache, disregard the cache and run the request
		$warmCacheHeader = self::getRequestHeaderValue(self::WARM_CACHE_HEADER);
		if ($warmCacheHeader !== false)
		{
			// make a trace in the access log of this being a warmup call
			header("X-Kaltura:cached-warmup-$warmCacheHeader,".$this->_cacheKey, false);
		}
		
		$cacheRules = $this->_cacheStore->get($this->_cacheKey . self::SUFFIX_RULES);
		if (!$cacheRules)
		{
			// don't have any cached response for this key
			return false;
		}
		
		$cacheRules = unserialize($cacheRules);	
		foreach ($cacheRules as $rule)
		{
			list($cacheExpiry, $expiryInterval, $conditions) = $rule;
		
			$cacheTTL = $cacheExpiry - time(); 
			if($cacheTTL <= 0)
			{
				// the cache is expired
				continue;
			}
				
			if ($conditions)
			{
				list($this->_cacheId, $invalidationKeys, $cachedInvalidationTime) = $conditions;
				$invalidationTime = self::getMaxInvalidationTime($invalidationKeys);
				if ($invalidationTime === null)		
					continue;					// failed to get the invalidation time from memcache, can't use cache
					
				if ($cachedInvalidationTime < $invalidationTime)
					continue;					// something changed since the response was cached

				if (isset($cacheRules[self::CACHE_MODE_ANONYMOUS]))
				{
					// since the conditions matched, we can extend the expiry of the anonymous cache
					list($cacheExpiry, $expiryInterval, $conditions) = $cacheRules[self::CACHE_MODE_ANONYMOUS];
					$cacheExpiry = time() + $expiryInterval;
					$cacheRules[self::CACHE_MODE_ANONYMOUS] = array($cacheExpiry, $expiryInterval, $conditions);
					$this->_cacheStore->set($this->_cacheKey . self::SUFFIX_RULES, serialize($cacheRules), $cacheTTL + self::EXPIRY_MARGIN);
				}
			}
			else if ($warmCacheHeader !== false)
			{
				// if there are no conditions and this is a cache warmup request, don't use the cache
				continue;
			}
			else if ($cacheTTL < self::WARM_CACHE_INTERVAL) // 1 minute left for cache, lets warm it
			{
				self::warmCache($this->_cacheKey);	
			}
			
			return true;
		}
		
		return false;
	}
	
	private static function getMaxInvalidationTime($invalidationKeys)
	{
		$memcache = kCacheManager::getCache(kCacheManager::MC_GLOBAL_KEYS);
		if (!$memcache)
			return null;

		$cacheResult = $memcache->multiGet($invalidationKeys);
		if ($cacheResult === false)
			return null;			// failed to get the invalidation keys
			
		if (!$cacheResult)
			return 0;				// no invalidation keys - no changes occured
			
		return max($cacheResult);
	}

	private function getCacheModes()
	{
		if ($this->_cacheStatus == self::CACHE_STATUS_DISABLED)
			return null;
			
		$ks = null;
		try
		{
			$ks = kSessionUtils::crackKs($this->_ks);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			self::disableCache();
			return null;
		}
		
		if ($ks && 
			($ks->valid_until <= time() ||						// don't cache when the KS is expired
			$ks->isSetLimitAction())) 							// don't cache when the KS has a limit on the number of actions
		{
			self::disableCache();
			return null;
		}
		
		$isAnonymous = !$ks || (!$ks->isAdmin() && ($ks->user === "0" || $ks->user === null));
        
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
				{
					$isAnonymous = true;
					break;
				}
			}
		}
		
		if (!$isAnonymous && $this->_cacheStatus == self::CACHE_STATUS_ANONYMOUS_ONLY)
		{
			self::disableCache();
			return null;
		}
		
		$result = array();
		if ($isAnonymous)
			$result[] = self::CACHE_MODE_ANONYMOUS;
		
		if ($this->_cacheStatus != self::CACHE_STATUS_ANONYMOUS_ONLY)
			$result[] = self::CACHE_MODE_CONDITIONAL;
		
		return $result;
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
		if (self::$_cacheWarmupInitiated)
			return;
			
		self::$_cacheWarmupInitiated = true;
	
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

	private static function handleSessionStart(&$params)
	{
		if (!isset($params['service']) || $params['service'] != 'session' ||
			!isset($params['action']) || $params['action'] != 'start' ||
			isset($params['multirequest']))
		{
			return;			// not a stand-alone call to session start
		}
		
		if (!isset($params['secret']) ||
			!isset($params['partnerId']))
		{
			return;			// missing mandatory params or not admin session
		}
					
		$format = isset($params['format']) ? $params['format'] : self::RESPONSE_TYPE_XML;
		if ($format != self::RESPONSE_TYPE_XML && $format != self::RESPONSE_TYPE_PHP)
		{
			return;			// the format is unsupported at this level
		}

		$type = isset($params['type']) ? $params['type'] : 0;
		if (!in_array($type, array(0, 2)))
		{
			return;			// invalid session type
		}
		$type = (int)$type;
		
		$partnerId = $params['partnerId'];
		$secrets = kSessionBase::getSecretsFromCache($partnerId);
		if (!$secrets)
		{
			return;			// can't find the secrets of the partner in the cache
		}
		list($adminSecret, $userSecret) = $secrets;				
		$secretToMatch = $type ? $adminSecret : $userSecret;
		$paramSecret = $params['secret'];
		if ($paramSecret != $secretToMatch)
		{
			return;			// invalid secret
		}
		
		$userId = isset($params['userId']) ? $params['userId'] : '';
		$expiry = isset($params['expiry']) ? $params['expiry'] : 86400;
		$privileges = isset($params['privileges']) ? $params['privileges'] : null;
		
		$result = kSessionBase::generateSession($adminSecret, $userId, $type, $partnerId, $expiry, $privileges);
		if ($format == self::RESPONSE_TYPE_XML)
		{
			header("Content-Type: text/xml");
			echo "<xml><result>{$result}</result><executionTime>0</executionTime></xml>";
			die;
		}
		else if ($format == self::RESPONSE_TYPE_PHP)
		{
			echo serialize($result);
			die;
		}
	}
}

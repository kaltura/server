<?php
/**
 * @package api
 * @subpackage cache
 */
require_once(dirname(__FILE__) . '/../../alpha/apps/kaltura/lib/cache/kApiCache.php');

/**
 * @package api
 * @subpackage cache
 */
class KalturaResponseCacher extends kApiCache
{
	// copied from KalturaResponseType
	const RESPONSE_TYPE_JSON = 1;
	const RESPONSE_TYPE_XML = 2;
	const RESPONSE_TYPE_PHP = 3;
		
	static protected $cachedContentHeaders = array('content-type', 'content-disposition', 'content-length', 'content-transfer-encoding');
	
	protected $_defaultExpiry = 0;
	protected $_cacheHeadersExpiry = 60; // cache headers for CDN & browser - used  for GET request with kalsig param
			
	public function __construct($params = null, $cacheType = kCacheManager::CACHE_TYPE_API_V3, $expiry = 0)
	{
		if ($expiry)
			$this->_defaultExpiry = $this->_expiry = $expiry;
		
		$this->_cacheKeyPrefix = 'cache_v3-';
		
		parent::__construct($cacheType, $params);
	}

	protected function init()
	{
		if (!parent::init())
			return false;

		self::handleCacheBasedServiceActions($this->_params);
		
		// remove parameters that do not affect the api result
		foreach(kConf::get('v3cache_ignore_params') as $name)
			unset($this->_params[$name]);
		
		unset($this->_params['kalsig']);		
		unset($this->_params['clientTag']);
		unset($this->_params['callback']);
		
		$this->_params['___cache___uri'] = $_SERVER['SCRIPT_NAME'];

		// extract any baseEntry.getContextData referrer parameters
		for ($i = 0; ; $i++)
		{
			$prefix = $i ? "{$i}:" : "";		// 0 = try single request, >0 = try multirequest
			if (!isset($this->_params["{$prefix}service"]) || !isset($this->_params["{$prefix}action"]))
			{
				if (!$i)			// could not find service/action, try multirequest - 1:service/1:action
					continue;
				break;
			}
			
			$service = $this->_params["{$prefix}service"];
			$action = $this->_params["{$prefix}action"];
			if (strtolower($service) != 'baseentry' || strtolower($action) != 'getcontextdata')
				continue;
			
			$referrerKey = "{$prefix}contextDataParams:referrer";
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
		
		$this->addExtraFields();
				
		return true;
	}

	protected function getKs()
	{
		$ks = parent::getKs();
		
		foreach($this->_params as $key => $value)
		{
			if (is_numeric($key) && is_array($value) && array_key_exists('ks', $value))
			{
				$curKs = $value['ks'];
				if (strpos($curKs, ':result') !== false)
					continue;				// the ks is the result of some sub request
				
				if ($ks && $ks != $curKs)
					return false;			// several different ks's in a multirequest - don't use cache
				
				$ks = $curKs;
				unset($this->_params[$key]['ks']);
				continue;
			}
			
			if(!preg_match('/[\d]+:ks/', $key))
				continue;				// not a ks

			if (strpos($value, ':result') !== false)
				continue;				// the ks is the result of some sub request

			if ($ks && $ks != $value)
				return false;			// several different ks's in a multirequest - don't use cache

			$ks = $value;
			unset($this->_params[$key]);
		}
		
		return $ks;
	}

	protected function sendCachingHeaders($usingCache, $lastModified = null)
	{
		header("Access-Control-Allow-Origin:*"); // avoid html5 xss issues

		// we should never return caching headers for non widget sessions since the KS can be ended and the CDN won't know
		$isAnonymous = $this->isAnonymous($this->_ksObj);
		$partnerId = $this->_ksObj ? $this->_ksObj->partner_id : 0;
		
		$forceCachingHeaders = false;
		if ($this->_ksObj && kConf::hasParam("force_caching_headers") && in_array($partnerId, kConf::get("force_caching_headers")))
			$forceCachingHeaders = true;

		// for GET requests with kalsig (signature of call params) return cdn/browser caching headers
		if ($usingCache && $isAnonymous && $_SERVER["REQUEST_METHOD"] == "GET" && isset($_REQUEST["kalsig"]) &&  
			(!self::hasExtraFields() || $forceCachingHeaders)) 
		{
			$v3cacheHeadersExpiry = kConf::get('v3cache_headers_expiry', 'local', array());
			if(isset($v3cacheHeadersExpiry[$partnerId]))
				$this->_cacheHeadersExpiry = $v3cacheHeadersExpiry[$partnerId];
		    		    
			$max_age = !is_null($this->minCacheTTL) ? min($this->_cacheHeadersExpiry, $this->minCacheTTL) : $this->_cacheHeadersExpiry ;
			header('Cache-Control: private, max-age=' . $max_age . ', max-stale=0');
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $max_age) . ' GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
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
		$response = $this->checkCache();		
		if (!$response)
		{
			$this->sendCachingHeaders(false);
			return;
		}
		
		$responseMetadata = $this->_responseMetadata;
		
		if ($responseMetadata['class'])
		{
			require_once(dirname(__FILE__) . "/../../alpha/apps/kaltura/lib/renderers/{$responseMetadata['class']}.php");
			
			$response = unserialize($response);
			if (!$response->validate())
			{
				if (self::$_debugMode)
					$this->debugLog('failed to validate the response');
				
				$this->sendCachingHeaders(false);
				return;
			}
			
			foreach ($responseMetadata['headers'] as $curHeader)
				header($curHeader, true);
			
			$response->output();
			die;
		}

		foreach ($responseMetadata['headers'] as $curHeader)
			header($curHeader, true);
		
		$this->sendCachingHeaders(true, isset($responseMetadata['lastModified']) ? $responseMetadata['lastModified'] : time());

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

		if(self::$_responsePostProcessor)
			self::$_responsePostProcessor->processResponse($response);
		
		echo $response;
		die;
	}
	
	protected function getContentHeaders()
	{
		$result = array();
		$headers = headers_list();
		foreach($headers as $headerStr)
		{
			$header = explode(":", $headerStr);
			if (isset($header[0]) && in_array(strtolower($header[0]), self::$cachedContentHeaders))
			{
				$result[] = $headerStr;
			}
		}
		return $result;
	}
	
	public function end($response)
	{
		$this->initCacheModes();
		if ($this->_cacheModes)
		{
			$responseClass = '';
			$serializeResponse = false;
			if ($response instanceof kRendererBase)
			{
				$responseClass = get_class($response);
				$serializeResponse = true;
			}
			
			$contentHeaders = $this->getContentHeaders();
			
			$responseMetadata = array(
				'lastModified' => time(),
				'headers' => $contentHeaders, 
				'class' => $responseClass
			);
						
			$this->storeCache($response, $responseMetadata, $serializeResponse);
		}
		
		if ($response instanceof kRendererBase)
		{
			$response->output();
			die;
		}
		else
		{
			if(self::$_responsePostProcessor)
				self::$_responsePostProcessor->processResponse($response);
			
			echo $response;
			die;
		}
	}

	protected function getAnonymousCachingExpiry()
	{
		if ($this->_expiry == $this->_defaultExpiry)
		{
			if (kConf::hasParam("v3cache_expiry"))
			{
				$expiryArr = kConf::get("v3cache_expiry");
				if (array_key_exists($this->_ksPartnerId, $expiryArr))
					return $expiryArr[$this->_ksPartnerId];
			}
		}
		
		return $this->_expiry;
	}
	
	protected function isAnonymous($ks)
	{
		if (parent::isAnonymous($ks))
			return true;
		else if(!$ks)
			return false;
			
		if($this->clientTag && strpos($this->clientTag, 'kmc') === 0)
			return false;
        
		// force caching of actions listed in kConf even if admin ks is used
		if(!kConf::hasParam('v3cache_ignore_admin_ks'))
			return false;
			
		$v3cacheIgnoreAdminKS = kConf::get('v3cache_ignore_admin_ks');
		if(!isset($v3cacheIgnoreAdminKS[$ks->partner_id]))
			return false;
			
		$actions = explode(',', $v3cacheIgnoreAdminKS[$ks->partner_id]);
		foreach($actions as $action)
		{
			list($serviceId, $actionId) = explode('.', $action);
			if($this->_params['service'] == $serviceId && $this->_params['action'] == $actionId)
			{
				return true;
			}
		}
		
		return false;
	}

	private static function isSupportedFormat($format)
	{
		return $format == self::RESPONSE_TYPE_XML ||
			$format == self::RESPONSE_TYPE_PHP ||
			$format == self::RESPONSE_TYPE_JSON ;
	}

	private static function handleCacheBasedServiceActions(&$params)
	{
		if (isset($params['service']) && isset($params['action']))
		{
			$service = $params['service'];
			$action = $params['action'];
			if ($service === 'session' && $action === 'start')
				return self::handleSessionStart($params);
			else
			{
				$format = isset($params['format']) ? $params['format'] : self::RESPONSE_TYPE_XML;
				if (!self::isSupportedFormat($format))
					return;			// the format is unsupported at this level
				$confActions = $path = kConf::get('cache_based_service_actions');;
				if (is_array($confActions))
				{
					$actionKey = $service . '_' . $action;
					if (array_key_exists($actionKey, $confActions))
					{
						$startTime = microtime(true);
						$filePath = dirname(__FILE__).$confActions[$actionKey];
						if ($filePath != dirname(__FILE__) &&
							file_exists($filePath))
						{
							require_once($filePath);
							$className = basename($filePath, ".php");
							if (class_exists($className) && method_exists($className, $action))
								$result =  $className::$action($params);
							else
								$result = "Could not run $className::$action since it does not exist";
						}
						else
						    $result = "Failed to parse $actionKey as a valid class configuration";
						
						if($result === false)
							return;
						
						$processingTime = microtime(true) - $startTime;
						self::returnCacheResponseStructure($processingTime, $format, $result);
					}
				}
			}
		}
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
		if (!self::isSupportedFormat($format))
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
		list($adminSecret, $userSecret, $ksVersion) = $secrets;				
		$paramSecret = $params['secret'];
		if ($paramSecret !== $adminSecret &&
			($type || $paramSecret !== $userSecret))
		{
			return;			// invalid secret
		}
		
		$startTime = microtime(true);
		
		$userId = isset($params['userId']) ? $params['userId'] : '';
		$expiry = isset($params['expiry']) ? $params['expiry'] : 86400;
		$privileges = isset($params['privileges']) ? $params['privileges'] : null;
		
		$result = kSessionBase::generateSession($ksVersion, $adminSecret, $userId, $type, $partnerId, $expiry, $privileges);
		
		$processingTime = microtime(true) - $startTime;
		$cacheKey = md5("{$partnerId}_{$userId}_{$type}_{$expiry}_{$privileges}");
		self::returnCacheResponseStructure($processingTime, $format, $result, $cacheKey);
	}

	private static function returnCacheResponseStructure($processingTime, $format, $result ,$cacheKey='noCacheKey')
	{
		header("X-Kaltura:cached-dispatcher,$cacheKey,$processingTime", false);
		header("Access-Control-Allow-Origin:*"); // avoid html5 xss issues
		header("Expires: Sun, 19 Nov 2000 08:52:00 GMT", true);
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0", true);
		header("Pragma: no-cache", true);

		if ($format == self::RESPONSE_TYPE_XML)
		{
			header("Content-Type: text/xml");
			echo "<xml><result>{$result}</result><executionTime>{$processingTime}</executionTime></xml>";
			die;
		}
		else if ($format == self::RESPONSE_TYPE_JSON)
		{
			header("Content-Type: application/json");
			echo json_encode($result);
			die;
		}
		else if ($format == self::RESPONSE_TYPE_PHP)
		{
			echo serialize($result);
			die;
		}
	}

	public static function adjustApiCacheForException($ex)
	{
		self::setExpiry(120);
		
		$cacheConditionally = false;
		if ($ex instanceof KalturaAPIException && kConf::hasParam("v3cache_conditional_cached_errors"))
		{
			$cacheConditionally = in_array($ex->getCode(), kConf::get("v3cache_conditional_cached_errors"));
		}
		if (!$cacheConditionally)
		{
			self::disableConditionalCache();
		}
	}

	public static function setHeadersCacheExpiry($expiry)
	{
		foreach (self::$_activeInstances as $curInstance)
		{
			if ($curInstance->_cacheHeadersExpiry && $curInstance->_cacheHeadersExpiry < $expiry)
				continue;
			if (self::$_debugMode)
				$curInstance->debugLog("setHeadersCacheExpiry called with [$expiry]");
			$curInstance->_cacheHeadersExpiry = $expiry;
		}
	}
	
	public function checkCache($cacheHeaderName = 'X-Kaltura', $cacheHeader = 'cached-dispatcher')
	{
		$result = parent::checkCache($cacheHeaderName, $cacheHeader);
		if(!$result)
			return $result;
		
		if (isset($this->_responseMetadata['responsePostProcessor']) && is_array($this->_responseMetadata['responsePostProcessor']) && !isset(self::$_responsePostProcessor))
		{
			$responsePostProcessor = $this->_responseMetadata['responsePostProcessor'];
			$filePath = key($responsePostProcessor);
			require_once $filePath;
			$postProcessor = unserialize($responsePostProcessor[$filePath]);
			self::$_responsePostProcessor = $postProcessor;
		}
		
		return $result;
	}
	
	public function storeCache($response, $responseMetadata = "", $serializeResponse = false)
	{		
		if(self::$_responsePostProcessor)
		{
			$postProcessorClass = new ReflectionClass(self::$_responsePostProcessor);
			$fileName = $postProcessorClass->getFileName();
			$responsePostProcessor = array($fileName => serialize(self::$_responsePostProcessor));
			$responseMetadata['responsePostProcessor'] = $responsePostProcessor;
		}
		
		parent::storeCache($response, $responseMetadata, $serializeResponse);
	}
}

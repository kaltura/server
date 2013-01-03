<?php
/**
 * @package api
 * @subpackage cache
 */
require_once(dirname(__FILE__) . '/../../server_infra/cache/kApiCache.php');

/**
 * @package api
 * @subpackage cache
 */
class KalturaResponseCacher extends kApiCache
{
	// copied from KalturaResponseType
	const RESPONSE_TYPE_XML = 2;
	const RESPONSE_TYPE_PHP = 3;
		
	protected $_defaultExpiry = 600;
	protected $_cacheHeadersExpiry = 60; // cache headers for CDN & browser - used  for GET request with kalsig param
		
	public function __construct($params = null, $cacheTypes = array(kCacheManager::FS_API_V3), $expiry = 0)
	{
		if ($expiry)
			$this->_defaultExpiry = $this->_expiry = $expiry;
		
		$this->_cacheKeyPrefix = 'cache_v3-';
		
		parent::__construct($cacheTypes, $params);
	}

	protected function init()
	{
		self::handleSessionStart($this->_params);
		
		// remove parameters that do not affect the api result
		foreach(kConf::get('v3cache_ignore_params') as $name)
			unset($this->_params[$name]);
		
		unset($this->_params['kalsig']);
		unset($this->_params['clientTag']);
		unset($this->_params['callback']);
		
		$this->_params['___cache___uri'] = $_SERVER['PHP_SELF'];

		// extract any baseEntry.getContentData referrer parameters
		$contextDataObjectType = 'contextDataParams:objectType';
		foreach ($this->_params as $key => $value)
		{
			if (substr($key, -strlen($contextDataObjectType)) !== $contextDataObjectType)
				continue;

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
		
		$this->addExtraFields();
				
		return true;
	}

	protected function getKs()
	{
		$ks = parent::getKs();
		
		foreach($this->_params as $key => $value)
		{
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

	public static function endCacheIfDisabled()
	{
		$lastInstance = end(self::$_activeInstances);
		if ($lastInstance)
			$lastInstance->initCacheModes();
		
		if (self::isCacheEnabled())
			return;
		
		while(ob_get_level())
			ob_end_clean();
	}	
	
	protected function sendCachingHeaders($usingCache)
	{
		header("Access-Control-Allow-Origin:*"); // avoid html5 xss issues

		// we should never return caching headers for non widget sessions since the KS can be ended and the CDN won't know
		$isAnonymous = !$this->_ks || ($this->_ksObj && $this->_ksObj->isWidgetSession());
		
		// for GET requests with kalsig (signature of call params) return cdn/browser caching headers
		if ($usingCache && $isAnonymous && $_SERVER["REQUEST_METHOD"] == "GET" && isset($_REQUEST["kalsig"]) && !self::hasExtraFields())
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
		
		if ($this->_responseMetadata) 
		{
			header($this->_responseMetadata, true);
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
		$this->initCacheModes();
		if (!$this->_cacheModes)
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
        
		// force caching of actions listed in kConf even if admin ks is used
		if(!kConf::hasMap('v3cache_ignore_admin_ks'))
			return false;
			
		$v3cacheIgnoreAdminKS = kConf::getMap('v3cache_ignore_admin_ks');
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
		list($adminSecret, $userSecret, $ksVersion) = $secrets;				
		$secretToMatch = $type ? $adminSecret : $userSecret;
		$paramSecret = $params['secret'];
		if ($paramSecret != $secretToMatch)
		{
			return;			// invalid secret
		}
		
		$userId = isset($params['userId']) ? $params['userId'] : '';
		$expiry = isset($params['expiry']) ? $params['expiry'] : 86400;
		$privileges = isset($params['privileges']) ? $params['privileges'] : null;
		
		$result = kSessionBase::generateSession($ksVersion, $adminSecret, $userId, $type, $partnerId, $expiry, $privileges);
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

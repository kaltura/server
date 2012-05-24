<?php

require_once(dirname(__FILE__) . '/../requestUtils.class.php');
require_once(dirname(__FILE__) . '/../webservices/kSessionBase.class.php');
require_once(dirname(__FILE__) . '/../../../../../infra/cache/kCacheManager.php');

class kPlayManifestCacher
{
	const CACHE_EXPIRY = 600;		// 10 min
	
	protected $_cacheEnabled = false;

	protected $_cacheKey = "";
	protected $_cacheWrapper = null;
	
	protected $_ksPartnerId = null;
	protected $_ksValidated = false;
	
	protected $_playbackContext = null;
	
	static protected $_instance = null;
	
	///////////////////////////////////////////////////////////////////
	//	Init functions
	
	public function __construct()
	{		
		if (!kConf::get('enable_cache'))
			return;
			
		$params = requestUtils::getRequestParams();
		if (isset($params['nocache']))
			return;
		
		$this->calculateCacheKey($params);
		
		$this->_cacheEnabled = true;
	}
	
	private function getKsData(&$params)
	{
		$ks = isset($params['ks']) ? $params['ks'] : '';		
		unset($params['ks']);

		$ksObj = kSessionBase::getKSObject($ks);
		$this->_ksPartnerId = ($ksObj ? $ksObj->partner_id : null);
		$params["___cache___partnerId"] =  $this->_ksPartnerId;
		$params["___cache___ksType"] = 	   ($ksObj ? $ksObj->type		: null);
		$params["___cache___userId"] =     ($ksObj ? $ksObj->user		: null);
		$params["___cache___privileges"] = ($ksObj ? $ksObj->privileges : null);
		
		if (!$ks)
		{
			$this->_ksValidated = true;
		}
		else if ($ksObj && !$ksObj->isAdmin())
		{
			$this->_ksValidated = $ksObj->tryToValidateKS();
		}
	}

	private function calculateCacheKey(&$params)
	{
		$this->getKsData($params);
		
		$this->_playbackContext = isset($params['playbackContext']) ? $params['playbackContext'] : null;
		unset($params['playbackContext']);
		
		$params['___cache___protocol'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";
		$params['___cache___host'] = @$_SERVER['HTTP_HOST'];

		// take only the hostname part of the referrer parameter of baseEntry.getContextData
		if (isset($params['referrer']))
		{
			if (in_array($this->_ksPartnerId, kConf::get('v3cache_include_referrer_in_key')))
				$params['referrer'] = parse_url($params['referrer'], PHP_URL_HOST);
			else
				unset($params['referrer']);
		}
		
		ksort($params);

		$this->_cacheKey = 'playManifest-' . md5( http_build_query($params) );

		$this->_cacheWrapper = kCacheManager::getCache(kCacheManager::FS_PLAY_MANIFEST);
	}
	
	static public function getInstance()
	{
		if (!self::$_instance)
			self::$_instance = new kPlayManifestCacher();
		return self::$_instance;
	}
	
	///////////////////////////////////////////////////////////////////
	//	Cache reading functions
	private function getCachedResponse()
	{
		if (!$this->_cacheEnabled)
			return false;
		
		$startTime = microtime(true);
		if ($this->_ksValidated)
		{
			$response = $this->_cacheWrapper->get($this->_cacheKey);
			if ($response)
			{
				$processingTime = microtime(true) - $startTime;
				header("X-Kaltura:cached-dispatcher,{$this->_cacheKey},{$processingTime}", false);
				return $response;
			}
		}
		
		return false;				
	}
		
	public function checkOrStart()
	{
		if (!$this->_cacheEnabled)
			return;
		
		$response = $this->getCachedResponse();
		if (!$response)
			return;

		list($requiredFiles, $serializedRenderer) = $response;
		foreach ($requiredFiles as $requiredFile)
		{
			require_once($requiredFile);
		}
		$renderer = unserialize($serializedRenderer);
		$renderer->output($this->_playbackContext);
		die;
	}
	
	///////////////////////////////////////////////////////////////////
	//	Cache storing functions

	public function storeCache($renderer)
	{
		if (!$this->_ksValidated)
			return;

		// provide cache key in header unless the X-Kaltura header was already set with a value
		// such as an error code. the header is used for debugging but it also appears in the access_log
		// and there we rather show the error than the cache key

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
		
		// serialize the response
		$requiredFiles = $renderer->getRequiredFiles();
		$serializedRenderer = serialize($renderer);
	
		$this->_cacheWrapper->set($this->_cacheKey, array($requiredFiles, $serializedRenderer), self::CACHE_EXPIRY);
	}
}

<?php

require_once(dirname(__FILE__) . '/../requestUtils.class.php');
require_once(dirname(__FILE__) . '/../../../../../infra/cache/kCacheManager.php');
require_once(dirname(__FILE__) . '/../../../../../infra/cache/kApiCache.php');

class kPlayManifestCacher extends kApiCache
{
	const CACHE_VERSION = '1';

	protected $_cacheWrapper = null;
	
	protected $_ksValidated = false;
	
	protected $_playbackContext = null;
	
	static protected $_instance = null;
	
	///////////////////////////////////////////////////////////////////
	//	Init functions
	
	public function __construct()
	{		
		$this->_cacheKeyPrefix = 'playManifest-';
		
		parent::__construct();
	
		if (!kConf::get('enable_cache'))
			return;
			
		$this->_params = requestUtils::getRequestParams();
		if (isset($this->_params['nocache']))
			return;
		
		$this->calculateCacheKey();
		
		$this->enableCache();
	}
	
	private function getKsData()
	{
		$ks = isset($this->_params['ks']) ? $this->_params['ks'] : '';		
		unset($this->_params['ks']);

		$this->addKSData($ks);
		
		if (!$ks)
		{
			$this->_ksValidated = true;
		}
		else if ($this->_ksObj && !$this->_ksObj->isAdmin())
		{
			$this->_ksValidated = $this->_ksObj->tryToValidateKS();
		}
	}

	private function calculateCacheKey()
	{
		$this->getKsData();
		
		$this->_playbackContext = isset($this->_params['playbackContext']) ? $this->_params['playbackContext'] : null;
		unset($this->_params['playbackContext']);
		
		$this->_params['___cache___protocol'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https" : "http";
		$this->_params['___cache___host'] = @$_SERVER['HTTP_HOST'];
		$this->_params['___cache___version'] = self::CACHE_VERSION;

		// take only the hostname part of the referrer parameter of baseEntry.getContextData
		if (isset($this->_params['referrer']))
		{
			$referrer = base64_decode(str_replace(" ", "+", $this->_params['referrer']));
			if (!is_string($referrer)) 
				$referrer = "";				
			unset($this->_params['referrer']);
		}
		else
			$referrer = self::getHttpReferrer();
		$this->_referrers[] = $referrer;
		
		$this->finalizeCacheKey();
		
		$this->addExtraFields();
			
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
		if ($this->_cacheStatus == self::CACHE_STATUS_DISABLED)
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
		if ($this->_cacheStatus == self::CACHE_STATUS_DISABLED)
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

		$this->storeExtraFields();
			
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
	
		$this->_cacheWrapper->set($this->_cacheKey, array($requiredFiles, $serializedRenderer), $this->_expiry);
	}
}

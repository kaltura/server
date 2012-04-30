<?php

require_once(dirname(__FILE__) . '/../requestUtils.class.php');
require_once(dirname(__FILE__) . '/../webservices/kSessionBase.class.php');

class kPlayManifestCacher
{
	const CACHE_EXPIRY = 600;		// 10 min
	const CACHE_FILE_PREFIX = "playManifest-";
	
	protected $_cacheEnabled = false;

	protected $_cacheKey = "";
	protected $_cacheDataFilePath = "";
	
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

		// take only the hostname part of the referrer parameter of baseEntry.getContextData
		if (isset($params['referrer']))
		{
			if (in_array($this->_ksPartnerId, kConf::get('v3cache_include_referrer_in_key')))
				$params['referrer'] = parse_url($params['referrer'], PHP_URL_HOST);
			else
				unset($params['referrer']);
		}
		
		ksort($params);

		$this->_cacheKey = md5( http_build_query($params) );

		// split cache over 256 folders using the cachekey first 2 characters
		// this will reduce the amount of files per cache folder
		$_cacheDirectory = rtrim(kConf::get('response_cache_dir'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$_cacheDirectory .= "cache_manifest" . DIRECTORY_SEPARATOR;
		$pathWithFilePrefix = $_cacheDirectory . substr($this->_cacheKey, 0, 2) . DIRECTORY_SEPARATOR . self::CACHE_FILE_PREFIX;
		$this->_cacheDataFilePath = $pathWithFilePrefix . $this->_cacheKey;
	}
	
	static public function getInstance()
	{
		if (!self::$_instance)
			self::$_instance = new kPlayManifestCacher();
		return self::$_instance;
	}
	
	///////////////////////////////////////////////////////////////////
	//	Cache reading functions
	
	private function canUseCache()
	{
		if (!$this->_ksValidated)
			return false;					// ks not valid, do not return from cache
	
		if (!file_exists($this->_cacheDataFilePath))
		{
			// don't have any cached response for this key
			return false;
		}
		
		// check the expiry
		$cacheExpiry = filemtime($this->_cacheDataFilePath) + self::CACHE_EXPIRY;
		if ($cacheExpiry <= time())
		{
			// cached response is expired
			@unlink($this->_cacheDataFilePath);
			return false;
		}
		
		return true;
	}
	
	private function getCachedResponse()
	{
		if (!$this->_cacheEnabled)
			return false;
		
		$startTime = microtime(true);
		if ($this->canUseCache())
		{
			$response = @file_get_contents($this->_cacheDataFilePath);
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

		list($requiredFiles, $serializedRenderer) = unserialize($response);
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

	private function createDirForPath($filePath)
	{
		$dirname = dirname($filePath);
		if (!is_dir($dirname))
		{
			mkdir($dirname, 0777, true);
		}
	}

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
		$response = serialize(array($requiredFiles, $serializedRenderer));
	
		$this->createDirForPath($this->_cacheDataFilePath);
		
		// write the cached response to a temporary file and then rename, to prevent any
		// other running instance of apache from picking up a partially written response
		$tempDataFilePath = tempnam(dirname($this->_cacheDataFilePath), basename($this->_cacheDataFilePath));
		file_put_contents($tempDataFilePath, $response);
		rename($tempDataFilePath, $this->_cacheDataFilePath);		
	}
}

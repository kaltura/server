<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'kApiCache.php');

class kPlayManifestCacher extends kApiCache
{
	protected $_deliveryCode = null;
	
	protected $_playbackContext = null;
	
	static protected $_instance = null;
	
	///////////////////////////////////////////////////////////////////
	//	Init functions
	
	public function __construct()
	{		
		$this->_cacheKeyPrefix = 'playManifest-';
		
		parent::__construct(kCacheManager::CACHE_TYPE_PLAY_MANIFEST);
	}
	
	protected function init()
	{
		if (!parent::init())
			return false;

		// ignore params which may hurt caching such as callback, playSessionId
		if (kConf::hasParam('playmanifest_ignore_params'))
		{
			$ignoreParams = kConf::get('playmanifest_ignore_params');
			foreach($ignoreParams as $paramName)
			{
				unset($this->_params[$paramName]);
			}
		}
		
		$this->_playbackContext = isset($this->_params['playbackContext']) ? $this->_params['playbackContext'] : null;
		unset($this->_params['playbackContext']);

		$this->_deliveryCode = isset($this->_params['deliveryCode']) ? $this->_params['deliveryCode'] : null;
		unset($this->_params['deliveryCode']);
		
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
		
		return true;
	}
	
	static public function getInstance()
	{
		if (!self::$_instance)
			self::$_instance = new kPlayManifestCacher();
		return self::$_instance;
	}
	
	///////////////////////////////////////////////////////////////////
	//	Cache reading functions
			
	public function checkOrStart()
	{
		if ($this->_cacheStatus == self::CACHE_STATUS_DISABLED)
			return;
		
		$serializedRenderer = $this->checkCache();
		if (!$serializedRenderer)
			return;

		$requiredFiles = explode(',', $this->_responseMetadata);
		foreach ($requiredFiles as $requiredFile)
		{
			require_once($requiredFile);
		}
		$renderer = unserialize($serializedRenderer);
		
		$renderer->setKsObject($this->_ksObj);
		$renderer->setPlaybackContext($this->_playbackContext);
		$renderer->setDeliveryCode($this->_deliveryCode);
		
		$renderer->output();
		die;
	}
	
	///////////////////////////////////////////////////////////////////
	//	Cache storing functions

	public function storeRendererToCache($renderer)
	{
		$requiredFiles = $renderer->getRequiredFiles();
		
		$baseAppDir = kConf::get('kaltura_app_root_path', 'local', null);
		if($baseAppDir)
		{
			foreach ($requiredFiles as &$fileName)
			{
				$fileName = str_replace(realpath($baseAppDir), $baseAppDir, $fileName);
			}
		}
	
		parent::storeCache($renderer, implode(',', $requiredFiles), true);
	}
}

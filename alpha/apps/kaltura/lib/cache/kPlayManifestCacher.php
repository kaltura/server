<?php

require_once(dirname(__FILE__) . '/kApiCache.php');

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
		$renderer->output($this->_deliveryCode, $this->_playbackContext);
		die;
	}
	
	///////////////////////////////////////////////////////////////////
	//	Cache storing functions

	public function storeRendererToCache($renderer)
	{
		$requiredFiles = $renderer->getRequiredFiles();
	
		parent::storeCache($renderer, implode(',', $requiredFiles), true);
	}
}

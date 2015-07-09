<?php

class DeliveryProfileLiveAppleHttpToMulticast extends DeliveryProfileLiveAppleHttp {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
	/* (non-PHPdoc)
	 * @see DeliveryProfileLive::serve()
	 */
	public function doServe($baseUrl, $backupUrl) 
	{
		$flavor = $this->getFlavorAssetInfo('', $baseUrl);		// passing the url as urlPrefix so that only the path will be tokenized
		$renderer = $this->getRenderer(array($flavor));
		return $renderer;
	}
	
	public function getEdgeServerUrls($primaryUrl, $backupUrl)
	{
		list($primaryUrl, $backupUrl) = parent::getEdgeServerUrls($primaryUrl, null);
		$primaryUrl = preg_replace('/' . EdgeServer::EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME . '/', EdgeServer::EDGE_SERVER_DEFAULT_LIVE_UNICAST_TO_MC_APPLICATION_NAME , $primaryUrl, 1);
		
		return array($primaryUrl, null);
	}
}

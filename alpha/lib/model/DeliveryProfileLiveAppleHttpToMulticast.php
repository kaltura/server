<?php

class DeliveryProfileLiveAppleHttpToMulticast extends DeliveryProfileLiveAppleHttp {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		parent::finalizeUrls($baseUrl, $flavorsUrls);
		
		$baseUrl = preg_replace('/' . EdgeServer::EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME . '/', EdgeServer::EDGE_SERVER_DEFAULT_LIVE_UNICAST_TO_MC_APPLICATION_NAME , $baseUrl, 1);
	}
}

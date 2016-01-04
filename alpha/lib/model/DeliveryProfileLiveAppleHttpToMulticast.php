<?php

class DeliveryProfileLiveAppleHttpToMulticast extends DeliveryProfileLiveAppleHttp {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
	public function getDeliveryServerNodeUrl($removeAfterUse = false)
	{
		$deliveryUrl = parent::getDeliveryServerNodeUrl($removeAfterUse);
		
		if($deliveryUrl)
			$deliveryUrl = preg_replace('/' . EdgeServerNode::EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME . '/', EdgeServerNode::EDGE_SERVER_DEFAULT_LIVE_UNICAST_TO_MC_APPLICATION_NAME , $deliveryUrl, 1);
		
		return $deliveryUrl;
	}
}

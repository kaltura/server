<?php

class EdgeServerNode extends DeliveryServerNode {
	
	const CUSTOM_DATA_DELIVERY_IDS = 'delivery_profile_ids';
	const CUSTOM_DATA_EDGE_PLAYBACK_CONFIGURATION = 'edge_playback_configuration';
	const EDGE_SERVER_DEFAULT_HOST_NAME_TOKEN = "{playbackHost}";
	const EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME = "kCache";
	const EDGE_SERVER_DEFAULT_LIVE_UNICAST_TO_MC_APPLICATION_NAME = "kMulticast";
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		
		$this->setType(serverNodeType::EDGE);
	}
	
	public function getManifestUr($protocol = 'http')
	{
		$edgePlaybackHost = $this->getPlaybackHost();
		
		return $protocol . '://' . rtrim($edgePlaybackHost, '/') . '/';
	}
	
	public function getPlaybackHost()
	{
		$playbackHostName = $this->getPlaybackHostName() . "/" . self::EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME . "/";
	
		if($this->parent_id)
		{
			$parentEdge = ServerNodePeer::retrieveByPK($this->parent_id);
			if($parentEdge)
				$playbackHostName = $playbackHostName . $parentEdge->getPlaybackHost();
		}
	
		return $playbackHostName;
	}
	
	/* Delivery Settings */
	
	public function setDeliveryProfileIds($params)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DELIVERY_IDS, $params);
	}
	
	public function getDeliveryProfileIds()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DELIVERY_IDS, null, null);
	}
	
	public function buildPlaybackUrl($originalPlaybackUrl)
	{
		$urlParts = explode("://", $originalPlaybackUrl);
		
		$edgeUrl = $this->getManifestUr($urlParts[0]);
		
		return $edgeUrl . $urlParts[1];
	}

} // EdgeServer

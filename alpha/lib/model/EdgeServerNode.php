<?php

class EdgeServerNode extends DeliveryServerNode {

	const EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME = "kCache";
	const EDGE_SERVER_DEFAULT_VOD_CACHE_APPLICATION_NAME = "kVOD";
	const EDGE_SERVER_DEFAULT_LIVE_UNICAST_TO_MC_APPLICATION_NAME = "kMulticast";
	const CUSTOM_DATA_DELIVERY_IDS = "delivery_profile_ids";
	
	/* Delivery Settings */
	public function setDeliveryProfileIds($params)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DELIVERY_IDS, $params);
	}
	
	public function getDeliveryProfileIds()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DELIVERY_IDS, null, array());
	}
	
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
	
	public function getManifestUrl($protocol = 'http', $format = null)
	{
		$edgePlaybackHost = $this->getPlaybackHost($protocol, $format);
		
		return $protocol . '://' . rtrim($edgePlaybackHost, '/') . '/';
	}
	
	public function getPlaybackHost($protocol = 'http', $format = null, $deliveryType = null)
	{
		return $this->buildEdgeFullPath($protocol, $format, $deliveryType);
	}
	
	public function buildEdgeFullPath($protocol = 'http', $format = null, $deliveryType = null)
	{
		$edgeFullPath = rtrim($this->getedgePath($format, $deliveryType), "/") . "/";
		
		if($this->parent_id)
		{
			$parentEdge = ServerNodePeer::retrieveByPK($this->parent_id);
			if($parentEdge)
				$edgeFullPath = $edgeFullPath . $parentEdge->buildEdgeFullPath($protocol, $format, $deliveryType);
		}
		
		return $edgeFullPath;
	}
	
	public function getedgePath($format, $deliveryType = null)
	{
		$edgePath = $this->getPlaybackHostName();
		
		$edgeSpecificDeliveryProfileByType = $this->getEdgeSpecificDeliveryProfileByType($format, $deliveryType);
		if(!$edgeSpecificDeliveryProfileByType)
			return $edgePath . "/" . $this->getCacheLocationByDeliveryType($deliveryType);
	
		/* @var $deliveryProfile DeliveryProfile */
		$deliveryUrl = $edgeSpecificDeliveryProfileByType->getUrl();
		$edgePath = str_replace("{hostName}", $edgePath, $deliveryUrl);
		return $edgePath;
	}
	
	private function getEdgeSpecificDeliveryProfileByType($format, $deliveryType)
	{
		if(!$deliveryType)
			return null;
		
		$edgeDeliveryProfileIds = $this->getDeliveryProfileIds();
		if(!count($edgeDeliveryProfileIds))
			return null;
		
		if(!isset($edgeDeliveryProfileIds[$format]) || !count($edgeDeliveryProfileIds[$format]))
			return null;
		
		$deliveryIdsForFormat = explode(",", $edgeDeliveryProfileIds[$format]);
		$deliveryProfiles = DeliveryProfilePeer::retrieveByTypeAndPks($deliveryIdsForFormat, $deliveryType);
		if(!count($deliveryIdsForFormat))
			return null;
		
		return reset($deliveryProfiles);
	}
	
	private function getCacheLocationByDeliveryType($deliveryType = null)
	{
		if(!$deliveryType)
			return self::EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME;
		
		$liveDeliveryTypes = DeliveryProfilePeer::getAllLiveDeliveryProfileTypes();
		if(!in_array($deliveryType, $liveDeliveryTypes))
			return self::EDGE_SERVER_DEFAULT_VOD_CACHE_APPLICATION_NAME;
	
		return self::EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME;
	}
} // EdgeServer

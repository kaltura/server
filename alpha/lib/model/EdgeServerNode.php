<?php

class EdgeServerNode extends DeliveryServerNode {

	const EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME = "kCache";
	const EDGE_SERVER_DEFAULT_THUMBNAIL_CACHE_APPLICATION_NAME = "kThumbnail";
	const EDGE_SERVER_DEFAULT_VOD_CACHE_APPLICATION_NAME = "kVOD";
	const EDGE_SERVER_DEFAULT_LIVE_UNICAST_TO_MC_APPLICATION_NAME = "kMulticast";
	const EDGE_SERVER_DEFAULT_KAPI_APPLICATION_NAME = "kAPI";
	const CUSTOM_DATA_KES_CONFIG = "config";
	
	/* Config Settings */
	public function setConfig($config)
	{
		$this->putInCustomData(self::CUSTOM_DATA_KES_CONFIG, $config);
	}
	
	public function getConfig()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_KES_CONFIG, null, null);
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
		$playbackHost = $this->buildEdgeFullPath($protocol, $format, $deliveryType);
		
		if($playbackHost && $format && $format == PlaybackProtocol::APPLE_HTTP_TO_MC)
			$playbackHost = preg_replace('/' . EdgeServerNode::EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME . '/',
					EdgeServerNode::EDGE_SERVER_DEFAULT_LIVE_UNICAST_TO_MC_APPLICATION_NAME , $playbackHost, 1);
		
		return $playbackHost;
	}
	
	public function buildEdgeFullPath($protocol = 'http', $format = null, $deliveryType = null, $assetType = null)
	{
		$edgeFullPath = rtrim($this->getEdgePath($format, $deliveryType, $assetType), "/") . "/";
		
		$parentIds = $this->getParentIdsArray();
		if(!count($parentIds))
			return $edgeFullPath;
		
		$parentEdge = $this->getActiveParent($parentIds);
		$edgeFullPath = $edgeFullPath . $parentEdge->buildEdgeFullPath($protocol, $format, $deliveryType, $assetType);
		
		return $edgeFullPath;
	}
	
	public function getEdgePath($format, $deliveryType = null, $assetType = null)
	{
		$edgePath = $this->getPlaybackDomain();
		
		$edgeSpecificDeliveryProfileByType = $this->getEdgeSpecificDeliveryProfileByType($format, $deliveryType);
		if(!$edgeSpecificDeliveryProfileByType)
			return $edgePath . "/" . $this->getCacheLocation($deliveryType, $assetType);
	
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
	
	private function getCacheLocation($deliveryType = null, $assetType = null)
	{
		if($assetType && $assetType == assetType::THUMBNAIL)
			return self::EDGE_SERVER_DEFAULT_THUMBNAIL_CACHE_APPLICATION_NAME;
		
		if( ($assetType && $assetType == assetType::LIVE) || !$deliveryType)
			return self::EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME;
		
		$liveDeliveryTypes = DeliveryProfilePeer::getAllLiveDeliveryProfileTypes();
		if(!in_array($deliveryType, $liveDeliveryTypes))
			return self::EDGE_SERVER_DEFAULT_VOD_CACHE_APPLICATION_NAME;
	
		return self::EDGE_SERVER_DEFAULT_LIVE_CACHE_APPLICATION_NAME;
	}
	
	public function validateEdgeTreeRegistered()
	{
		/* @var $edgeServer EdgeServerNode */
		$parentIds = $this->getParentIdsArray();
		if(!count($parentIds))
			return true;
		
		$parentEdge = $this->getActiveParent($parentIds);
		if(!$parentEdge)
			return false;
		
		return $parentEdge->validateEdgeTreeRegistered();
	}
	
	public function getActiveParent($parentIds)
	{
		$activeParents = ServerNodePeer::retrieveRegisteredServerNodesArrayByPKs($parentIds);
		$activeParentEdge = reset($activeParents);
		return $activeParentEdge;
	}
	
	public function getParentIdsArray()
	{
		$parentIds = array();
		
		$ids = $this->getParentId();
		if($ids)
		{
			$parentIds = explode(",", $ids);
		}
		
		return $parentIds;
	}
} // EdgeServer

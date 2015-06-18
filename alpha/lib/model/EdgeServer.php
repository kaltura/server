<?php


/**
 * Skeleton subclass for representing a row from the 'edge_server' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class EdgeServer extends BaseEdgeServer {
	
	const CUSTOM_DATA_DELIVERY_IDS = 'delivery_profile_ids';
	const EDGE_SERVER_DEFAULT_HOST_NAME_TOKEN = "{playbackHost}/kCache/";
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setType(edgeServerType::NODE);
	}
	
	/* Delivery Settings */
	
	public function setDeliveryProfileIds($params)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DELIVERY_IDS, $params);
	}
	
	public function getDeliveryProfileIds()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DELIVERY_IDS, null, array());
	}
	
	public function getPlaybackHostName()
	{
		$playbackHostName = $this->playback_host_name;
		
		if(!$playbackHostName)
			$playbackHostName = $this->host_name;
		
		return $playbackHostName;
	}
	
	public function getPlaybackHost($urlTemplate = "{playbackHost}/kCache/")
	{
		$playbackHostName = '';
		$urlParts = explode("/", $urlTemplate);
		$token = reset($urlParts);
		
		$playbackHostName = $this->getPlaybackHostName();
		
		if($this->parent_id)
		{
			$parentEdge = EdgeServerPeer::retrieveByPK($this->parent_id);
			if($parentEdge)
				$playbackHostName = $parentEdge->getPlaybackHost($urlTemplate) . "/" . $playbackHostName;
		}
		
		$playbackHostName = str_replace($token, $playbackHostName, $urlTemplate);
		return $playbackHostName;
	}

} // EdgeServer

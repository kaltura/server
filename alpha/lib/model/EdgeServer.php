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
	const DEFAULT_CACHE_EXPIRY = 300;

	/**
	 * Initializes internal state of EdgeServer object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
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
	
	private static function getCacheType()
	{
		return kCacheManager::CACHE_TYPE_EDGE_SERVER . '_' . kDataCenterMgr::getCurrentDcId();
	}
	
	public function updateStatus()
	{
		$key = $this->getId() . "_" . $this->getHostName();
		if(!kCacheManager::storeInCache(self::getCacheType(), $key, kConf::get('edge_server_cache_expiry', 'local', self::DEFAULT_CACHE_EXPIRY)))
			KalturaLog::debug("Failed to store edgse server [{$this->getHostName()}] in cache");
	}

} // EdgeServer

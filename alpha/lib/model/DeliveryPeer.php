<?php


/**
 *
 * @package Core
 * @subpackage model
 */
class DeliveryPeer extends BaseDeliveryPeer {
	
	// cache classes by their type
	protected static $class_types_cache = array(
			
			DeliveryType::APPLE_HTTP => 'DeliveryAppleHttp',
			DeliveryType::HD => 'DeliveryHds',
			DeliveryType::HDS => 'DeliveryHds',
			DeliveryType::HTTP => 'DeliveryHttp',
			DeliveryType::RTMP => 'DeliveryRtmp',
			DeliveryType::RTSP => 'DeliveryRtsp',
			DeliveryType::SILVER_LIGHT => 'DeliverySilverLight',
					
			DeliveryType::AKAMAI_HLS_DIRECT => 'DeliveryAkamaiAppleHttpDirect',
			DeliveryType::AKAMAI_HLS_MANIFEST => 'DeliveryAkamaiAppleHttpManifest',
			DeliveryType::AKAMAI_HD => 'DeliveryAkamaiHdNetworkSmil',
			DeliveryType::AKAMAI_HDS => 'DeliveryAkamaiHds',
			DeliveryType::AKAMAI_HTTP => 'DeliveryAkamaiHttp',
			DeliveryType::AKAMAI_RTMP => 'DeliveryAkamaiRtmp',
			DeliveryType::AKAMAI_RTSP => 'DeliveryAkamaiRtmp',
			DeliveryType::AKAMAI_SS => 'DeliveryAkamaiSilverLight',
					
			DeliveryType::GENERIC_HLS => 'DeliveryGenericAppleHttp',
			DeliveryType::GENERIC_HDS => 'DeliveryGenericHds',
			DeliveryType::GENERIC_HTTP => 'DeliveryGenericHttp',
					
			DeliveryType::LEVEL3_HLS => 'DeliveryLevel3AppleHttp',
			DeliveryType::LEVEL3_HTTP => 'DeliveryLevel3Http',
			DeliveryType::LEVEL3_RTMP => 'DeliveryLevel3Rtmp',
					
			DeliveryType::LIMELIGHT_HTTP => 'DeliveryLimeLightHttp',
			DeliveryType::LIMELIGHT_RTMP => 'DeliveryLimeLightRtmp',
					
			DeliveryType::LOCAL_PATH_APPLE_HTTP => 'DeliveryLocalPathAppleHttp',
			DeliveryType::LOCAL_PATH_HTTP => 'DeliveryLocalPathHttp',
			DeliveryType::LOCAL_PATH_RTMP => 'DeliveryLocalPathRtmp',
					
			DeliveryType::LIVE_HLS => 'DeliveryLiveAppleHttp',
			DeliveryType::LIVE_HDS => 'DeliveryLiveHds',
			DeliveryType::LIVE_RTMP => 'DeliveryLiveRtmp',
					
			DeliveryType::LIVE_AKAMAI_HDS => 'DeliveryLiveAkamaiHds',
	);
	
	public static function getClassByDeliveryType($deliveryType) {
		if(isset(self::$class_types_cache[$deliveryType]))
			return self::$class_types_cache[$deliveryType];
		
		$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $deliveryType);
		if($extendedCls)
		{
			self::$class_types_cache[$deliveryType] = $extendedCls;
			return $extendedCls;
		}
		self::$class_types_cache[$deliveryType] = parent::OM_CLASS;
		return parent::OM_CLASS;
	}
	
	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(DeliveryPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$deliveryType = $row[$typeField];
			return self::getClassByDeliveryType($deliveryType);
		}
			
		return parent::OM_CLASS;
	}
	
	// -------------------------------------
	// ------ Retrieval functionality ------
	// -------------------------------------
	
	/**
	 * This function returns the matching delivery object for a given partner and delivery protocol. 
	 * @var string $entryId - The entry ID
	 * @var PlaybackProtocol $streamerType - The protocol
	 * @var string $mediaProtocol - rtmp/rtmpe/https...
	 */
	public static function getLocalDeliveryByPartner($entryId, $streamerType = PlaybackProtocol::HTTP, $mediaProtocol = null) {
		
		$deliveries = array();
		
		$entry = entryPeer::retrieveByPK($entryId);
		$isSecured = $entry->isSecuredEntry();
		
		$partnerId = $entry->getPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);		
		$deliveryIds = $partner->getDeliveryIds();
		
		// if the partner has an override for the required format on the partner object - use that
		if(array_key_exists($streamerType, $deliveryIds)) {
			$deliveryIds = $deliveryIds[$streamerType];
			$deliveries = DeliveryPeer::retrieveByPKs($deliveryIds);
		} 
		// Else catch the default by the protocol
		else {
			$c = new Criteria();
			$c->add(DeliveryPeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
			$c->add(DeliveryPeer::IS_DEFAULT, true);
			$c->add(DeliveryPeer::STREAMER_TYPE, $streamerType);
			
			if($isSecured)
				$c->addDescendingOrderByColumn(DeliveryPeer::IS_SECURE);
			else
				$c->addAscendingOrderByColumn(DeliveryPeer::IS_SECURE);
				
			$deliveries = self::doSelect($c);
		}
		
		$delivery = self::selectByMediaProtocol($deliveries, $mediaProtocol);
		if($delivery) {
			KalturaLog::debug('Delivery ID for partnerId: '. $partnerId . ' and streamer type: ' . $streamerType . ' is ' . $delivery->getId());
			$delivery->setEntryId($entryId);
		} else {
			throw new Exception('Delivery ID can\'t be determined for partnerId: '. $partnerId . ' and streamer type: ' . $streamerType);
		}
		return $delivery;
	}
	
	/**
	 * This function returns the delivery object that matches a given storage profile and format
	 * If one not found - throws an exception.
	 * @var int $storageProfileId - The storage profile ID
	 * @var string $entryId - The entry ID
	 * @var PlaybackProtocol $streamerType - The protocol
	 * @var string $mediaProtocol - rtmp/rtmpe/https...
	 */
	public static function getRemoteDeliveryByStorageId($storageProfileId, $entryId, $streamerType = PlaybackProtocol::HTTP, $mediaProtocol = null) {
	
		$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		$deliveryIds = $storageProfile->getDeliveryIds();
		if(!array_key_exists($streamerType, $deliveryIds)) {
			throw new Exception('Delivery ID can\'t be determined for storageId: '. $storageProfileId . ' and streamer type: ' . $streamerType);
		}
		
		$deliveries = DeliveryPeer::retrieveByPKs($deliveryIds[$streamerType]);
		$delivery = self::selectByMediaProtocol($deliveries, $mediaProtocol);
		if($delivery) {
			KalturaLog::debug('Delivery ID for partnerId: '. $storageProfile->getPartnerId() . ' and streamer type:' . $streamerType . ' is ' . $delivery->getId());
			$delivery->setEntryId($entryId);
			$delivery->setStorageProfileId($storageProfileId);
		}
		return $delivery;
	}
	
	protected static function selectByMediaProtocol($deliveries, $mediaProtocol = null) {
		foreach ($deliveries as $delivery) {
			$supportedProtocols = explode(",", $delivery->getMediaProtocols());
			if((!$mediaProtocol) || (in_array($mediaProtocol, $supportedProtocols))) {
				return $delivery;
			}
		}
		return null;
	}
	

	public static function getUrlManagerIdentifyRequest(Partner $partner) {
		$deliveries =
		$enforceDelivery = $partner->getEnforceDelivery();
		if($enforceDelivery) {
			//  use only the delivery ids as described on partner.
			$deliveryIds = array();
			$deliveryIdsMap = $partner->getDeliveryIds();
			foreach($deliveryIdsMap as $deliveriesByFormat) 
				$deliveryIds = array_merge ( $deliveryIds, $deliveriesByFormat);
			$deliveries = $this->retrieveByPKs($deliveryIds);
		} else {
			$c = new Criteria();
			$c->add(DeliveryPeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
			$deliveries = $this->doSelect($c);
		}
		
		foreach($deliveries as $delivery) {
			if($delivery->identifyRequest())
				return true;
		}
		return false;
	}
} // DeliveryPeer

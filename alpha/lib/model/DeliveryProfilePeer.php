<?php


/**
 *
 * @package Core
 * @subpackage model
 */
class DeliveryProfilePeer extends BaseDeliveryProfilePeer {
	
	const LIVE_DELIVERY_PROFILE = 'LIVE';
	public static $LIVE_DELIVERY_PROFILES = 
		array(	DeliveryProfileType::LIVE_AKAMAI_HDS, 
				DeliveryProfileType::LIVE_HDS, 
				DeliveryProfileType::LIVE_HLS, 
				DeliveryProfileType::LIVE_RTMP);
	
	// cache classes by their type
	protected static $class_types_cache = array(
			
			DeliveryProfileType::APPLE_HTTP => 'DeliveryProfileAppleHttp',
			DeliveryProfileType::HDS => 'DeliveryProfileHds',
			DeliveryProfileType::HTTP => 'DeliveryProfileHttp',
			DeliveryProfileType::RTMP => 'DeliveryProfileRtmp',
			DeliveryProfileType::RTSP => 'DeliveryProfileRtsp',
			DeliveryProfileType::SILVER_LIGHT => 'DeliveryProfileSilverLight',
					
			DeliveryProfileType::AKAMAI_HLS_DIRECT => 'DeliveryProfileAkamaiAppleHttpDirect',
			DeliveryProfileType::AKAMAI_HLS_MANIFEST => 'DeliveryProfileAkamaiAppleHttpManifest',
			DeliveryProfileType::AKAMAI_HD => 'DeliveryProfileAkamaiHdNetworkSmil',
			DeliveryProfileType::AKAMAI_HDS => 'DeliveryProfileAkamaiHds',
			DeliveryProfileType::AKAMAI_HTTP => 'DeliveryProfileAkamaiHttp',
			DeliveryProfileType::AKAMAI_RTMP => 'DeliveryProfileAkamaiRtmp',
			DeliveryProfileType::AKAMAI_RTSP => 'DeliveryProfileAkamaiRtmp',
			DeliveryProfileType::AKAMAI_SS => 'DeliveryProfileAkamaiSilverLight',
					
			DeliveryProfileType::GENERIC_HLS => 'DeliveryProfileGenericAppleHttp',
			DeliveryProfileType::GENERIC_HDS => 'DeliveryProfileGenericHds',
			DeliveryProfileType::GENERIC_HTTP => 'DeliveryProfileGenericHttp',
					
			DeliveryProfileType::LEVEL3_HLS => 'DeliveryProfileLevel3AppleHttp',
			DeliveryProfileType::LEVEL3_HTTP => 'DeliveryProfileLevel3Http',
			DeliveryProfileType::LEVEL3_RTMP => 'DeliveryProfileLevel3Rtmp',
					
			DeliveryProfileType::LIMELIGHT_HTTP => 'DeliveryProfileLimeLightHttp',
			DeliveryProfileType::LIMELIGHT_RTMP => 'DeliveryProfileLimeLightRtmp',
					
			DeliveryProfileType::LOCAL_PATH_APPLE_HTTP => 'DeliveryProfileLocalPathAppleHttp',
			DeliveryProfileType::LOCAL_PATH_HTTP => 'DeliveryProfileLocalPathHttp',
			DeliveryProfileType::LOCAL_PATH_RTMP => 'DeliveryProfileLocalPathRtmp',
					
			DeliveryProfileType::LIVE_HLS => 'DeliveryProfileLiveAppleHttp',
			DeliveryProfileType::LIVE_HDS => 'DeliveryProfileLiveHds',
			DeliveryProfileType::LIVE_RTMP => 'DeliveryProfileLiveRtmp',
					
			DeliveryProfileType::LIVE_AKAMAI_HDS => 'DeliveryLiveAkamaiHds',
	);
	
	public static function getClassByDeliveryProfileType($deliveryType) {
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
			$typeField = self::translateFieldName(DeliveryProfilePeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$deliveryType = $row[$typeField];
			return self::getClassByDeliveryProfileType($deliveryType);
		}
			
		return parent::OM_CLASS;
	}
	
	// -------------------------------------
	// ------ Retrieval functionality ------
	// -------------------------------------
	
	public static function getDeliveryProfile($entryId, $streamerType = PlaybackProtocol::HTTP) 
	{
		return self::getLocalDeliveryByPartner($entryId, $streamerType, null, null, null, false);	
	}
	
	/**
	 * This function returns the matching delivery object for a given partner and delivery protocol. 
	 * @var string $entryId - The entry ID
	 * @var PlaybackProtocol $streamerType - The protocol
	 * @var string $mediaProtocol - rtmp/rtmpe/https...
	 */
	public static function getLocalDeliveryByPartner($entryId, $streamerType = PlaybackProtocol::HTTP, $mediaProtocol = null, $cdnHost = null, $checkSecured = true) {
		
		$deliveries = array();
		$entry = entryPeer::retrieveByPK($entryId);
		$partnerId = $entry->getPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		$isSecured = false;
		if($checkSecured)
			$isSecured = self::isSecured($partner, $entry);
		$deliveryIds = $partner->getDeliveryIds();
		
		// if the partner has an override for the required format on the partner object - use that
		if(array_key_exists($streamerType, $deliveryIds)) {
			$deliveryIds = $deliveryIds[$streamerType];
			$deliveries = DeliveryProfilePeer::retrieveByPKs($deliveryIds);
			
			$cmp = new DeliveryProfileCdnHostComparator($isSecured, $cdnHost);
			array_walk($deliveries, array($cmp,"decorateWithUserOrder"));
			uasort($deliveries, array($cmp, "compare"));
		} 
		// Else catch the default by the protocol
		else {
			$c = new Criteria();
			$c->add(DeliveryProfilePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
			$c->add(DeliveryProfilePeer::IS_DEFAULT, true);
			$c->add(DeliveryProfilePeer::STREAMER_TYPE, $streamerType);
			$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::NOT_IN);
			
			if($isSecured)
				$c->addDescendingOrderByColumn(DeliveryProfilePeer::TOKENIZER);
			else
				$c->addAscendingOrderByColumn(DeliveryProfilePeer::TOKENIZER);
			
			$orderBy = "(" . DeliveryProfilePeer::PARTNER_ID . "<>{$partnerId})";
			$c->addAscendingOrderByColumn($orderBy);
			
			$deliveries = self::doSelect($c);
		}
		
		$delivery = self::selectByMediaProtocol($deliveries, $mediaProtocol);
		if($delivery) {
			KalturaLog::debug('Delivery ID for partnerId: '. $partnerId . ' and streamer type: ' . $streamerType . ' is ' . $delivery->getId());
			$delivery->setEntryId($entryId);
		} else {
			KalturaLog::err('Delivery ID can\'t be determined for partnerId: '. $partnerId . ' and streamer type: ' . $streamerType);
		}
		return $delivery;
	}
	
	protected static function isSecured($partner, $entry) {
		$ks = kCurrentContext::$ks_object;
		$isSecured = false;
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT, $partner->getId()) &&
				($partner->getDefaultEntitlementEnforcement() || ($ks && $ks->getEnableEntitlement())))
			$isSecured = true;
		if(!$isSecured)
			$isSecured = $entry->isSecuredEntry();
		return $isSecured;
	}
	
	/**
	 * This function returns the delivery object that matches a given storage profile and format
	 * If one not found - returns null
	 * @var int $storageProfileId - The storage profile ID
	 * @var string $entryId - The entry ID
	 * @var PlaybackProtocol $streamerType - The protocol
	 * @var string $mediaProtocol - rtmp/rtmpe/https...
	 */
	public static function getRemoteDeliveryByStorageId($storageProfileId, $entryId, $streamerType = PlaybackProtocol::HTTP, $mediaProtocol = null) {
	
		$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		$deliveryIds = $storageProfile->getDeliveryIds();
		if(!array_key_exists($streamerType, $deliveryIds)) {
			KalturaLog::err('Delivery ID can\'t be determined for storageId: '. $storageProfileId . ' and streamer type: ' . $streamerType);
			return null;
		}
		
		$deliveries = DeliveryProfilePeer::retrieveByPKs($deliveryIds[$streamerType]);
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
			if(is_null($delivery->getMediaProtocols()))
				return $delivery;

			$supportedProtocols = explode(",", $delivery->getMediaProtocols());
			if((!$mediaProtocol) || (in_array($mediaProtocol, $supportedProtocols))) {
				return $delivery;
			}
		}
		return null;
	}
	
	public static function getDeliveryProfileByHostName($cdnHost, $entryId, $streamerType = PlaybackProtocol::HTTP, $mediaProtocol = null) {
		$entry = entryPeer::retrieveByPK($entryId);
		$partnerId = $entry->getPartnerId();
		
		$c = new Criteria();
		$c->add(DeliveryProfilePeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $partnerId), Criteria::IN); 
		$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::IN);
		
		$hostCond = $c->getNewCriterion(DeliveryProfilePeer::HOST_NAME, $cdnHost);
		$hostCond->addOr($c->getNewCriterion(DeliveryProfilePeer::HOST_NAME, null, Criteria::ISNULL));
		
		$c->addAnd($hostCond);
		$c->add(DeliveryProfilePeer::STREAMER_TYPE, $streamerType);
		
		$c->addDescendingOrderByColumn(DeliveryProfilePeer::HOST_NAME);
		$orderBy = "(" . DeliveryProfilePeer::PARTNER_ID . "<>{$partnerId})";
		$c->addAscendingOrderByColumn($orderBy);
			
		$deliveries = self::doSelect($c);
		$delivery = self::selectByMediaProtocol($deliveries, $mediaProtocol);
		if($delivery) {
			KalturaLog::debug('Delivery ID for Host Name: '. $cdnHost . ' and streamer type: ' . $streamerType . ' is ' . $delivery->getId());
			$delivery->setEntryId($entryId);
		} else {
			KalturaLog::err('Delivery ID can\'t be determined for Host Name: '. $cdnHost . ' and streamer type: ' . $streamerType);
		}
		return $delivery;
		
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
			$c->add(DeliveryProfilePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
			$deliveries = $this->doSelect($c);
		}
		
		foreach($deliveries as $delivery) {
			if($delivery->identifyRequest())
				return true;
		}
		return false;
	}
	
	
	public static function getAllLiveDeliveryProfileTypes()
	{
		$deliveryProfileTypes = KalturaPluginManager::getExtendedTypes(self::OM_CLASS, self::LIVE_DELIVERY_PROFILE);
		$deliveryProfileTypes = array_merge($deliveryProfileTypes, self::$LIVE_DELIVERY_PROFILES);
		return $deliveryProfileTypes;
	}
} // DeliveryProfilePeer


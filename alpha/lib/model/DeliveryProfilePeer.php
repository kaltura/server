<?php


/**
 *
 * @package Core
 * @subpackage model
 * 
 */
class DeliveryProfilePeer extends BaseDeliveryProfilePeer {
	
	const LIVE_DELIVERY_PROFILE = 'LIVE';
	
	/**
	 * This array describe all known live delivery profiles types.
	 * It can be extended by the plugins - DeliveryProfile-live.
	 * 
	 * @var array
	 */
	public static $LIVE_DELIVERY_PROFILES = 
		array(	DeliveryProfileType::LIVE_AKAMAI_HDS, 
				DeliveryProfileType::LIVE_HDS, 
				DeliveryProfileType::LIVE_HLS, 
				DeliveryProfileType::LIVE_RTMP);
	
	/**
	 * Static cache for mapping between delivery profile type to delivery profile type. 
	 * Can be extended by the plugins.
	 * @var unknown_type
	 */
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
			DeliveryProfileType::AKAMAI_RTSP => 'DeliveryProfileAkamaiRtsp',
			DeliveryProfileType::AKAMAI_SS => 'DeliveryProfileAkamaiSilverLight',
					
			DeliveryProfileType::GENERIC_HLS => 'DeliveryProfileGenericAppleHttp',
			DeliveryProfileType::GENERIC_HDS => 'DeliveryProfileGenericHds',
			DeliveryProfileType::GENERIC_HTTP => 'DeliveryProfileGenericHttp',
			DeliveryProfileType::GENERIC_HLS_MANIFEST => 'DeliveryProfileGenericAppleHttpManifest',
			DeliveryProfileType::GENERIC_HDS_MANIFEST => 'DeliveryProfileGenericHdsManifest',
			DeliveryProfileType::GENERIC_SS => 'DeliveryProfileGenericSilverLight',
			DeliveryProfileType::GENERIC_RTMP => 'DeliveryProfileGenericRtmp',
					
			DeliveryProfileType::LEVEL3_HLS => 'DeliveryProfileLevel3AppleHttp',
			DeliveryProfileType::LEVEL3_HTTP => 'DeliveryProfileLevel3Http',
			DeliveryProfileType::LEVEL3_RTMP => 'DeliveryProfileLevel3Rtmp',
					
			DeliveryProfileType::LIMELIGHT_HTTP => 'DeliveryProfileLimeLightHttp',
			DeliveryProfileType::LIMELIGHT_RTMP => 'DeliveryProfileLimeLightRtmp',
			
			DeliveryProfileType::VOD_PACKAGER_DASH => 'DeliveryProfileVodPackagerDash',
			DeliveryProfileType::VOD_PACKAGER_HDS => 'DeliveryProfileVodPackagerHds',
			DeliveryProfileType::VOD_PACKAGER_MSS => 'DeliveryProfileVodPackagerMss',
				
			DeliveryProfileType::LOCAL_PATH_APPLE_HTTP => 'DeliveryProfileLocalPathAppleHttp',
			DeliveryProfileType::LOCAL_PATH_HTTP => 'DeliveryProfileLocalPathHttp',
			DeliveryProfileType::LOCAL_PATH_RTMP => 'DeliveryProfileLocalPathRtmp',
					
			DeliveryProfileType::LIVE_HLS => 'DeliveryProfileLiveAppleHttp',
			DeliveryProfileType::LIVE_HDS => 'DeliveryProfileLiveHds',
			DeliveryProfileType::LIVE_RTMP => 'DeliveryProfileLiveRtmp',
					
			DeliveryProfileType::LIVE_AKAMAI_HDS => 'DeliveryProfileLiveAkamaiHds',
	);
	
	/**
	 * Returns the matching delivery profile class by the delivery profile type.
	 * @param DeliveryProfileType $deliveryType
	 * @return string representing the delivery object class
	 */
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
	
	/**
	 * Returns the delivery profile that matches the entryID and the streamer type.
	 * @param string $entryId The entry id
	 * @param PlayBackProtocol $streamerType the streamer type
	 * @return DeliveryProfile
	 */
	public static function getDeliveryProfile($entryId, $streamerType = PlaybackProtocol::HTTP) 
	{
		if ($streamerType == PlaybackProtocol::HTTP)
		{
			$delivery = self::getLocalDeliveryByPartner($entryId, $streamerType, infraRequestUtils::getProtocol(), null, null, false);
			if ($delivery)
				return $delivery;
		}
		return self::getLocalDeliveryByPartner($entryId, $streamerType, null, null, null, false);	
	}
	
	/**
	 * This function returns the matching delivery object for a given entry and delivery protocol. 
	 * @param string $entryId - The entry ID
	 * @param PlaybackProtocol $streamerType - The protocol
	 * @param string $mediaProtocol - rtmp/rtmpe/https...
	 * @param string $cdnHost - The requesting CdnHost if known / preffered.
	 * @param boolean $checkSecured whether we should prefer secured delivery profiles.
	 * @return DeliveryProfile
	 */
	public static function getLocalDeliveryByPartner($entryId, $streamerType = PlaybackProtocol::HTTP, $mediaProtocol = null, $cdnHost = null, $checkSecured = true) {
		
		$deliveries = array();
		$entry = entryPeer::retrieveByPK($entryId);
		$partnerId = $entry->getPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner) {
			KalturaLog::err('Failed to retrieve partnerId: '. $partnerId);
			return null;
		}
		
		$isSecured = false;
		if($checkSecured)
			$isSecured = self::isSecured($partner, $entry);
		$delivery = self::getDeliveryByPartner($partner, $streamerType, $mediaProtocol, $cdnHost, $isSecured);
		if($delivery)
			$delivery->setEntryId($entryId);
		return $delivery;
	}
		
	/**
	 * This function returns the matching delivery object for a given partner and delivery protocol
	 * @param Partner $partner The partner
	 * @param PlaybackProtocol $streamerType - The protocol
	 * @param string $mediaProtocol - rtmp/rtmpe/https...
	 * @param string $cdnHost - The requesting CdnHost if known / preffered.
	 * @param boolean $isSecured whether we're interested in secured delivery profile
	 */
	public static function getDeliveryByPartner(Partner $partner, $streamerType = PlaybackProtocol::HTTP, $mediaProtocol = null, $cdnHost = null, $isSecured = false) {
		
		$partnerId = $partner->getId();
		$deliveryIds = $partner->getDeliveryProfileIds();
		
		// if the partner has an override for the required format on the partner object - use that
		if(array_key_exists($streamerType, $deliveryIds)) {
			$deliveryIds = $deliveryIds[$streamerType];
			$deliveries = DeliveryProfilePeer::retrieveByPKs($deliveryIds);
			
			$cmp = new DeliveryProfileComparator($isSecured, $cdnHost);
			array_walk($deliveries, "DeliveryProfileComparator::decorateWithUserOrder", $deliveryIds);
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
				$c->addDescendingOrderByColumn('(' . DeliveryProfilePeer::TOKENIZER . ' is not null)');
			else
				$c->addDescendingOrderByColumn('(' . DeliveryProfilePeer::TOKENIZER . ' is null)');
			
			$orderBy = "(" . DeliveryProfilePeer::PARTNER_ID . "<>{$partnerId})";
			$c->addAscendingOrderByColumn($orderBy);
			
			$deliveries = self::doSelect($c);
		}
		
		$delivery = self::selectByMediaProtocol($deliveries, $mediaProtocol);
		if($delivery) {
			KalturaLog::debug("Delivery ID for partnerId [$partnerId] and streamer type [$streamerType] is " . $delivery->getId());
		} else {
			KalturaLog::err("Delivery ID can't be determined for partnerId [$partnerId] streamer type [$streamerType] and media protocol [$mediaProtocol]");
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
	 * @param int $storageProfileId - The storage profile ID
	 * @param string $entryId - The entry ID
	 * @param PlaybackProtocol $streamerType - The protocol
	 * @param string $mediaProtocol - rtmp/rtmpe/https...
	 * @return DeliveryProfile
	 */
	public static function getRemoteDeliveryByStorageId($storageProfileId, $entryId, $streamerType = PlaybackProtocol::HTTP, $mediaProtocol = null, 
			FileSync $fileSync = null, asset $asset = null) {
	
		$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		if(!$storageProfile) {
			KalturaLog::err('Couldn\'t retrieve storageId: '. $storageProfileId);
			return null;
		}
			
		$deliveryIds = $storageProfile->getDeliveryProfileIds();
		if(!array_key_exists($streamerType, $deliveryIds)) {
			KalturaLog::err("Delivery ID can't be determined for storageId [$storageProfileId] ( PartnerId [" .  $storageProfile->getPartnerId() . "] ) and streamer type [ $streamerType ]");
			return null;
		}
		
		$deliveries = DeliveryProfilePeer::retrieveByPKs($deliveryIds[$streamerType]);
		$delivery = self::selectByMediaProtocol($deliveries, $mediaProtocol);
		if($delivery) {
			KalturaLog::debug("Delivery ID for storageId [$storageProfileId] ( PartnerId [" . $storageProfile->getPartnerId() . "] ) and streamer type [$streamerType] is " . $delivery->getId());
			$delivery->setEntryId($entryId);
			$delivery->setStorageProfileId($storageProfileId);
			
			$delivery->initDeliveryDynamicAttributes($fileSync, $asset);
		} else {
			KalturaLog::err("Delivery ID can't be determined for storageId [$storageProfileId] ( PartnerId [" .  $storageProfile->getPartnerId() . "] ) streamer type [$streamerType] and media protocol [$mediaProtocol]");
		}
		
		return $delivery;
	}
	
	/**
	 * Selects between a list of deliveries by a requested media protocol
	 * @param array $deliveries list of deliveries
	 * @param string $mediaProtocol requested media protocol
	 * @return The matching DeliveryProfile if exists, or null otherwise
	 */
	protected static function selectByMediaProtocol($deliveries, $mediaProtocol = null) {
		if(!$mediaProtocol)
			return reset($deliveries);
		
		foreach ($deliveries as $delivery) {
			if(is_null($delivery->getMediaProtocols()))
				return $delivery;

			$supportedProtocols = explode(",", $delivery->getMediaProtocols());
			if(in_array($mediaProtocol, $supportedProtocols)) 
				return $delivery;
			
		}
		return null;
	}
	
	/**
	 * Returns the delivery profile by host name (or returns one of the defaults)
	 * @param string $cdnHost The host we're looking for
	 * @param string $entryId The entry for which we search for the delivery profile
	 * @param PlaybackProtocol $streamerType - The protocol
	 * @param string $mediaProtocol - rtmp/rtmpe/https...
	 * @return DeliveryProfile
	 */
	public static function getLiveDeliveryProfileByHostName($cdnHost, $entryId, $streamerType = PlaybackProtocol::HTTP, $mediaProtocol = null) {
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry) {
			KalturaLog::err('Failed to retrieve entryId: '. $entryId);
			return null;
		}
		$partnerId = $entry->getPartnerId();
		
		$c = new Criteria();
		$c->add(DeliveryProfilePeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $partnerId), Criteria::IN); 
		$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::IN);
		
		$hostCond = $c->getNewCriterion(DeliveryProfilePeer::HOST_NAME, $cdnHost);
		$hostCond->addOr($c->getNewCriterion(DeliveryProfilePeer::HOST_NAME, null, Criteria::ISNULL));
		
		$c->addAnd($hostCond);
		$c->add(DeliveryProfilePeer::STREAMER_TYPE, $streamerType);
		
		$c->addDescendingOrderByColumn('(' . DeliveryProfilePeer::HOST_NAME . ' is not null)');
		$orderBy = "(" . DeliveryProfilePeer::PARTNER_ID . "<>{$partnerId})";
		$c->addAscendingOrderByColumn($orderBy);
			
		$deliveries = self::doSelect($c);
		$delivery = self::selectByMediaProtocol($deliveries, $mediaProtocol);
		if($delivery) {
			KalturaLog::debug("Delivery ID for Host Name: [$cdnHost] and streamer type: [$streamerType] is [" . $delivery->getId());
			$delivery->setEntryId($entryId);
		} else {
			KalturaLog::err("Delivery ID can't be determined for Host Name [$cdnHost] and streamer type [$streamerType]");
		}
		return $delivery;
		
	}

	/**
	 * Checks whether a the current request is restricted by the partner.
	 * @param Partner $partner The partner we want to verify it for.
	 * @return true if the request is should be blocked, flase otherwise
	 */
	public static function isRequestRestricted(Partner $partner) {
		$enforceDelivery = $partner->getEnforceDelivery();
		if(!$enforceDelivery)
			return false;
			
		// Retrieve request origin
		$requestOrigin = @$_SERVER['HTTP_X_FORWARDED_HOST'];
		if(!$requestOrigin)
			$requestOrigin = @$_SERVER['HTTP_HOST'];
		
		//  Otherwise, check the partner delivery profiles
		$deliveryIds = array();
		$deliveryIdsMap = $partner->getDeliveryProfileIds();
		foreach($deliveryIdsMap as $deliveriesByFormat) {
			if(is_array($deliveriesByFormat))
				$deliveryIds = array_merge ( $deliveryIds, $deliveriesByFormat);
			else 
				$deliveryIds[] = $deliveriesByFormat;
		}
		$deliveries = self::retrieveByPKs($deliveryIds);
		
		foreach($deliveries as $delivery) {
			$recognizer = $delivery->getRecognizer();
			if(!is_null($recognizer)) {
				if($recognizer->isRecognized($requestOrigin)) {
					return false;					
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Returns all live delivery profile types
	 * @return array supported live types
	 */
	protected static function getAllLiveDeliveryProfileTypes()
	{
		$deliveryProfileTypes = KalturaPluginManager::getExtendedTypes(self::OM_CLASS, self::LIVE_DELIVERY_PROFILE);
		$deliveryProfileTypes = array_merge($deliveryProfileTypes, self::$LIVE_DELIVERY_PROFILES);
		
		$key = array_search(self::LIVE_DELIVERY_PROFILE, $deliveryProfileTypes);
		unset($deliveryProfileTypes[$key]);
		
		return $deliveryProfileTypes;
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("deliveryProfile:id=%s", self::ID), array("deliveryProfile:partnerId=%s", self::PARTNER_ID));
	}
	
	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
	
		$c = new myCriteria();
		
		// We'd like to retrieve only active delivery profiles, and the ones we consider to remove but haven't removed yet.
		$c->addAnd(DeliveryProfilePeer::STATUS, array(DeliveryStatus::ACTIVE, DeliveryStatus::STAGING_OUT), Criteria::IN);
		
		self::$s_criteria_filter->setFilter ( $c );
	}
	
} // DeliveryProfilePeer


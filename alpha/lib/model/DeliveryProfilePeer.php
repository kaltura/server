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
				DeliveryProfileType::LIVE_DASH, 
				DeliveryProfileType::LIVE_RTMP,
				DeliveryProfileType::LIVE_HLS_TO_MULTICAST,
				DeliveryProfileType::LIVE_PACKAGER_HLS,
				DeliveryProfileType::LIVE_PACKAGER_HDS,
				DeliveryProfileType::LIVE_PACKAGER_DASH,
				DeliveryProfileType::LIVE_PACKAGER_MSS,
		);
	
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
			
			DeliveryProfileType::VOD_PACKAGER_HLS => 'DeliveryProfileVodPackagerHls',
			DeliveryProfileType::VOD_PACKAGER_HLS_MANIFEST => 'DeliveryProfileVodPackagerHlsManifest',
			DeliveryProfileType::VOD_PACKAGER_DASH => 'DeliveryProfileVodPackagerDash',
			DeliveryProfileType::VOD_PACKAGER_HDS => 'DeliveryProfileVodPackagerHds',
			DeliveryProfileType::VOD_PACKAGER_MSS => 'DeliveryProfileVodPackagerMss',
				
			DeliveryProfileType::LOCAL_PATH_APPLE_HTTP => 'DeliveryProfileLocalPathAppleHttp',
			DeliveryProfileType::LOCAL_PATH_HTTP => 'DeliveryProfileLocalPathHttp',
			DeliveryProfileType::LOCAL_PATH_RTMP => 'DeliveryProfileLocalPathRtmp',
			DeliveryProfileType::LOCAL_PATH_HDS => 'DeliveryProfileLocalPathHds',
					
			DeliveryProfileType::LIVE_HLS => 'DeliveryProfileLiveAppleHttp',
			DeliveryProfileType::LIVE_HDS => 'DeliveryProfileLiveHds',
			DeliveryProfileType::LIVE_DASH => 'DeliveryProfileLiveDash',
			DeliveryProfileType::LIVE_RTMP => 'DeliveryProfileLiveRtmp',
			DeliveryProfileType::LIVE_HLS_TO_MULTICAST => "DeliveryProfileLiveAppleHttpToMulticast",
			DeliveryProfileType::LIVE_PACKAGER_HLS => 'DeliveryProfileLivePackagerHls',
			DeliveryProfileType::LIVE_PACKAGER_HDS => 'DeliveryProfileLivePackagerHds',
			DeliveryProfileType::LIVE_PACKAGER_DASH => 'DeliveryProfileLivePackagerDash',
			DeliveryProfileType::LIVE_PACKAGER_MSS => 'DeliveryProfileLivePackagerMss',
					
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
		$delivery = null;
		$deliveryAttributes = DeliveryProfileDynamicAttributes::init(null, $entryId, $streamerType);
		
		if ($streamerType == PlaybackProtocol::HTTP)
		{
			$deliveryAttributes->setMediaProtocol(infraRequestUtils::getProtocol());
			$delivery = self::getLocalDeliveryByPartner($entryId, $streamerType, $deliveryAttributes, null, false);
			
			// if a delivery profile wasn't found try again without forcing the request protocol
			if(!$delivery)
				$deliveryAttributes->setMediaProtocol(infraRequestUtils::getProtocol() == 'http' ? 'https' : 'http');
		}
		
		if(!$delivery)
			$delivery = self::getLocalDeliveryByPartner($entryId, $streamerType, $deliveryAttributes, null, false);
		
		if($delivery)
			$delivery->setDynamicAttributes($deliveryAttributes);
		
		return $delivery;
	}
	
	/**
	 * This function returns the matching delivery object for a given entry and delivery protocol. 
	 * @param string $entryId - The entry ID
	 * @param PlaybackProtocol $streamerType - The protocol
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes - constraints on delivery such as media protocol, flv support, etc..
	 * @param string $cdnHost - The requesting CdnHost if known / preffered.
	 * @param boolean $checkSecured whether we should prefer secured delivery profiles.
	 * @return DeliveryProfile
	 */
	public static function getLocalDeliveryByPartner($entryId, $streamerType = PlaybackProtocol::HTTP, DeliveryProfileDynamicAttributes $deliveryAttributes, $cdnHost = null, $checkSecured = true)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			KalturaLog::err('Failed to retrieve entryID: '. $entryId);
			return null;
		}

		$partnerId = $entry->getPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
		{
			KalturaLog::err('Failed to retrieve partnerId: '. $partnerId);
			return null;
		}

		$isSecured = $checkSecured ? self::isSecured($partner, $entry) : false;
		$isLive = $entry->getType() == entryType::LIVE_STREAM;

		$delivery = self::getDeliveryByPartner($entry, $partner, $streamerType, $deliveryAttributes, $cdnHost, $isSecured, $isLive);
		if($delivery)
			$delivery->setEntryId($entryId);
		
		return $delivery;
	}
		
	/**
	 * This function returns the matching delivery object for a given partner and delivery protocol
	 * @param entry $entry server entryId
	 * @param Partner $partner The partner
	 * @param PlaybackProtocol $streamerType - The protocol
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes - constraints on delivery such as media protocol, flv support, etc..
	 * @param string $cdnHost - The requesting CdnHost if known / preffered.
	 * @param boolean $isSecured whether we're interested in secured delivery profile
	 * @param boolean $isLive should fetch live or vod delivery profiles
	 *
	 * @return DeliveryProfile $delivery
	 */
	public static function getDeliveryByPartner(entry $entry, Partner $partner, $streamerType, DeliveryProfileDynamicAttributes $deliveryAttributes, $cdnHost = null, $isSecured = false, $isLive = false)
	{
		if($deliveryAttributes->getRequestedDeliveryProfileIds())
			$deliveryIds = $deliveryAttributes->getRequestedDeliveryProfileIds();
		else
			$deliveryIds = self::getCustomDeliveryIds($entry, $partner, $streamerType, $isLive, $deliveryAttributes);

		// if the partner has an override for the required format on the partner object - use that
		if(count($deliveryIds))
		{
			$deliveries = self::getDeliveryByIds($deliveryIds, $partner, $streamerType, $deliveryAttributes, $cdnHost, $isSecured, $isLive);
		}
		// Else catch the default by the protocol
		else
		{
			$deliveries = self::getDefaultDelivery($partner, $streamerType, $deliveryAttributes, $cdnHost, $isSecured, $isLive);
		}

		return self::selectDeliveryByDeliveryAttributes($partner->getId(), $streamerType, $deliveries, $deliveryAttributes);
	}

	protected static function getCustomDeliveryIds($entry, $partner, $streamerType, $isLive, DeliveryProfileDynamicAttributes $deliveryAttributes)
	{
		$deliveryIds = array();

		if($isLive)
		{
			/* @var $entry LiveEntry */
			/* @var $partner Partner */
			$playableServerNode = $entry->getMediaServer();
			if($playableServerNode)
			{
				/* @var WowzaMediaServerNode $playableServerNode */
				$machineDeliveryIds = $playableServerNode->getDeliveryProfileIds();
				if(array_key_exists($streamerType, $machineDeliveryIds))
				{
					$deliveryIds = explode(",", $machineDeliveryIds[$streamerType]);
				}
			}
			
			if(!count($deliveryIds))
			{
				$partnerLiveDeliveryIds = $partner->getLiveDeliveryProfileIds();
				if(array_key_exists($streamerType, $partnerLiveDeliveryIds))
				{
					$deliveryIds = $partnerLiveDeliveryIds[$streamerType];
				}
			}
			
			if(!count($deliveryIds) && in_array($entry->getSource(), array(EntrySourceType::MANUAL_LIVE_STREAM, EntrySourceType::AKAMAI_UNIVERSAL_LIVE)))
			{
				$customLiveStreamConfigurations = array();
				if($entry->getHlsStreamUrl($deliveryAttributes->getMediaProtocol()))
				{
					$hlsLiveStreamConfig = new kLiveStreamConfiguration();
					$hlsLiveStreamConfig->setUrl($entry->getHlsStreamUrl($deliveryAttributes->getMediaProtocol()));
					$hlsLiveStreamConfig->setProtocol(PlaybackProtocol::APPLE_HTTP);
					$customLiveStreamConfigurations[] = $hlsLiveStreamConfig;
				}
				
				$customLiveStreamConfigurations = array_merge($entry->getCustomLiveStreamConfigurations(), $customLiveStreamConfigurations);
				foreach($customLiveStreamConfigurations as $customLiveStreamConfiguration)
				{
					/* @var $customLiveStreamConfiguration kLiveStreamConfiguration */
					if($streamerType == $customLiveStreamConfiguration->getProtocol())
					{
						$cdnHost = parse_url($customLiveStreamConfiguration->getUrl(), PHP_URL_HOST);
						$customLiveDelivery = self::getLiveDeliveryProfileByHostName($cdnHost, $deliveryAttributes);
						$deliveryIds = array($customLiveDelivery->getId());
					}
				}
			}
		}
		else
		{
			$partnerDeliveryIds = $partner->getDeliveryProfileIds();
			if(array_key_exists($streamerType, $partnerDeliveryIds))
			{
				$deliveryIds = $partnerDeliveryIds[$streamerType];
			}
		}

		return $deliveryIds;
	}

	protected static function selectDeliveryByDeliveryAttributes($partnerId, $streamerType, $deliveries, DeliveryProfileDynamicAttributes $deliveryAttributes)
	{
		$delivery = self::selectByDeliveryAttributes($deliveries, $deliveryAttributes);

		if($delivery)
		{
			KalturaLog::info("Delivery ID for partnerId [$partnerId] and streamer type [$streamerType] is " . $delivery->getId());
		} else
		{
			$mediaProtocol = $deliveryAttributes ? $deliveryAttributes->getMediaProtocol() : null;
			KalturaLog::err("Delivery ID can't be determined for partnerId [$partnerId] streamer type [$streamerType] and media protocol [$mediaProtocol]");
		}

		return $delivery;
	}

	// if the partner has an override for the required format on the partner object - use that
	protected static function getDeliveryByIds($deliveryIds, Partner $partner, $streamerType, DeliveryProfileDynamicAttributes $deliveryAttributes, $cdnHost = null, $isSecured = false, $isLive = false)
	{
		self::filterDeliveryProfilesArray($deliveryIds, $deliveryAttributes);

		$c = new Criteria();
		$c->add(DeliveryProfilePeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $partner->getId()), Criteria::IN);
		if (!(empty($deliveryIds)))
			$c->add(DeliveryProfilePeer::ID, $deliveryIds, Criteria::IN);

		if($isLive)
			$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::IN);
		else
			$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::NOT_IN);

		$deliveries = DeliveryProfilePeer::doSelect($c);

		$cmp = new DeliveryProfileComparator($isSecured, $cdnHost);
		array_walk($deliveries, "DeliveryProfileComparator::decorateWithUserOrder", $partner->getDeliveryProfileIds()[$deliveryAttributes->getFormat()]);
		uasort($deliveries, array($cmp, "compare"));

		return $deliveries;
	}

	protected static function getDefaultDelivery(Partner $partner, $streamerType, DeliveryProfileDynamicAttributes $deliveryAttributes, $cdnHost = null, $isSecured = false, $isLive = false)
	{
		$c = new Criteria();
		$c->add(DeliveryProfilePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
		$c->add(DeliveryProfilePeer::IS_DEFAULT, true);
		$c->add(DeliveryProfilePeer::STREAMER_TYPE, $streamerType);

		$c->addDescendingOrderByColumn('(' . DeliveryProfilePeer::HOST_NAME . ' is not null)');

		if($isLive)
			$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::IN);
		else
			$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::NOT_IN);

		if($isSecured)
			$c->addDescendingOrderByColumn('(' . DeliveryProfilePeer::TOKENIZER . ' is not null)');
		else
			$c->addDescendingOrderByColumn('(' . DeliveryProfilePeer::TOKENIZER . ' is null)');

		self::filterDeliveryProfilesCriteria($c, $deliveryAttributes);

		$orderBy = "(" . DeliveryProfilePeer::PARTNER_ID . "<>{$partner->getId()})";
		$c->addAscendingOrderByColumn($orderBy);
		$c->addAscendingOrderByColumn(DeliveryProfilePeer::PRIORITY);

		$deliveries = self::doSelect($c);

		return $deliveries;
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
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes - containing requested storageId, entryId, format and media protocol
	 * @return DeliveryProfile
	 */
	public static function getRemoteDeliveryByStorageId(DeliveryProfileDynamicAttributes $deliveryAttributes, 
			FileSync $fileSync = null, asset $asset = null) {

		$storageId = $deliveryAttributes->getStorageId();
		$storageProfile = StorageProfilePeer::retrieveByPK($storageId);
		if(!$storageProfile) {
			KalturaLog::err('Couldn\'t retrieve storageId: '. $storageId);
			return null;
		}

		$streamerType = $deliveryAttributes->getFormat();
		$deliveryIds = $storageProfile->getDeliveryProfileIds();

		if(!array_key_exists($streamerType, $deliveryIds)) {
			KalturaLog::err("Delivery ID can't be determined for storageId [$storageId] ( PartnerId [" .  $storageProfile->getPartnerId() . "] ) and streamer type [ $streamerType ]");
			return null;
		}

		$RequestedDeliveryProfileId = $deliveryAttributes->getRequestedDeliveryProfileIds();
		if($RequestedDeliveryProfileId)
		{
			$intersectDeliveryProfileIds = array_intersect($deliveryIds[$streamerType], $RequestedDeliveryProfileId);
			if(count($intersectDeliveryProfileIds))
				$deliveryIds = array($streamerType => $intersectDeliveryProfileIds);
			else
			{
				KalturaLog::err('Requested delivery profile ids ['. implode("|", $intersectDeliveryProfileIds)."], can't be determined for storageId [$storageId] ,PartnerId [".$storageProfile->getPartnerId()."] and streamer type [$streamerType]");
				return null;
			}
		}

		$deliveryIds = $deliveryIds[$streamerType];
		self::filterDeliveryProfilesArray($deliveryIds, $deliveryAttributes);
		
		$deliveries = DeliveryProfilePeer::retrieveByPKs($deliveryIds);
		$delivery = self::selectByDeliveryAttributes($deliveries, $deliveryAttributes);
		if($delivery) {
			KalturaLog::info("Delivery ID for storageId [$storageId] ( PartnerId [" . $storageProfile->getPartnerId() . "] ) and streamer type [$streamerType] is " . $delivery->getId());
			$delivery->setEntryId($deliveryAttributes->getEntryId());
			$delivery->setStorageId($storageId);
			
			$delivery->initDeliveryDynamicAttributes($fileSync, $asset);
		} else {
			KalturaLog::err("Delivery ID can't be determined for storageId [$storageId] ( PartnerId [" .  $storageProfile->getPartnerId() . "] ) streamer type [$streamerType] and media protocol [".$deliveryAttributes->getMediaProtocol()."]");
		}
		
		return $delivery;
	}
	
	/**
	 * Selects between a list of deliveries by a requested media protocol
	 * @param array $deliveries list of deliveries
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes - constraints on delivery such as media protocol, flv support, etc..
	 * @return The matching DeliveryProfile if exists, or null otherwise
	 */
	protected static function selectByDeliveryAttributes($deliveries, DeliveryProfileDynamicAttributes $deliveryAttributes) {
		$partialSupport = null;
		
		// find either a fully supported deliveryProfile or the first partial supported one
		foreach ($deliveries as $delivery) {
			$result = $delivery->supportsDeliveryDynamicAttributes($deliveryAttributes);
			if ($result == DeliveryProfile::DYNAMIC_ATTRIBUTES_FULL_SUPPORT)
				return $delivery;
			else if (!$partialSupport && $result == DeliveryProfile::DYNAMIC_ATTRIBUTES_PARTIAL_SUPPORT)
				$partialSupport = $delivery;
		}
		
		return $partialSupport;
	}
	
	/**
	 * Filters an array of delivery profile ids according to the access control set in the $deliveryAttributes
	 * @param array $deliveryIds an array of delivery profile ids
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes        	
	 */
	protected static function filterDeliveryProfilesArray(&$deliveryIds, DeliveryProfileDynamicAttributes $deliveryAttributes) {
		$aclIds = $deliveryAttributes->getDeliveryProfileIds ();
		if ($aclIds) {
			if ($deliveryAttributes->getIsDeliveryProfilesBlockedList ())
				$deliveryIds = array_diff ( $deliveryIds, $aclIds );
			else
				$deliveryIds = array_intersect ( $deliveryIds, $aclIds );
		}
	}

	/**
	 * Adds a filter to a Criteria according to the access control set in the $deliveryAttributes
	 * @param Criteria $c - a Criteria
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes
	 */
	protected static function filterDeliveryProfilesCriteria(&$c, DeliveryProfileDynamicAttributes $deliveryAttributes) {
		$aclIds = $deliveryAttributes->getDeliveryProfileIds ();
		if ($aclIds) {
			$c->add ( DeliveryProfilePeer::ID, $aclIds, $deliveryAttributes->getIsDeliveryProfilesBlockedList () ? Criteria::NOT_IN : Criteria::IN );
		}
	}
	
	/**
	 * Returns the delivery profile by host name (or returns one of the defaults)
	 * @param string $cdnHost The host we're looking for
	 * @param string $entryId The entry for which we search for the delivery profile
	 * @param PlaybackProtocol $streamerType - The protocol
	 * @param string $mediaProtocol - rtmp/rtmpe/https...
	 * @return DeliveryProfile
	 */
	public static function getLiveDeliveryProfileByHostName($cdnHost, DeliveryProfileDynamicAttributes $deliveryAttributes) {
		$entryId = $deliveryAttributes->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry) {
			KalturaLog::err('Failed to retrieve entryId: '. $entryId);
			return null;
		}
		$partnerId = $entry->getPartnerId();
		$streamerType = $deliveryAttributes->getFormat();
		
		$c = new Criteria();
		$c->add(DeliveryProfilePeer::PARTNER_ID, array(PartnerPeer::GLOBAL_PARTNER, $partnerId), Criteria::IN); 
		$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::IN);
		
		$hostCond = $c->getNewCriterion(DeliveryProfilePeer::HOST_NAME, $cdnHost);
		$hostCond->addOr($c->getNewCriterion(DeliveryProfilePeer::HOST_NAME, null, Criteria::ISNULL));
		
		$c->addAnd($hostCond);
		$c->add(DeliveryProfilePeer::STREAMER_TYPE, $streamerType);
		
		self::filterDeliveryProfilesCriteria($c, $deliveryAttributes);
		
		$c->addDescendingOrderByColumn('(' . DeliveryProfilePeer::HOST_NAME . ' is not null)');
		$orderBy = "(" . DeliveryProfilePeer::PARTNER_ID . "<>{$partnerId})";
		$c->addAscendingOrderByColumn($orderBy);
			
		$deliveries = self::doSelect($c);
		
		$delivery = self::selectByDeliveryAttributes($deliveries, $deliveryAttributes);
		if($delivery) {
			KalturaLog::info("Delivery ID for Host Name: [$cdnHost] and streamer type: [$streamerType] is [" . $delivery->getId());
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
	public static function getAllLiveDeliveryProfileTypes()
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
	
	public static function retrieveByTypeAndPks($pks, $type)
	{
		if(!count($pks))
			return array();
		
		$criteria = new Criteria();
		$criteria->add(DeliveryProfilePeer::ID, $pks, Criteria::IN);
		$criteria->add(DeliveryProfilePeer::TYPE, $type);
		
		return DeliveryProfilePeer::doSelect($criteria);
	}

	public static function getDeliveryProfilesByIds($entry, $deliveryIds, Partner $partner, DeliveryProfileDynamicAttributes $deliveryAttributes, $checkSecured = true)
	{
		if (count($deliveryIds))
		{
			$isSecured = $checkSecured ? self::isSecured($partner, $entry) : false;
			$isLive = $entry->getType() == entryType::LIVE_STREAM;
			return self::getDeliveryByIds($deliveryIds, $partner, null, $deliveryAttributes, null, $isSecured, $isLive);
		}

		return array();
	}

	public static function getDefaultDeliveriesFilteredByStreamerTypes($entry, Partner $partner, $excludedStreamerTypes)
	{
		$deliveryAttributes = new DeliveryProfileDynamicAttributes();
		$isLive = $entry->getType() == entryType::LIVE_STREAM;

		return self::getDefaultDeliveryProfiles($partner, $deliveryAttributes, $isLive, $excludedStreamerTypes);
	}

	protected static function getDefaultDeliveryProfiles(Partner $partner, DeliveryProfileDynamicAttributes $deliveryAttributes, $isLive = false, $excludedStreamerTypes = array())
	{
		$c = new Criteria();
		$c->add(DeliveryProfilePeer::PARTNER_ID, PartnerPeer::GLOBAL_PARTNER);
		$c->add(DeliveryProfilePeer::IS_DEFAULT, true);
		if (count($excludedStreamerTypes))
			$c->add(DeliveryProfilePeer::STREAMER_TYPE, $excludedStreamerTypes, Criteria::NOT_IN);

		$c->addDescendingOrderByColumn('(' . DeliveryProfilePeer::HOST_NAME . ' is not null)');

		if($isLive)
			$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::IN);
		else
			$c->add(DeliveryProfilePeer::TYPE, self::getAllLiveDeliveryProfileTypes(), Criteria::NOT_IN);

		self::filterDeliveryProfilesCriteria($c, $deliveryAttributes);

		$orderBy = "(" . DeliveryProfilePeer::PARTNER_ID . "<>{$partner->getId()})";
		$c->addAscendingOrderByColumn($orderBy);

		$deliveries = self::doSelect($c);

		return $deliveries;
	}

	public static function getCustomDeliveryProfileIds($entry, $partner, DeliveryProfileDynamicAttributes $deliveryAttributes)
	{
		$deliveryIds = array();

		if( $entry->getType() == entryType::LIVE_STREAM )
		{
			if( in_array($entry->getSource(), array(EntrySourceType::MANUAL_LIVE_STREAM, EntrySourceType::AKAMAI_UNIVERSAL_LIVE)))
			{
				$customLiveStreamConfigurations = array();
				if($entry->getHlsStreamUrl($deliveryAttributes->getMediaProtocol()))
				{
					$hlsLiveStreamConfig = new kLiveStreamConfiguration();
					$hlsLiveStreamConfig->setUrl($entry->getHlsStreamUrl($deliveryAttributes->getMediaProtocol()));
					$hlsLiveStreamConfig->setProtocol(PlaybackProtocol::APPLE_HTTP);
					$customLiveStreamConfigurations[] = $hlsLiveStreamConfig;
				}

				$customLiveStreamConfigurations = array_merge($entry->getCustomLiveStreamConfigurations(), $customLiveStreamConfigurations);
				foreach($customLiveStreamConfigurations as $customLiveStreamConfiguration)
				{
					/* @var $customLiveStreamConfiguration kLiveStreamConfiguration */
					$cdnHost = parse_url($customLiveStreamConfiguration->getUrl(), PHP_URL_HOST);
					$deliveryAttributes->setFormat($customLiveStreamConfiguration->getProtocol());
					$customLiveDelivery = self::getLiveDeliveryProfileByHostName($cdnHost, $deliveryAttributes);
					if ($customLiveDelivery)
						$deliveryIds[] = array($customLiveDelivery->getId());
				}
			}
			else
			{
				/* @var $entry LiveEntry */
				/* @var $partner Partner */
				$playableServerNode = $entry->getMediaServer();
				if($playableServerNode)
				{
					/* @var WowzaMediaServerNode $playableServerNode */
					$deliveryIds[] = $playableServerNode->getDeliveryProfileIds();
				}

				$deliveryIds[] = $partner->getLiveDeliveryProfileIds();
				$deliveryIds = call_user_func_array('array_merge', $deliveryIds);
			}
		}
		else
		{
			$deliveryIds = $partner->getDeliveryProfileIds();
		}

		return $deliveryIds;
	}

} // DeliveryProfilePeer


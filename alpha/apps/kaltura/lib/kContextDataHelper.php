<?php
/**
 * @package Core
 * @subpackage utils
 *
 */
class kContextDataHelper
{
	const ALL_TAGS = 'all';
	const DEFAULT_SERVE_VOD_FROM_LIVE_DURATION = 300000;
	
	/**
	 * 
	 * @var array
	 */
	private $allowedFlavorAssets = array();
	
	/**
	 * 
	 * @var int
	 */
	private $msDuration = 0;
	
	/**
	 * 
	 * @var bool
	 */
	private $isSecured = false;
	
	/**
	 * 
	 * @var bool
	 */
	private $isAdmin = false;
	
	/**
	 * 
	 * @var bool
	 */
	private $disableCache = false;
	
	/**
	 * 
	 * @var string
	 */
	private $selectedTag = null;
	
	/**
	 * the result of applyContext
	 * @var kEntryContextDataResult
	 */
	private $contextDataResult;
	
	/**
	 * 
	 * @var entry
	 */
	private $entry;
	
	/**
	 * 
	 * @var Partner
	 */
	private $partner;
	
	/**
	 * 
	 * @var asset
	 */
	private $asset;
	
	private $storageProfilesXML = null;
	
	private $streamerType = null;
	
	private $mediaProtocol = null;
	
	/**
	 * 
	 * @param entry $entry
	 * @param Partner $partner
	 * @param asset $asset
	 */
	public function __construct(entry $entry, Partner $partner, asset $asset = null)
	{
		$this->entry = $entry;
		$this->partner = $partner;
		$this->asset = $asset;
	}
	
	/**
	 * @return array $allowedFlavorAssets
	 */
	public function getAllowedFlavorAssets() {
		return $this->allowedFlavorAssets;
	}
	
	/**
	 * @return int $msDuration
	 */
	public function getMsDuration() {
		return $this->msDuration;
	}

	/**
	 * @return bool $isAdmin
	 */
	public function getIsAdmin() {
		return $this->isAdmin;
	}

	/**
	 * @return bool $disableCache
	 */
	public function getDisableCache() {
		return $this->disableCache;
	}

	/**
	 * @return kEntryContextDataResult $contextDataResult
	 */
	public function getContextDataResult() {
		return $this->contextDataResult;
	}

	/**
	 * @return string $storageProfilesXML
	 */
	public function getStorageProfilesXML() {
		return $this->storageProfilesXML;
	}

	/**
	 * @return string $streamerType
	 */
	public function getStreamerType() {
		return $this->streamerType;
	}

	public function setStreamerType($streamerType) {
		$this->streamerType = $streamerType;
	}

	/**
	 * @return string $mediaProtocol
	 */
	public function getMediaProtocol() {
		return $this->mediaProtocol;
	}

	public function setMediaProtocol($protocol) {
		 $this->mediaProtocol = $protocol;
	}

	public function buildContextDataResult(accessControlScope $scope, $flavorTags, $streamerType, $mediaProtocol, $shouldHandleRuleCodes = false)
	{
		$this->streamerType = $streamerType;
		$this->mediaProtocol = $mediaProtocol;
		$this->isAdmin = $scope->getKs() ? $scope->getKs()->isAdmin() : false;
		$this->contextDataResult = new kEntryContextDataResult();

		$this->contextDataResult->setShouldHandleRuleCodes($shouldHandleRuleCodes);
		
		$this->applyAccessControlOnContextData($scope);
		$this->setContextDataFlavorAssets($flavorTags);
		$this->setContextDataStorageProfilesXml();	
		$this->setContextDataStreamerTypeAndMediaProtocol($scope, $flavorTags);
		
	}
	
	private function applyAccessControlOnContextData(accessControlScope $accessControlScope)
	{
		$accessControl = $this->entry->getAccessControl();		
		/* @var $accessControl accessControl */
		if ($accessControl && $accessControl->hasRules())
		{
			$this->isSecured = true;
			if (kConf::hasMap("optimized_playback"))
			{
				$partnerId = $accessControl->getPartnerId();
				$optimizedPlayback = kConf::getMap("optimized_playback");
				if (array_key_exists($partnerId, $optimizedPlayback))
				{
					$params = $optimizedPlayback[$partnerId];
					if (array_key_exists('cache_kdp_access_control', $params) && $params['cache_kdp_access_control'] &&
					 	(strpos(strtolower(kCurrentContext::$client_lang), "kdp") !== false || strpos(strtolower(kCurrentContext::$client_lang), "html") !== false ))
						return;
				}
			}		

			$accessControlScope->setEntryId($this->entry->getId());
			$this->disableCache = $accessControl->applyContext($this->contextDataResult, $accessControlScope); 
		}
	}
	
	private function setContextDataFlavorAssets($flavorTags)
	{
		if ($this->entry->getType() == entryType::PLAYLIST &&
			$this->entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_TEXT)
		{
			list($entryIds, $durations, $mediaEntry, $captionFiles) =
				myPlaylistUtils::executeStitchedPlaylist($this->entry);
			if (!$mediaEntry)
			{
				return;
			}

			$mediaEntryId = $mediaEntry->getId();
			$this->msDuration = array_sum($durations);
		}
		elseif (myEntryUtils::shouldServeVodFromLive($this->entry))
		{
			$mediaEntryId = $this->entry->getRootEntryId();
			$liveEntry = entryPeer::retrieveByPK($mediaEntryId);
			
			if($liveEntry && $liveEntry->getLengthInMsecs())
				$this->msDuration = $liveEntry->getLengthInMsecs();
			elseif($this->entry->getLengthInMsecs())
				$this->msDuration = $this->entry->getLengthInMsecs();
			else
				$this->msDuration = self::DEFAULT_SERVE_VOD_FROM_LIVE_DURATION;
		}
		else
		{
			$mediaEntryId = $this->entry->getId();
			$this->msDuration = $this->entry->getLengthInMsecs();
		}

		$flavorParamsIds = null;
		$flavorParamsNotIn = false;
		
		foreach ($this->contextDataResult->getActions() as $action) 
		{	
			if($action->getType() == RuleActionType::BLOCK)
			{
				//in case of block action do not set the list of flavors
				return;
			}
			if($action->getType() == RuleActionType::LIMIT_FLAVORS)
			{
				/* @var $action kAccessControlLimitFlavorsAction */
				$flavorParamsIds = explode(',', $action->getFlavorParamsIds());
				$flavorParamsNotIn = $action->getIsBlockedList();
			}
		}	
		
		$flavorAssets = array();
		if (is_null($this->asset))
		{
			if(count($flavorParamsIds))
				$flavorAssets = assetPeer::retrieveReadyByEntryIdAndFlavorParams($mediaEntryId, $flavorParamsIds, $flavorParamsNotIn);
			else 
				$flavorAssets = assetPeer::retrieveFlavorsByEntryIdAndStatus($mediaEntryId, null, array(flavorAsset::ASSET_STATUS_READY));
			
			if ($mediaEntryId != $this->entry->getId())
			{
				// hack: setting the entry id of the flavors to the original playlist id
				//		since the player uses it in the playManifest url 
				foreach($flavorAssets as $flavorAsset)
				{
					$flavorAsset->setEntryId($this->entry->getId());
				}
			}
		}
		else
		{
			$flavorAllowed = true;	
			if(count($flavorParamsIds))
				$flavorAllowed = $this->isFlavorAllowed($this->asset->getFlavorParamsId(), $flavorParamsIds, $flavorParamsNotIn); 	
			if($flavorAllowed)
				$flavorAssets[] = $this->asset;
		}
		$this->filterFlavorAssetsByTags($flavorAssets, $flavorTags);
		
		//If serving vod from live use live entry to select the correct playback protocols
		if(myEntryUtils::shouldServeVodFromLive($this->entry))
		{
			$liveEntry = entryPeer::retrieveByPK($mediaEntryId);
			$this->entry = $liveEntry;
		}
	}
	
	private function isFlavorAllowed($flavorParamsId, array $flavorParamsIds, $flavorParamsNotIn)
	{
		$exists = in_array($flavorParamsId, $flavorParamsIds);
		if($flavorParamsNotIn)
			return !$exists;
		else 
			return $exists;
	}
	
	private function filterFlavorAssetsByTags($flavorAssets, $flavorTags)
	{
		if($flavorTags == self::ALL_TAGS)
		{
			$this->allowedFlavorAssets = $flavorAssets;
			return;
		}
		if(!$flavorTags)
			$flavorTags = flavorParams::TAG_MBR.','.flavorParams::TAG_WEB;
			
		$tagsArray = explode(',', $flavorTags);
				
		foreach ($tagsArray as $tag) 
		{
			$filteredFlavorAssets = array();
			foreach ($flavorAssets as $flavorAsset) 
			{
				if($flavorAsset->hasTag($tag))
					$filteredFlavorAssets[] = $flavorAsset;
			}
			if(count($filteredFlavorAssets))
			{
				$this->selectedTag = $tag;
				break;
			}
		}
		if(count($filteredFlavorAssets))
			$this->allowedFlavorAssets = $filteredFlavorAssets;
	}
	
	private function setContextDataStorageProfilesXml()
	{
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE_DELIVERY_PRIORITY, $this->entry->getPartnerId()) &&
			$this->partner->getStorageServePriority() != StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
		{
			$asset = reset($this->allowedFlavorAssets);		
			if(!$asset)
				return;			
			$assetSyncKey = $asset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$fileSyncs = kFileSyncUtils::getAllReadyExternalFileSyncsForKey($assetSyncKey);
					
			$storageProfilesXML = new SimpleXMLElement("<StorageProfiles/>");
			foreach ($fileSyncs as $fileSync)
			{
				$storageProfileId = $fileSync->getDc();
				
				$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
				$deliveryProfileRtmp = DeliveryProfilePeer::getRemoteDeliveryByStorageId(DeliveryProfileDynamicAttributes::init($storageProfileId, $this->entry, PlaybackProtocol::RTMP));
				
				if ( is_null($deliveryProfileRtmp)
					&& (!$this->streamerType || $this->streamerType == PlaybackProtocol::AUTO))
				{
					$this->streamerType = PlaybackProtocol::HTTP;
					$this->mediaProtocol = PlaybackProtocol::HTTP;
				}
				$storageProfileXML = $storageProfilesXML->addChild("StorageProfile");
				
				$storageProfileXML->addAttribute("storageProfileId",$storageProfileId);
				$storageProfileXML->addChild("Name", $storageProfile->getName());
				$storageProfileXML->addChild("SystemName", $storageProfile->getSystemName());				
			}

			$this->storageProfilesXML = $storageProfilesXML->saveXML();			
		}
	}
	
	
	private function setContextDataStreamerTypeAndMediaProtocol(accessControlScope $scope, $flavorTags)
	{
		if($this->streamerType && $this->streamerType != PlaybackProtocol::AUTO)
		{
			$this->mediaProtocol = $this->mediaProtocol ? $this->mediaProtocol : $this->streamerType;
		}
		elseif ($this->entry instanceof LiveEntry)
		{
			$protocols = array();
			
			if(!in_array($this->entry->getSource(), LiveEntry::$kalturaLiveSourceTypes))
				$protocols[] = PlaybackProtocol::AKAMAI_HDS;
				
			$protocols[] = PlaybackProtocol::HDS;

			if ($this->entry->getStreamName())
				$this->streamerType = PlaybackProtocol::RTMP;
			
			foreach ($protocols as $protocol)
			{
				$config = $this->entry->getLiveStreamConfigurationByProtocol($protocol, requestUtils::getProtocol());
				if ($config)
				{	
					$this->streamerType = $protocol;
					break;
				}
			}	
			
			if(in_array($this->entry->getSource(), array(EntrySourceType::LIVE_STREAM, EntrySourceType::LIVE_STREAM_ONTEXTDATA_CAPTIONS)))
				$this->streamerType = PlaybackProtocol::HDS;
			if($this->entry->getSource() == EntrySourceType::AKAMAI_LIVE)
				$this->streamerType = PlaybackProtocol::RTMP;
			if($this->entry->getSource() == EntrySourceType::AKAMAI_UNIVERSAL_LIVE)
				$this->streamerType = PlaybackProtocol::AKAMAI_HDS;
		}
		elseif ($this->entry->getType() == entryType::PLAYLIST)
		{
			$this->streamerType = PlaybackProtocol::AKAMAI_HDS;
			$this->mediaProtocol = infraRequestUtils::getProtocol();
		}
		else
		{
			$this->isSecured = $this->isSecured || PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT_USED, $this->entry->getPartnerId());
			$forcedDeliveryTypeKey = kDeliveryUtils::getForcedDeliveryTypeKey($this->selectedTag);
			
			if($forcedDeliveryTypeKey)
				$defaultDeliveryTypeKey = $forcedDeliveryTypeKey;
			else 
				$defaultDeliveryTypeKey = $this->partner->getDefaultDeliveryType();
				
			if (!$defaultDeliveryTypeKey || $defaultDeliveryTypeKey == PlaybackProtocol::AUTO)
				$deliveryType = $this->selectDeliveryTypeForAuto();
			else 
				$deliveryType = kDeliveryUtils::getDeliveryTypeFromConfig($defaultDeliveryTypeKey);
			
			if(!$deliveryType)
				$deliveryType = array();
				
			$this->streamerType = kDeliveryUtils::getStreamerType($deliveryType);
			$this->mediaProtocol = kDeliveryUtils::getMediaProtocol($deliveryType);
		}
		
		$httpStreamerTypes = array(
			PlaybackProtocol::HTTP,
			PlaybackProtocol::HDS,
			PlaybackProtocol::HLS,
			PlaybackProtocol::SILVER_LIGHT,
			PlaybackProtocol::MPEG_DASH,
		);
		
		if (in_array($this->streamerType, $httpStreamerTypes))
			$this->mediaProtocol = infraRequestUtils::getProtocol();
		
		if ($this->streamerType == PlaybackProtocol::AKAMAI_HD || $this->streamerType == PlaybackProtocol::AKAMAI_HDS)
			$this->mediaProtocol = PlaybackProtocol::HTTP;
		
		//If a plugin can determine the streamerType and mediaProtocol, prefer plugin result
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaContextDataHelper');
		foreach ($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaContextDataHelper */
			$this->streamerType = $pluginInstance->getContextDataStreamerType($scope, $flavorTags, $this->streamerType);
			$this->mediaProtocol = $pluginInstance->getContextDataMediaProtocol($scope, $flavorTags, $this->streamerType, $this->mediaProtocol);
		}
	}
	
	private function selectDeliveryTypeForAuto()
	{
		$enabledDeliveryTypes = $this->partner->getDeliveryTypes();
		$deliveryType = null;
		foreach ($enabledDeliveryTypes as $enabledDeliveryTypeKey => $values){
			if ($enabledDeliveryTypeKey == PlaybackProtocol::AUTO)
				unset($enabledDeliveryTypes[$enabledDeliveryTypeKey]);
			else if ($this->asset && $enabledDeliveryTypeKey == PlaybackProtocol::HTTP)	
				$deliveryType = $enabledDeliveryTypes[$enabledDeliveryTypeKey];
		}
				
		if (!count($enabledDeliveryTypes))
		{
			KalturaLog::err('At least one non auto delivery type must be specified');
			return array();
		}

		if (is_null($deliveryType)){
			$deliveryTypeKeys = array();
			$deliveryTypeName = null; 
			if($this->isSecured)
				$deliveryTypeKeys[] = 'secured_default_delivery_type';
			if($this->entry->getDuration() <= kConf::get('short_entries_max_duration'))
				$deliveryTypeKeys[] = 'short_entries_default_delivery_type';
			$deliveryTypeKeys[] = 'default_delivery_type';
	
			reset($enabledDeliveryTypes);
			$deliveryTypeName = key($enabledDeliveryTypes);
			foreach ($deliveryTypeKeys as $deliveryTypeKey)
			{
				$deliveryTypesToValidate = kConf::get($deliveryTypeKey);
				$deliveryTypesToValidate = explode(',', $deliveryTypesToValidate);
				foreach ($deliveryTypesToValidate as $deliveryTypeToValidate)
				{
		            if (isset ($enabledDeliveryTypes[$deliveryTypeToValidate]))
		            {
		             	$deliveryTypeName = $deliveryTypeToValidate;
		             	//When match is found break this loop and outer loop as well (http://www.php.net/manual/en/control-structures.break.php)
		                break 2;
					}
				}
			}		
			$deliveryType = $enabledDeliveryTypes[$deliveryTypeName];	
		}
		return $deliveryType;
	}
}
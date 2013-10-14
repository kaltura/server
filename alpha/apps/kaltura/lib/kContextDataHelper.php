<?php
/**
 * @package Core
 * @subpackage utils
 *
 */
class kContextDataHelper
{
	const ALL_TAGS = 'all';
	
	/**
	 * 
	 * @var array
	 */
	private $allowedFlavorAssets = array();
	
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

	/**
	 * @return string $mediaProtocol
	 */
	public function getMediaProtocol() {
		return $this->mediaProtocol;
	}

	public function buildContextDataResult(accessControlScope $scope, $flavorTags, $streamerType, $mediaProtocol)
	{
		$this->streamerType = $streamerType;
		$this->mediaProtocol = $mediaProtocol;
		if($scope->getKs())
			$this->isAdmin = $scope->getKs()->isAdmin();
		$this->contextDataResult = new kEntryContextDataResult();
		
		$this->applyAccessControlOnContextData($scope);
		$this->setContextDataFlavorAssets($flavorTags);
		$this->setContextDataStorageProfilesXml();	
		$this->setContextDataStreamerTypeAndMediaProtocol($scope, $flavorTags);
		
	}
	
	private function applyAccessControlOnContextData(accessControlScope $accessControlScope)
	{
		if($this->isAdmin)
			return;
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
					if (array_key_exists('cache_kdp_access_control', $params) && $params['cache_kdp_access_control'])
						return;
				}
			}		

			$accessControlScope->setEntryId($this->entry->getId());
			$this->isAdmin = ($accessControlScope->getKs() && $accessControlScope->getKs()->isAdmin());
            
			$this->disableCache = $accessControl->applyContext($this->contextDataResult); 
		}
	}
	
	private function setContextDataFlavorAssets($flavorTags)
	{
		$flavorParamsIds = null;
		$flavorParamsNotIn = false;
		if(!$this->isAdmin)
		{
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
		}
		$flavorAssets = array();
		if (is_null($this->asset))
		{
			if(count($flavorParamsIds))
				$flavorAssets = assetPeer::retrieveReadyByEntryIdAndFlavorParams($this->entry->getId(), $flavorParamsIds, $flavorParamsNotIn);
			else 
				$flavorAssets = assetPeer::retrieveFlavorsByEntryIdAndStatus($this->entry->getId(), null, array(flavorAsset::ASSET_STATUS_READY));			
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
				
				if ( !$storageProfile->getDeliveryRmpBaseUrl()
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
		elseif ($this->entry->getType() == entryType::LIVE_STREAM)
		{
			$protocols = array(PlaybackProtocol::AKAMAI_HDS, PlaybackProtocol::HDS);
			foreach ($protocols as $protocol)
			{
				$config = kLiveStreamConfiguration::getSingleItemByPropertyValue($this->entry, 'protocol', $protocol);
				if ($config)
				{	
					$this->streamerType = $protocol;
					break;
				}
			}	
			if (!$this->streamerType || $this->streamerType == PlaybackProtocol::AUTO)
				$this->streamerType = PlaybackProtocol::RTMP;
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
			if ($this->streamerType == PlaybackProtocol::HTTP && infraRequestUtils::getProtocol() == infraRequestUtils::PROTOCOL_HTTPS)
				$this->mediaProtocol = 'https';
		}
		
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
			foreach ($deliveryTypeKeys as $deliveryTypeKey){
				$deliveryTypeToValidate = kConf::get($deliveryTypeKey);
	            if (isset ($enabledDeliveryTypes[$deliveryTypeToValidate]))
	            {
	             	$deliveryTypeName = $deliveryTypeToValidate;
	                break;
				}
			}		
			$deliveryType = $enabledDeliveryTypes[$deliveryTypeName];	
		}
		return $deliveryType;
	}
}
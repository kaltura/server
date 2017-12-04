<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class playManifestAction extends kalturaAction
{
	
	const URL = 'url';
	const DOWNLOAD = 'download';
	
	const HDNETWORKSMIL = 'hdnetworksmil';
	
	
	static protected $httpProtocols = array(
		'http',
		'https',
	);

	/**
	 * When this list start to contain plugins - 
	 * conside moving it to either kConf or as a function on the enum.
	 * @var array
	 */ 
	static protected $httpFormats = array(
		PlaybackProtocol::HTTP,
		PlaybackProtocol::SILVER_LIGHT,
		PlaybackProtocol::APPLE_HTTP,
		PlaybackProtocol::HDS,
		PlaybackProtocol::HLS,	
		PlaybackProtocol::AKAMAI_HDS,
		PlaybackProtocol::AKAMAI_HD,
		PlaybackProtocol::MPEG_DASH,
	);
	
	/**
	 * Short names for action arguments
	 * @var array
	 */
	static protected $shortNames = array(
		"cdnHost" => 'ch',
		"clipTo" => 'ct',
		"deliveryCode" => 'dc',
		"entryId" => 'e',
		"expiry" => 'ex',
		"flavorId" => 'fi',
		"flavorIds" => 'fs',
		"flavorParamId" => 'fp',
		"flavorParamIds" => 'fps',
		"format" => 'f',
		"maxBitrate" => 'mb',
		"minBitrate" => 'mib',
		"playbackContext" => 'pc',
		"preferredBitrate" => 'pb',
		"protocol" => 'pt',
		"referrer" => 'r',
		"seekFrom" => 'sf',
		"storageId" => 'si',
		"tags" => 't',
		"uiConfId" => 'ui',
	);

	const KALTURA_TOKEN_MARKER = '{kt}';
	
	/**
	 * @var string
	 */
	private $entryId;
	
	/**
	 * @var entry
	 */
	private $entry;
		
	/**
	 * @var string
	 */
	private $protocol = null;
	
	/**
	 * @var string
	 */
	private $cdnHost = null;
	
	/**
	 * @var int
	 */
	private $maxBitrate = null;
	
	/**
	 * @var int
	 */
	private $minBitrate = null;
	
	/**
	 * @var array
	 */
	private $flavorIds = null;
	
	/**
	 * @var array
	 */
	private $flavorParamsIds = null;
	
	/**
	 * @var DeliveryProfile
	 */
	private $deliveryProfile = null;
	
	/**
	 * @var KSecureEntryHelper
	 */
	private $secureEntryHelper = null;
	
	/**
	 * @var int
	 */
	private $duration = null;
	
	/**
	 * @var DeliveryProfileDynamicAttributes
	 */
	private $deliveryAttributes = null;

	/**
	 * @var array
	 */
	private $requestedDeliveryProfileIds = null;
	
	///////////////////////////////////////////////////////////////////////////////////
	//	URL tokenization functions
	
	/**
	 * @param string $url
	 * @param string $urlToken
	 * @return boolean
	 */
	static protected function validateKalturaToken($url, $urlToken)
	{
		$url = str_replace($urlToken, self::KALTURA_TOKEN_MARKER, $url);
		$calcToken = sha1(kConf::get('url_token_secret') . $url);
		return $calcToken == $urlToken;
	}
	
	///////////////////////////////////////////////////////////////////////////////////
	//	Initialization functions

	protected function initEntry()  
	{
		$this->entryId = $this->getRequestParameter ( "entryId", null );

		// look for a valid token
		$expiry = $this->getRequestParameter("expiry");
		if ($expiry && $expiry <= time())
			KExternalErrors::dieError(KExternalErrors::EXPIRED_TOKEN);

		$urlToken = $this->getRequestParameter("kt");
		if ($urlToken)
		{
			if ($_SERVER["REQUEST_METHOD"] != "GET" ||			// don't allow tokens in post requests since the token protects only the URI and not the post parameters 
				!self::validateKalturaToken($_SERVER["REQUEST_URI"], $urlToken))
				KExternalErrors::dieError(KExternalErrors::INVALID_TOKEN);
		}
		
		// initalize the context
		$ksStr = $this->getRequestParameter("ks");
		if($ksStr && !$urlToken)
		{
			try 
			{
				kCurrentContext::initKsPartnerUser($ksStr);
			}
			catch (Exception $ex)
			{
				KExternalErrors::dieError(KExternalErrors::INVALID_KS);	
			}
		}
		else
		{
			$this->entry = kCurrentContext::initPartnerByEntryId($this->entryId);
			if (!$this->entry || $this->entry->getStatus() == entryStatus::DELETED) {
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
			}
		}
		
		// no need for any further check if a token was used
		if ($urlToken)
		{
			return;
		}

		myPartnerUtils::addPartnerToCriteria ('entry' ,kCurrentContext::getCurrentPartnerId(), true);

		// enforce entitlement
		kEntitlementUtils::initEntitlementEnforcement();
		
		if(!$this->entry)
		{
			$this->entry = entryPeer::retrieveByPKNoFilter( $this->entryId );
			if (!$this->entry || $this->entry->getStatus() == entryStatus::DELETED)
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		}
		else
		{
			if(!kEntitlementUtils::isEntryEntitled($this->entry))
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		}
		
		myPartnerUtils::blockInactivePartner($this->entry->getPartnerId());
		
		// enforce access control
		$base64Referrer = $this->getRequestParameter("referrer");
		$hashes = $this->getRequestParameter("hashes");
		$keyValueHashes = array(); 
		if ($hashes)
		{
			$hashes = urldecode($hashes);
			$hashes = explode(",", $hashes);
			foreach ($hashes as $keyValueHashString)
			{
				list ($key, $value) = explode('=', $keyValueHashString);
				$keyValueHashes[$key] = $value;
			}
		}
		// replace space in the base64 string with + as space is invalid in base64 strings and caused
		// by symfony calling str_parse to replace + with spaces.
		// this happens only with params passed in the url path and not the query strings. specifically the ~ char at
		// a columns divided by 3 causes this issue (e.g. http://www.xyzw.com/~xxx)
		//replace also any - with + and _ with / 
		$referrer = base64_decode(str_replace(array('-', '_', ' '), array('+', '/', '+'), $base64Referrer));
		if (!is_string($referrer))
			$referrer = ""; // base64_decode can return binary data

		$context = $this->deliveryAttributes->getFormat() == self::DOWNLOAD ? ContextType::DOWNLOAD : ContextType::PLAY;
		
		$this->secureEntryHelper = new KSecureEntryHelper($this->entry, $ksStr, $referrer, $context, $keyValueHashes);
		
		if ($this->secureEntryHelper->shouldPreview())
		{
			$previewLengthInMsecs = $this->secureEntryHelper->getPreviewLength() * 1000;
			$entryLengthInMsecs = $this->entry->getLengthInMsecs();
			if ($previewLengthInMsecs <  $entryLengthInMsecs)
				$this->deliveryAttributes->setClipTo($previewLengthInMsecs);
		}
		else
		{
			$this->secureEntryHelper->validateForPlay();
		}
		
		if (PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT, $this->entry->getPartnerId()) || 
			$this->secureEntryHelper->hasRules())
			$this->forceUrlTokenization = true;
		
		//check if recorded entry is ready if not than serve the live entry
		if(myEntryUtils::shouldServeVodFromLive($this->entry))
		{
			$this->deliveryAttributes->setServeVodFromLive(true);
			$this->deliveryAttributes->setServeLiveAsVodEntryId($this->entryId);
			$this->entryId = $this->entry->getRootEntryId();
			$this->entry = entryPeer::retrieveByPK($this->entryId);
			if (!$this->entry || $this->entry->getStatus() == entryStatus::DELETED)
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		}
	}
	
	protected function initFlavorIds()
	{
		$flavorIds = $this->getRequestParameter ( "flavorIds", null );
		if (!is_null($flavorIds))
			$this->flavorIds = explode(',', $flavorIds);
		
		$flavorId = $this->getRequestParameter ( "flavorId", null );
		if (!is_null($flavorId))
			$this->flavorIds = array($flavorId);
						
		if (!is_null($this->flavorIds))
			return;

		$flavorParamIds = $this->getRequestParameter ( "flavorParamIds", null );
		if (!is_null($flavorParamIds))
			$this->flavorParamsIds = explode(',', $flavorParamIds);
		
		$flavorParamId = $this->getRequestParameter ( "flavorParamId", null );
		if (!is_null($flavorParamId))
			$this->flavorParamsIds = array($flavorParamId);
			
		if (is_null($this->flavorParamsIds))
			return;
			
		if($this->secureEntryHelper)
			$this->flavorParamsIds = $this->secureEntryHelper->filterAllowedFlavorParams($this->flavorParamsIds);
	}

	private static function shouldAddAltAudioFlavors($format)
	{
		$supportedProtocols = Array(PlaybackProtocol::APPLE_HTTP, PlaybackProtocol::MPEG_DASH, PlaybackProtocol::SILVER_LIGHT);
		if (in_array($format, $supportedProtocols))
			return true;

		return false;
	}

	protected function initFlavorParamsIds()
	{
		$this->initFlavorIds();
		
		if(is_null($this->flavorParamsIds) && !is_null($this->flavorIds))
		{
			$flavors = assetPeer::retrieveByIds($this->flavorIds);
			$this->flavorParamsIds = array();
			foreach($flavors as $flavor)
			{
				/* @var $flavor asset */
				$this->flavorParamsIds[] = $flavor->getFlavorParamsId();
			}
		}
		
		if(is_null($this->flavorParamsIds))
			$this->flavorParamsIds = array();

		$this->deliveryAttributes->setFlavorParamIds($this->flavorParamsIds);
	}
	
	protected function enforceEncryption()
	{
		$playbackParams = array();
		if (kConf::hasMap("optimized_playback"))
		{
			$partnerId = $this->entry->getPartnerId();
			$optimizedPlayback = kConf::getMap("optimized_playback");
			if (array_key_exists($partnerId, $optimizedPlayback))
			{
				$playbackParams = $optimizedPlayback[$partnerId];
			}
		}

		// TODO add protocol limitation action to access control
		if (array_key_exists('enforce_encryption', $playbackParams) && $playbackParams['enforce_encryption'])
		{
			if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on')
				KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED, 'unencrypted manifest request - forbidden');

			$allowedProtocols = array('https','rtmpe','rtmpte');
			if (!in_array(strtolower($this->deliveryAttributes->getMediaProtocol()) , $allowedProtocols))
				KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED, 'unencrypted playback protocol - forbidden');
		}
	}
	
	private function enforceAudioVideoEntry()
	{
		switch ($this->entry->getType())
		{
		case entryType::MEDIA_CLIP:
			if(!in_array($this->entry->getMediaType(), array(
					entry::ENTRY_MEDIA_TYPE_VIDEO,
					entry::ENTRY_MEDIA_TYPE_AUDIO)))
				KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
			break;
			
		case entryType::PLAYLIST: 
			if ($this->entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_TEXT)
				KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
			break;
				
		default:
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
		}
	}
	
	protected function shouldUseLocalFlavors($hasLocalFlavors, $hasRemoteFlavors)
	{
		switch ($this->entry->getPartner()->getStorageServePriority())
		{
		case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
			return true;
			
		case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST:
			if ($hasLocalFlavors)
				return true;
			break;

		case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST:
			if (!$hasRemoteFlavors)
				return true;
			break;
		}
		return false;
	}
	
	protected function getFlavorKeyByTag($flavorAssets, $tag, $syncKeyType) {
		if($flavorAssets)
		{
			foreach ($flavorAssets as $flavorAsset)
			{
				if($flavorAsset->hasTag($tag))
				{
					return $flavorAsset->getSyncKey($syncKeyType);
				}
			}
		}
		return null;
	}
	
	protected function initSilverLightManifest($flavorAssets)
	{
		$key = $this->getFlavorKeyByTag($flavorAssets, assetParams::TAG_ISM_MANIFEST, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				
		if(!$key)
			$key = $this->entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
			
		$localFileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
		$remoteFileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key);
		
		//To Remove - Until the migration process from asset sub type 3 to asset sub type 1 will be completed we need to support both formats
		if(!$localFileSync && !$remoteFileSync)
		{
			$key = $this->getFlavorKeyByTag($flavorAssets, assetParams::TAG_ISM_MANIFEST, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM);
			if (!$key)
			{
				return false;
			}
			$localFileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
			$remoteFileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key);
		}
		
		if ($this->shouldUseLocalFlavors($localFileSync, $remoteFileSync))
		{
			$this->deliveryAttributes->setStorageId(null);
			$this->deliveryAttributes->setManifestFileSync($localFileSync);
		}
		else
		{
			if($remoteFileSync) 
				$this->deliveryAttributes->setStorageId($remoteFileSync->getDc());
			$this->deliveryAttributes->setManifestFileSync($remoteFileSync);
		}
		
		if (!$this->deliveryAttributes->getManifestFileSync())
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
		
		return true;
	}

	protected function initSmilManifest($flavorAssets)
	{
		$key = $this->getFlavorKeyByTag($flavorAssets, assetParams::TAG_SMIL_MANIFEST, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		if (!$key)
			return false;

		$localFileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
		$remoteFileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key);
		if ($this->shouldUseLocalFlavors($localFileSync, $remoteFileSync))
		{
			$this->deliveryAttributes->setStorageId(null);
			$this->deliveryAttributes->setManifestFileSync($localFileSync);
		}
		else
		{
			if($remoteFileSync)
				$this->deliveryAttributes->setStorageId($remoteFileSync->getDc());
			$this->deliveryAttributes->setManifestFileSync($remoteFileSync);
		}

		return (!is_null($this->deliveryAttributes->getManifestFileSync()));
	}

	private function removeNotAllowedFlavors($flavorAssets)
	{
		if(!$this->secureEntryHelper)
			return $flavorAssets;

		$returnedFlavors = array();

		foreach ($flavorAssets as $flavorAsset)
		{
			if ($this->secureEntryHelper->isAssetAllowed($flavorAsset))
			{
				$returnedFlavors[] = $flavorAsset;
			}
		}
	
		return $returnedFlavors;
	}
	
	/**
	 * @param array $flavorAssets
	 * @return array
	 */
	private function removeFlavorsByBitrate($flavorAssets)
	{
		if (!($this->minBitrate || $this->maxBitrate))
			return $flavorAssets;
		$returnedFlavors = array();		
		foreach ($flavorAssets as $flavor)
		{
			//audio language assets shouldn't be filtered
			if(!($flavor->hasTag(assetParams::TAG_ALT_AUDIO) || $flavor->hasTag(assetParams::TAG_AUDIO_ONLY)))
			{
				$currentBitrate = $flavor->getBitrate();
				if ($this->minBitrate && $currentBitrate < $this->minBitrate)
					continue;
				if($this->maxBitrate && $currentBitrate > $this->maxBitrate)
					continue;
			}
			$returnedFlavors[] = $flavor;
		}
	
		return $returnedFlavors;
	}
	
	protected function shouldInitFlavorAssetsArray()
	{
		if ($this->entry->getType() == entryType::LIVE_STREAM)
			return false;
			
		if($this->deliveryAttributes->getFormat() == "hdnetwork")
			return false;
	
		if ($this->entry instanceof LiveEntry)
			return false;			// live stream entries don't have flavors
		
		return true;
	}

	protected function initPlaylistFlavorAssetArray()
	{
		list($entryIds, $durations, $mediaEntry, $captionFiles) =
			myPlaylistUtils::executeStitchedPlaylist($this->entry);
		if (!$mediaEntry)
		{
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		}

		$this->setPlaylistFlavorAssets($durations, $mediaEntry->getId());
	}

	private function filterFlavorsByAssetIdOrParamsIds($flavorAssets)
	{
		$filteredFlavorAssets = Array();

		foreach ($flavorAssets as $flavorAsset)
		{
			/**
			 * @var asset $flavorAsset
			 */
			if ($this->shouldIncludeFlavor($flavorAsset))
				$filteredFlavorAssets[] = $flavorAsset;
		}

		if(count($filteredFlavorAssets) && self::shouldAddAltAudioFlavors($this->deliveryAttributes->getFormat()))
			$this->addAltAudioFlavors($filteredFlavorAssets, $flavorAssets);
		return $filteredFlavorAssets;
	}

	private function addAltAudioFlavors(&$filteredFlavorAssets, $originalFlavorAssets)
	{
		foreach ($originalFlavorAssets as $flavorAsset)
		{
			/**
			 * @var asset $flavorAsset
			 */
			if ($flavorAsset->hasTag(assetParams::TAG_ALT_AUDIO) && !in_array($flavorAsset, $filteredFlavorAssets))
				$filteredFlavorAssets[] = $flavorAsset;
		}
	}

	private function shouldIncludeFlavor($flavorAsset)
	{
		if(($this->flavorIds && in_array($flavorAsset->getId(), $this->flavorIds)) || ($this->flavorParamsIds && in_array($flavorAsset->getFlavorParamsId(), $this->flavorParamsIds)))
			return true;

		return false;
	}

	protected function retrieveAssets()
	{
		//in case asset id specified, allow url and download regardless of asset type
		//ignoring flavor-assets due to backward compat.
		if(count($this->flavorIds) == 1 && in_array($this->deliveryAttributes->getFormat(), array(self::URL, self::DOWNLOAD)))
		{
			$asset = assetPeer::retrieveById($this->flavorIds[0]);
			if($asset && $asset->getStatus() == asset::FLAVOR_ASSET_STATUS_READY && !in_array($asset->getType(), assetPeer::retrieveAllFlavorsTypes()))
				return array($asset);
		}
		return assetPeer::retrieveReadyFlavorsByEntryId($this->entryId);
	}
	
	protected function initFlavorAssetArray()
	{
		if(!$this->shouldInitFlavorAssetsArray())
			return;
		
		$oneOnly = false;
		if(in_array($this->deliveryAttributes->getFormat(), 
			array(PlaybackProtocol::HTTP, PlaybackProtocol::RTSP, self::URL, self::DOWNLOAD)))
		{
			$oneOnly = true;
		}

		$flavorAssets = $this->retrieveAssets();
		$flavorByTags = false;
		$flavorAssets = $this->removeNotAllowedFlavors($flavorAssets);

		$filteredFlavorAssets = $this->filterFlavorsByAssetIdOrParamsIds($flavorAssets);

		if (!$filteredFlavorAssets || !count($filteredFlavorAssets))
			$flavorByTags = true;

		if($this->deliveryAttributes->getFormat() == PlaybackProtocol::SILVER_LIGHT)
		{
			if ($this->initSilverLightManifest($flavorAssets))
			{
				return;
			}
			
			// revert the tags selection unless they were explicitly set by the client
			if (!$this->getRequestParameter("tags", null))
			{
				$this->deliveryAttributes->setTags(array(array('ipadnew', 'iphonenew')));
			}
		}

		if ($flavorByTags)
		{
			$filteredFlavorAssets = $this->deliveryAttributes->filterFlavorsByTags($flavorAssets);
			if(count($filteredFlavorAssets) && self::shouldAddAltAudioFlavors($this->deliveryAttributes->getFormat()))
				$this->addAltAudioFlavors($filteredFlavorAssets, $flavorAssets);
		}

		$flavorAssets = $filteredFlavorAssets;

		if($this->deliveryAttributes->getFormat() == PlaybackProtocol::HDS || $this->deliveryAttributes->getFormat() == PlaybackProtocol::APPLE_HTTP)
		{
			// try to look for a smil manifest, if it was found, we will use it for hds and hls
			if ($this->initSmilManifest($flavorAssets))
				return;
		}

		$flavorAssetsFilteredByBitrate = $this->removeFlavorsByBitrate($flavorAssets);
		if(count($flavorAssetsFilteredByBitrate))
			$flavorAssets = $flavorAssetsFilteredByBitrate;
		
		// get flavors availability
		$servePriority = $this->entry->getPartner()->getStorageServePriority();
		
		$localFlavors = array();
		$remoteFlavorsByDc = array();
		$remoteFileSyncs = array();
		
		foreach($flavorAssets as $flavorAsset)
		{
			$flavorId = $flavorAsset->getId();
			$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);

			$c = new Criteria();
			$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
			$c->addAnd ( FileSyncPeer::STATUS , FileSync::FILE_SYNC_STATUS_READY );
			
			switch ($servePriority)
			{
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
				$c->addAnd ( FileSyncPeer::FILE_TYPE , FileSync::FILE_SYNC_FILE_TYPE_URL, Criteria::NOT_EQUAL);
				break;
				
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY:
				$c->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL);
				break;
			}
			
			if ($this->deliveryAttributes->getStorageId())
				$c->addAnd ( FileSyncPeer::DC , $this->deliveryAttributes->getStorageId() );
			
			$fileSyncs = FileSyncPeer::doSelect($c);
			foreach ($fileSyncs as $fileSync)
			{
				if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
				{
					$dc = $fileSync->getDc();
					$remoteFlavorsByDc[$dc][$flavorId] = $flavorAsset;
					$remoteFileSyncs[$dc][$flavorId] = $fileSync;
				}
				else
				{
					$localFlavors[$flavorId] = $flavorAsset;
				}
			}
		}
		
		// filter out any invalid / disabled storage profiles
		if ($remoteFileSyncs)
		{
			$storageProfileIds = array_keys($remoteFileSyncs);
			$storageProfiles = StorageProfilePeer::retrieveExternalByPartnerId(
				$this->entry->getPartnerId(), 
				$storageProfileIds);

			$activeStorageProfileIds = array();
			foreach ($storageProfiles as $storageProfile)
			{
				if($this->shouldIncludeStorageProfile($storageProfile))
					$activeStorageProfileIds[] = $storageProfile->getId();
			}
			
			foreach ($storageProfileIds as $storageProfileId)
			{
				if (in_array($storageProfileId, $activeStorageProfileIds))
					continue;
				
				unset($remoteFlavorsByDc[$storageProfileId]);
				unset($remoteFileSyncs[$storageProfileId]);
			}
		}
		
		// choose the storage profile with the highest number of flavors
		$maxDc = null;
		$maxDcFlavorCount = 0;
		$remoteFlavors = array();
		foreach ($remoteFlavorsByDc as $dc => $curDcFlavors)
		{
			$curDcFlavorCount = count($curDcFlavors);
			if ($curDcFlavorCount <= $maxDcFlavorCount)
				continue;
			$maxDc = $dc;
			$maxDcFlavorCount = $curDcFlavorCount;
			$remoteFlavors = $curDcFlavors;
		}
				
		// choose the flavor set according to the serve priority
		if ($this->shouldUseLocalFlavors($localFlavors, $remoteFlavors))
		{
			$this->deliveryAttributes->setStorageId(null);
			$this->deliveryAttributes->setFlavorAssets($localFlavors);
		}
		else if ($maxDc)
		{
			$this->deliveryAttributes->setStorageId($maxDc);
			$this->deliveryAttributes->setFlavorAssets($remoteFlavors);
			$this->deliveryAttributes->setRemoteFileSyncs($remoteFileSyncs[$maxDc]);
		}
	
		if (!$this->deliveryAttributes->getFlavorAssets())
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
	
		if ($oneOnly) {
			$flavorAssets = $this->deliveryAttributes->getFlavorAssets();
			$this->deliveryAttributes->setFlavorAssets(array(reset($flavorAssets)));
		}
	}

	private function shouldIncludeStorageProfile($storageProfile)
	{
		if($this->requestedDeliveryProfileIds)
		{
			$deliveryIdsByStreamerType = $storageProfile->getDeliveryProfileIds();
			$storageStreamerTypeDeliveryProfileIds = $deliveryIdsByStreamerType[$this->deliveryAttributes->getFormat()];
			return isset($storageStreamerTypeDeliveryProfileIds) && count(array_intersect($this->requestedDeliveryProfileIds, $storageStreamerTypeDeliveryProfileIds));
		}

		return true;
	}

	/**
	 * @return int
	 */
	private function initEntryDuration()
	{
		$this->duration = $this->entry->getDurationInt();
		$flavors = $this->deliveryAttributes->getFlavorAssets();
		foreach($flavors as $flavorAsset)
		{
			/* @var $flavorAsset flavorAsset */
			$mediaInfo = $flavorAsset->getMediaInfo();
			if($mediaInfo && ($mediaInfo->getVideoDuration() || $mediaInfo->getAudioDuration() || $mediaInfo->getContainerDuration()))
			{
				$duration = ($mediaInfo->getVideoDuration() ? $mediaInfo->getVideoDuration() : 
								($mediaInfo->getAudioDuration() ? $mediaInfo->getAudioDuration() : 
									$mediaInfo->getContainerDuration()));
				$this->duration = $duration / 1000;
				break;
			}
		}
	}

	public function initStorageProfile()
	{
		if(!$this->deliveryAttributes->getStorageId())
			return;
			
		$storageProfile = StorageProfilePeer::retrieveByPK($this->deliveryAttributes->getStorageId());
		if(!$storageProfile)
			KExternalErrors::dieGracefully();			// TODO use a dieError
				
		// storage doesn't belong to the partner
		if($storageProfile->getPartnerId() != $this->entry->getPartnerId())
			KExternalErrors::dieGracefully();			// TODO use a dieError
	}
	
	protected function initDeliveryProfile()
	{
		if ($this->deliveryAttributes->getStorageId())
		{
			return DeliveryProfilePeer::getRemoteDeliveryByStorageId($this->deliveryAttributes);
		} else {		
			$cdnHost = $this->cdnHost;
			$cdnHostOnly = trim(preg_replace('#https?://#', '', $cdnHost), '/');
			
			return DeliveryProfilePeer::getLocalDeliveryByPartner($this->entryId, $this->deliveryAttributes->getFormat(), 
					$this->deliveryAttributes, $cdnHostOnly);
		}
	}

	protected function getDownloadFileName()
	{
		$flavorAssets = $this->deliveryAttributes->getFlavorAssets();
		$flavorAsset = reset($flavorAssets);
		list($fileName, $extension) = kAssetUtils::getFileName($this->entry, $flavorAsset);

		$fileName = str_replace("\n", ' ', $fileName);
		$fileName = kString::keepOnlyValidUrlChars($fileName);

		if ($extension)
			$fileName .= ".$extension";

		return $fileName;
	}

///////////////////////////////////////////////////////////////////////////////////
	//	Main functions

	private function serveVodEntry()
	{
		$this->initFlavorIds();
		
		if($this->entry->getPartner()->getForceCdnHost())
			$this->cdnHost = myPartnerUtils::getCdnHost($this->entry->getPartnerId(), $this->protocol);
		
		switch($this->entry->getType())
		{
		case entryType::PLAYLIST:
			$this->initPlaylistFlavorAssetArray();
			break;
		
		case entryType::MEDIA_CLIP:
			if ($this->deliveryAttributes->getSequence())
			{
				$sequenceArr = explode(',',$this->deliveryAttributes->getSequence());
				$sequenceEntries = entryPeer::retrieveByPKs($sequenceArr);
				if (count($sequenceEntries))
				{
					$this->deliveryAttributes->setHasValidSequence(true);
					list($entryIds, $durations, $mediaEntry, $captionFiles) = myPlaylistUtils::getPlaylistDataFromEntries($sequenceEntries, null, null);
					$this->setPlaylistFlavorAssets($durations, $this->entry->getId());
				}
			}
			if (!$this->deliveryAttributes->getHasValidSequence())
			{
				$this->initFlavorAssetArray();
				$this->initEntryDuration();
			}
			break;
		}
		
		if ($this->duration && $this->duration < 10 && $this->deliveryAttributes->getFormat() == PlaybackProtocol::AKAMAI_HDS)
		{
			// videos shorter than 10 seconds cannot be played with HDS, fall back to HTTP
			$this->deliveryAttributes->setFormat(PlaybackProtocol::HTTP);
			$flavorAssets = $this->deliveryAttributes->getFlavorAssets();
			$flavorAsset = reset($flavorAssets);
			$this->deliveryAttributes->setFlavorAssets(array($flavorAsset));
		}
		
		$this->initStorageProfile();
		
		// Fixing ALL kinds of historical bugs.
		
		switch ($this->deliveryAttributes->getFormat())
		{
		case self::URL:
			if(is_null($this->deliveryAttributes->getResponseFormat()))
				$this->deliveryAttributes->setResponseFormat('redirect');
			$this->deliveryAttributes->setFormat(PlaybackProtocol::HTTP);
			break;

		case self::DOWNLOAD:
			if(is_null($this->deliveryAttributes->getResponseFormat()))
				$this->deliveryAttributes->setResponseFormat('redirect');
			$this->deliveryAttributes->setFormat(PlaybackProtocol::HTTP);
			$this->deliveryAttributes->setUrlParams('/fileName/' . $this->getDownloadFileName());
			break;

		case PlaybackProtocol::AKAMAI_HD:
			// This is a hack to return an f4m that has a URL of a smil
			return $this->serveHDNetwork();

		case self::HDNETWORKSMIL:
			// Translate to playback protocol format 	
			$this->deliveryAttributes->setFormat(PlaybackProtocol::AKAMAI_HD);
			break;

		case PlaybackProtocol::RTMP:
			if(strpos($this->deliveryAttributes->getMediaProtocol(), "rtmp") !== 0) 
				$this->deliveryAttributes->setMediaProtocol("rtmp");
			break;

		case PlaybackProtocol::HTTP:
			if(strpos($this->deliveryAttributes->getMediaProtocol(), "http") !== 0)
				$this->deliveryAttributes->setMediaProtocol("http");
			break;
		}

		// <-- 
		
		$this->deliveryProfile = $this->initDeliveryProfile();
		if(!$this->deliveryProfile)
			return null;


		$this->setParamsForPlayServer($this->deliveryProfile->getAdStitchingEnabled());

		$filter = $this->deliveryProfile->getSupplementaryAssetsFilter();
		if ($filter && !$this->deliveryAttributes->getHasValidSequence())
		{
			$c = new Criteria();
			$filter->attachToCriteria($c);
			$c->add(assetPeer::ENTRY_ID, $this->entryId);
			$c->add(assetPeer::STATUS, asset::ASSET_STATUS_READY);
			$assets = assetPeer::doSelect($c);

			$assets = array_merge(
					$this->deliveryAttributes->getFlavorAssets(), 
					$assets);
			$this->deliveryAttributes->setFlavorAssets($assets);
		}

		$this->enforceAudioVideoEntry();
		
		$this->deliveryProfile->setDynamicAttributes($this->deliveryAttributes);	
		return $this->deliveryProfile->serve();
	}
	
	private function serveHDNetwork()
	{
		kApiCache::setConditionalCacheExpiry(600);		// the result contains a KS so we shouldn't cache it for a long time

        	if ($this->deliveryAttributes->getMediaProtocol() == 'https' && kConf::hasParam('cdn_api_host_https'))
        	{
            		$mediaUrl = "https://" . kConf::get('cdn_api_host_https');
        	}
        	else
        	{
            		$mediaUrl = "http://" . kConf::get('cdn_api_host');
        	}
        	$mediaUrl .= str_replace("f4m", "smil", str_replace("hdnetwork", "hdnetworksmil", $_SERVER["REQUEST_URI"]));

		$renderer = new kF4MManifestRenderer(array(), $this->entryId);
		$renderer->mediaUrl = $mediaUrl;
		return $renderer;
	}

	private function updateDeliveryAttributes()
	{
		$tag = null;
		$tags = $this->deliveryAttributes->getTags();
		if(count($tags) == 1) 
			$tag = reset($tags);
			
		$protocol = $this->deliveryAttributes->getMediaProtocol(); 
		if(in_array($this->deliveryAttributes->getFormat(), self::$httpFormats) && !in_array($protocol, self::$httpProtocols))
			$protocol = requestUtils::getProtocol();
		
		// use only cloud transcode flavors if timeAlignedRenditions was set
		$partnerId = $this->entry->getPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$partnerTimeAligned = $partner->getTimeAlignedRenditions();
		
		if ( ($partnerTimeAligned) && ( $this->getRequestParameter("playerType") === 'flash' ) ) {
			// check entry's flavors
			$entryFlavorParams = assetParamsPeer::retrieveByPKs(explode(',', $this->entry->getFlavorParamsIds()));
			$hasTranscode = false;
			foreach ($entryFlavorParams as $flavor)
			{
				// check if we have any transcode flavor
				if (!$flavor->hasTag("ingest")) {
					$hasTranscode = true;
				}
			}
    		 
			// if so, use only the transcode
			if ($hasTranscode) {
				$tag = 'mbr';
			}
		}

		$this->deliveryAttributes->setTags($tag);
		$this->deliveryAttributes->setMediaProtocol($protocol);
	}
	
	private function serveLiveEntry()
	{
		$this->initFlavorParamsIds();

		if (in_array($this->entry->getSource(), LiveEntry::$kalturaLiveSourceTypes) && !$this->deliveryAttributes->getServeVodFromLive())
 		{
 			if (!$this->entry->isCurrentlyLive())
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_LIVE, "Entry [$this->entryId] is not broadcasting");
 			
 			kApiCache::setExpiry(120);
 		}

		if($this->deliveryAttributes->getFormat() == PlaybackProtocol::MULTICAST_SL)
			$this->deliveryAttributes->setFormat(PlaybackProtocol::HDS);

		$this->deliveryProfile = $this->initDeliveryProfile();
		if(!$this->deliveryProfile)
			return null;

		$this->updateDeliveryAttributes();
		$this->deliveryProfile->setDynamicAttributes($this->deliveryAttributes);
		return $this->deliveryProfile->serve();
	}
	
	/* (non-PHPdoc)
	 * @see /symfony/action/sfComponent#getRequestParameter()
	 */
	public function getRequestParameter($name, $default = null)
	{
		$requestParams = infraRequestUtils::getRequestParams();
		if (array_key_exists($name, $requestParams))
		{
			return $requestParams[$name];
		}

		if (isset(self::$shortNames[$name]))
		{
			$shortName = self::$shortNames[$name];
			if (array_key_exists($shortName, $requestParams))
			{
				return $requestParams[$shortName];
			}
		}

		return $default;
	}
  
	static protected function getDefaultTagsByFormat($format)
	{
		switch ($format)
		{
		case PlaybackProtocol::SILVER_LIGHT:
			return array(
				array(assetParams::TAG_ISM),
			);

		case PlaybackProtocol::MPEG_DASH:
			return array(
				array('dash'),
				array('ipadnew', 'iphonenew'),
				array('ipad', 'iphone'),
			);
			
		case PlaybackProtocol::APPLE_HTTP:
		case PlaybackProtocol::HDS:
			return array(
				array(assetParams::TAG_APPLEMBR),
				array('ipadnew', 'iphonenew'),
				array('ipad', 'iphone'),
			);
			
		default:
			return array(
				array(assetParams::TAG_MBR),
				array(assetParams::TAG_WEB),
			);
		}
	}

	private function setParamsForPlayServer($usePlayServer)
	{
		$usePlayServer = $this->deliveryAttributes->getUsePlayServer() || (bool)$usePlayServer;
		$this->deliveryAttributes->setUsePlayServer((bool) $usePlayServer && PermissionPeer::isValidForPartner(PermissionName::FEATURE_PLAY_SERVER, $this->entry->getPartnerId()));
		if($this->deliveryAttributes->getUsePlayServer())
		{
			$this->deliveryAttributes->setPlayerConfig($this->getRequestParameter("playerConfig"));
			//In case request needs to be redirected to play-server we need to add the ui conf id to the manifest url as well
			$this->deliveryAttributes->setUiConfId($this->getRequestParameter("uiConfId"));
			if(!$this->deliveryAttributes->getUiConfId())
				$this->deliveryAttributes->setUiConfId($this->getRequestParameter("uiconf"));
			if($this->getRequestParameter("sessionId"))
				$this->deliveryAttributes->setSessionId($this->getRequestParameter("sessionId"));
		}
	}

	public function execute()
	{
		if($this->getRequestParameter("format", "Empty") !== PlaybackProtocol::APPLE_HTTP_TO_MC)
			KExternalErrors::setResponseErrorCode(KExternalErrors::HTTP_STATUS_NOT_FOUND);
		
		$this->deliveryAttributes = new DeliveryProfileDynamicAttributes();
		// Parse input parameters
		$this->deliveryAttributes->setSeekFromTime($this->getRequestParameter ( "seekFrom" , -1));
		if ($this->deliveryAttributes->getSeekFromTime() <= 0)
			$this->deliveryAttributes->setSeekFromTime(-1);
		$this->deliveryAttributes->setClipTo($this->getRequestParameter ( "clipTo" , 0));

		$this->deliveryAttributes->setPlaybackRate($this->getRequestParameter ( "playbackRate" , 0));
		
		$deliveryCode = $this->getRequestParameter( "deliveryCode", null );
		$playbackContext = $this->getRequestParameter( "playbackContext", null );
		$this->deliveryAttributes->setMediaProtocol($this->getRequestParameter ( "protocol", null ));
		if(!$this->deliveryAttributes->getMediaProtocol() || $this->deliveryAttributes->getMediaProtocol() === "null")
			$this->deliveryAttributes->setMediaProtocol(PlaybackProtocol::HTTP);
		
		$this->deliveryAttributes->setFormat($this->getRequestParameter ( "format" ));
		if(!$this->deliveryAttributes->getFormat())
			$this->deliveryAttributes->setFormat(PlaybackProtocol::HTTP);

		if ($this->deliveryAttributes->getFormat() == PlaybackProtocol::AKAMAI_HDS || $this->deliveryAttributes->getFormat() == self::HDNETWORKSMIL)  
			if(strpos($this->deliveryAttributes->getMediaProtocol(), "http") !== 0)
			    $this->deliveryAttributes->setMediaProtocol(PlaybackProtocol::HTTP);

		$tags = $this->getRequestParameter ( "tags", null );
		if (!$tags)
		{
			$this->deliveryAttributes->setTags(self::getDefaultTagsByFormat($this->deliveryAttributes->getFormat()));
		}
		else
		{
			$tagsArray = explode(',', $tags);
			$tags = array();
			foreach ($tagsArray as $tag)
			{
				$tags[] = array(trim($tag));
			}
			
			$this->deliveryAttributes->setTags($tags);
		}

		$this->deliveryAttributes->setpreferredBitrate($this->getRequestParameter ( "preferredBitrate", null ));
		$this->maxBitrate = $this->getRequestParameter ( "maxBitrate", null );
		if(($this->maxBitrate) && ((!is_numeric($this->maxBitrate)) || ($this->maxBitrate <= 0)))
			KExternalErrors::dieError(KExternalErrors::INVALID_MAX_BITRATE);

		$this->minBitrate = $this->getRequestParameter ( "minBitrate", null );
		if(($this->minBitrate) && ((!is_numeric($this->minBitrate)) || ($this->minBitrate <= 0)))
			KExternalErrors::dieError(KExternalErrors::INVALID_MIN_BITRATE);

		$this->deliveryAttributes->setStorageId($this->getRequestParameter ( "storageId", null ));
		$this->cdnHost = $this->getRequestParameter ( "cdnHost", null );

		$this->deliveryAttributes->setResponseFormat($this->getRequestParameter ( "responseFormat", null ));

		$requestDeliveryProfileIds = $this->getRequestParameter( "deliveryProfileIds", null);
		if($requestDeliveryProfileIds)
		{
			$this->requestedDeliveryProfileIds = explode(',', $requestDeliveryProfileIds);
		}
		else
		{
			$requestDeliveryProfileIds = $this->getRequestParameter( "deliveryProfileId", null);
			if($requestDeliveryProfileIds)
			{
				$this->requestedDeliveryProfileIds = array($requestDeliveryProfileIds);
			}
		}

		$this->deliveryAttributes->setRequestedDeliveryProfileIds($this->requestedDeliveryProfileIds);

		// Initialize
		$this->initEntry();
		$this->deliveryAttributes->setEntryId($this->entryId);

		$this->setParamsForPlayServer($this->getRequestParameter("usePlayServer"));
		$this->deliveryAttributes->setDefaultAudioLanguage($this->getRequestParameter("defaultAudioLang"));

		if ( in_array($this->deliveryAttributes->getFormat(), array(PlaybackProtocol::APPLE_HTTP, PlaybackProtocol::MPEG_DASH, PlaybackProtocol::AKAMAI_HDS)) )
			$this->deliveryAttributes->setSequence($this->getRequestParameter("sequence"));

		if($this->secureEntryHelper)
			$this->secureEntryHelper->updateDeliveryAttributes($this->deliveryAttributes);

		$this->enforceEncryption();
		
		$renderer = null;

		switch($this->entry->getType())
		{
			case entryType::PLAYLIST:
			case entryType::MEDIA_CLIP:
				// VOD
				$renderer = $this->serveVodEntry();
				$entryType = 'vod';
				break;
				
			case entryType::LIVE_STREAM:			
			case entryType::LIVE_CHANNEL:
				// Live stream
				$renderer = $this->serveLiveEntry();
				$entryType = 'live';
				break;
			
			default:
				KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
		}
				
		if (!$renderer)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'This format is unsupported');
		
		$renderer->contributors = array();
		$config = new kManifestContributorConfig();
		$config->format = $this->deliveryAttributes->getFormat();
		$config->storageId = $this->deliveryAttributes->getStorageId();
		$config->entryId = $this->entryId;
		$config->rendererClass = get_class($renderer);
		$config->deliveryProfile = $this->deliveryProfile;
		$config->hasSequence = $this->deliveryAttributes->getHasValidSequence();
		$config->disableCaptions = $this->getRequestParameter("disableCaptions", false);

		$contributors = KalturaPluginManager::getPluginInstances('IKalturaPlayManifestContributor');
		foreach ($contributors as $contributor)
		{
			/* @var $contributor IKalturaPlayManifestContributor */
			$renderer->contributors = array_merge($renderer->contributors, $contributor->getManifestEditors($config));
		}

		$renderer->entryId = $this->entryId;
		$renderer->partnerId = $this->entry->getPartnerId();
		$renderer->entryType = $entryType;
		$renderer->duration = $this->duration;
		if ($this->deliveryProfile)
		{
			$renderer->tokenizer = $this->deliveryProfile->getTokenizer();
			if ($renderer->tokenizer)
			{
				$renderer->tokenizer->setEntryId($this->entryId);
				$renderer->tokenizer->setPartnerId($this->entry->getPartnerId());
			}
		}
		$renderer->defaultDeliveryCode = $this->entry->getPartner()->getDefaultDeliveryCode();
		$renderer->lastModified = time();

		// Handle caching
		$canCacheAccessControl = false;
		if (kConf::hasParam("force_caching_headers") && in_array($this->entry->getPartnerId(), kConf::get("force_caching_headers")))
		{
			$renderer->cachingHeadersAge = kConf::get('play_manifest_cache_age', 'local', 60);
			$renderer->forceCachingHeaders = true;
		}
		if (!$this->secureEntryHelper)
		{
			$canCacheAccessControl = true;			// TODO: reconsider this if/when expired ktokens will be used
		}
		else if (!$this->secureEntryHelper->shouldDisableCache() && !$this->secureEntryHelper->isKsAdmin() &&
			($this->secureEntryHelper->isKsWidget() || !$this->secureEntryHelper->hasRules()))
		{
			$canCacheAccessControl = true;
		}
		
		if (!$renderer->tokenizer && $canCacheAccessControl)
		{
			// Note: kApiCache::hasExtraFields is checked in kManifestRenderers
			$renderer->cachingHeadersAge = kConf::get('play_manifest_cache_age', 'local', 60);
		}
		if ($this->deliveryProfile && $this->deliveryProfile->getAdStitchingEnabled())
			$renderer->cachingHeadersAge = 0;

		
		if (!$this->secureEntryHelper || !$this->secureEntryHelper->shouldDisableCache())
		{
			$cache = kPlayManifestCacher::getInstance();
			$cache->storeRendererToCache($renderer);
		}

		// Output the response
		KExternalErrors::terminateDispatch();

		$renderer->setKsObject(kCurrentContext::$ks_object);
		$renderer->setPlaybackContext($playbackContext);
		$renderer->setDeliveryCode($deliveryCode);
		
		$renderer->output();
	}

	/**
	 * @param $durations
	 * @param $mediaEntry
	 */
	protected function setPlaylistFlavorAssets($durations, $mediaEntryId)
	{
		$this->duration = array_sum($durations) / 1000;

		$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($mediaEntryId);
		$flavorAssets = $this->removeNotAllowedFlavors($flavorAssets);
		$flavorAssets = $this->removeFlavorsByBitrate($flavorAssets);
		$filteredFlavorAssets = $this->filterFlavorsByAssetIdOrParamsIds($flavorAssets);

		if (!$filteredFlavorAssets || !count($filteredFlavorAssets))
		{
			$filteredFlavorAssets = $this->deliveryAttributes->filterFlavorsByTags($flavorAssets);
			if (count($filteredFlavorAssets) && self::shouldAddAltAudioFlavors($this->deliveryAttributes->getFormat()))
				$this->addAltAudioFlavors($filteredFlavorAssets, $flavorAssets);
		}

		$this->deliveryAttributes->setStorageId(null);
		$this->deliveryAttributes->setFlavorAssets($filteredFlavorAssets);
	}
}

<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class playManifestAction extends kalturaAction
{
	const URL = 'url';
	
	const HDNETWORKSMIL = 'hdnetworksmil';
	
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
	private $format;
	
	/**
	 * may contain several fallbacks options, each one with a set of tags 
	 * @var array
	 */
	private $tags;
	
	/**
	 * @var string
	 */
	private $entryId;
	
	/**
	 * @var entry
	 */
	private $entry;
		
	/**
	 * @var int
	 */
	private $clipTo = 0;
	
	/**
	 * @var int
	 */
	private $seekFrom = 0;
	
	/**
	 * @var int
	 */
	private $storageId = null;
	
	/**
	 * @var string
	 */
	private $cdnHost = null;
	
	/**
	 * @var string
	 */
	private $protocol = null;
	
	/**
	 * @var int
	 */
	private $maxBitrate = null;
	
	/**
	 * @var int
	 */
	private $preferredBitrate = null;
	
	/**
	 * @var array
	 */
	private $preferredFlavor = null;
	
	/**
	 * @var array
	 */
	private $flavorIds = null;
	
	/**
	 * @var kUrlManager
	 */
	private $urlManager = null;
	
	/**
	 * @var KSecureEntryHelper
	 */
	private $secureEntryHelper = null;
	
	/**
	 * @var array
	 */
	private $flavorAssets = array();
	
	/**
	 * @var array
	 */
	private $remoteFileSyncs = array();
	
	/**
	 * @var FileSync
	 */
	private $manifestFileSync = null;
		
	/**
	 * @var int
	 */
	private $duration = null;
	
	/**
	 * @var StorageProfile
	 */
	private $storageProfile = null;
	
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
	
	/**
	 * @param string $url
	 * @return string
	 */
	static protected function calculateKalturaToken($url)
	{
		$token = sha1(kConf::get('url_token_secret') . $url); 
		return str_replace(self::KALTURA_TOKEN_MARKER, $token, $url);
	}
	
	/**
	 * @param array $params
	 * @return array
	 */
	private function convertToShortNames(array $params)
	{
		$result = array();
		foreach ($params as $key => $value)
		{
			if (isset(self::$shortNames[$key]))
				$shortName = self::$shortNames[$key];
			else
				$shortName = $key;
			
			$result[$shortName] = $value;
		}
		
		return $result;
	}
	
	/**
	 * @param string $format
	 * @param string $fileName
	 * @return string
	 */
	private function getTokenizedManifestUrl($format, $fileName)
	{
		$params = requestUtils::getRequestParams();
		$params['format'] = $format;
		
		$excludeList = array('kt', 'ks', 'referrer', 'extwidget', 'a');		
		foreach ($excludeList as $excludedParam)
		{
			unset($params[$excludedParam]);
		}
		if ($this->clipTo)
			$params['clipTo'] = $this->clipTo;		// in order to enforce preview access control
			
		$params = $this->convertToShortNames($params);
		
		$partnerId = $this->entry->getPartnerId();
				
		$url = "/p/{$partnerId}/playManifest/kt/".self::KALTURA_TOKEN_MARKER;
		foreach ($params as $key => $value)
			$url .= "/$key/$value";
		$url .= "/{$fileName}";

		return self::calculateKalturaToken($url);
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
			if (!$this->entry || $this->entry->getStatus() == entryStatus::DELETED)
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		}
		
		// no need for any further check if a token was used
		if ($urlToken)
		{
			return;
		}
		
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
		$referrer = base64_decode(str_replace(" ", "+", $base64Referrer));
		if (!is_string($referrer))
			$referrer = ""; // base64_decode can return binary data
			
		$this->secureEntryHelper = new KSecureEntryHelper($this->entry, $ksStr, $referrer, ContextType::PLAY);
		$this->secureEntryHelper->setHashes($keyValueHashes);
		
		if ($this->secureEntryHelper->shouldPreview())
		{
			$this->clipTo = $this->secureEntryHelper->getPreviewLength() * 1000;
		}
		else
		{
			$this->secureEntryHelper->validateForPlay();
		}
		
		if (PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT, $this->entry->getPartnerId()) || 
			$this->secureEntryHelper->hasRules())
			$this->forceUrlTokenization = true;
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
			$flavorParamIds = explode(',', $flavorParamIds);
		
		$flavorParamId = $this->getRequestParameter ( "flavorParamId", null );
		if (!is_null($flavorParamId))
			$flavorParamIds = array($flavorParamId);
			
		if (is_null($flavorParamIds))
			return;
			
		$flavorParamIds = $this->secureEntryHelper->filterAllowedFlavorParams($flavorParamIds);
		
		if(is_null($flavorParamIds))
			return;
			
		$this->flavorIds = assetPeer::retrieveReadyFlavorsIdsByEntryId($this->entryId, $flavorParamIds);
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
			if (strtolower($this->protocol) != 'https')
				KExternalErrors::dieError(KExternalErrors::ACCESS_CONTROL_RESTRICTED, 'unencrypted playback protocol - forbidden');
		}
	}
	
	private function enforceAudioVideoEntry()
	{
		if($this->entry->getType() != entryType::MEDIA_CLIP)
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);

		if(!in_array($this->entry->getMediaType(), array(
			entry::ENTRY_MEDIA_TYPE_VIDEO,
			entry::ENTRY_MEDIA_TYPE_AUDIO)))
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
	}
	
	protected function shouldUseLocalFlavors($hasLocalFlavors, $hasRemoteFlavors)
	{
		switch ($this->entry->getPartner()->getStorageServePriority())
		{
		case 0:
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
	
	protected function initSilverLightManifest()
	{
		$key = $this->entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
		$localFileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
		$remoteFileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key);
		if ($this->shouldUseLocalFlavors($localFileSync, $remoteFileSync))
		{
			$this->storageId = null;
			$this->manifestFileSync = $localFileSync;
		}
		else
		{
			$this->storageId = $remoteFileSync->getDc();
			$this->manifestFileSync = $remoteFileSync;
		}
		
		if (!$this->manifestFileSync)
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
	}
	
	/**
	 * @return array
	 */
	private function getReadyFlavorsByTags($flavorAssets)
	{
		foreach ($this->tags as $tagsFallback)
		{
			$curFlavors = array();
			
			foreach ($flavorAssets as $flavorAsset)
			{
				foreach ($tagsFallback as $tagOption)
				{
					if (!$flavorAsset->hasTag($tagOption))
						continue;
					$curFlavors[] = $flavorAsset;
					break;
				}
			}
			
			if ($curFlavors)
				return $curFlavors;
		}		
		return array();
	}
	
	private function removeNotAllowedFlavors($flavorAssets)
	{
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
	private function removeMaxBitrateFlavors($flavorAssets)
	{
		if (!$this->maxBitrate)			
			return $flavorAssets;
			
		$returnedFlavors = array();		
		foreach ($flavorAssets as $flavor)
		{
			if ($flavor->getBitrate() <= $this->maxBitrate)
			{
				$returnedFlavors[] = $flavor;
			}
		}
	
		return $returnedFlavors;
	}
	
	protected function initFlavorAssetArray()
	{
		// check whether the flavor asset list is needed
		if ($this->entry->getType() == entryType::LIVE_STREAM)
			return;			// live stream entries don't have flavors
		
		$oneOnly = false;
		switch($this->format)
		{
			case PlaybackProtocol::HTTP:
			case "url":
			case "rtsp":
				$oneOnly = true;	// single flavor delivery formats
				break;
				
			case PlaybackProtocol::SILVER_LIGHT:
				$this->initSilverLightManifest();
				// no break here
			case "hdnetwork":
				return;				// manifest-based delivery formats
		}
				
		// get initial flavor list by input
		$flavorAssets = array();
		if ($this->flavorIds)
		{
			$flavorAssets = assetPeer::retrieveReadyByEntryId($this->entryId, $this->flavorIds);
			$flavorAssets = $this->removeNotAllowedFlavors($flavorAssets);
			$flavorAssets = $this->removeMaxBitrateFlavors($flavorAssets);		
		}
		if (!$flavorAssets || !count($flavorAssets))
		{
			$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($this->entryId); 
			$flavorAssets = $this->getReadyFlavorsByTags($flavorAssets);
			$flavorAssets = $this->removeNotAllowedFlavors($flavorAssets);
			$flavorAssets = $this->removeMaxBitrateFlavors($flavorAssets);
		}		
					
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
			case 0:
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
				$c->addAnd ( FileSyncPeer::FILE_TYPE , FileSync::FILE_SYNC_FILE_TYPE_URL, Criteria::NOT_EQUAL);
				break;
				
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY:
				$c->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_URL);
				break;
			}
			
			if ($this->storageId)
				$c->addAnd ( FileSyncPeer::DC , $this->storageId );
			
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
			$this->storageId = null;
			$this->flavorAssets = $localFlavors;
		}
		else if ($maxDc)
		{
			$this->storageId = $maxDc;
			$this->flavorAssets = $remoteFlavors;
			$this->remoteFileSyncs = $remoteFileSyncs[$maxDc];
		}
	
		if (!$this->flavorAssets)
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
	
		if ($oneOnly)
			$this->flavorAssets = array(reset($this->flavorAssets));
	}

	/**
	 * @return int
	 */
	private function initEntryDuration()
	{
		$this->duration = $this->entry->getDurationInt();
		foreach($this->flavorAssets as $flavorAsset)
		{
			/* @var $flavorAsset flavorAsset */
			
			$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
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
		if(!$this->storageId)
			return;
			
		$this->storageProfile = StorageProfilePeer::retrieveByPK($this->storageId);
		if(!$this->storageProfile)
			KExternalErrors::dieGracefully();			// TODO use a dieError
				
		// storage doesn't belong to the partner
		if($this->storageProfile->getPartnerId() != $this->entry->getPartnerId())
			KExternalErrors::dieGracefully();			// TODO use a dieError
	}
	
	protected function initUrlManager()
	{
		if ($this->storageId)
		{
			$this->urlManager = kUrlManager::getUrlManagerByStorageProfile($this->storageId, $this->entryId);
			return;
		}
		
		$baseUrl = null;
		switch($this->format)
		{
			case PlaybackProtocol::RTMP:
				$baseUrl = myPartnerUtils::getRtmpUrl($this->entry->getPartnerId());
				break;
				
			case PlaybackProtocol::SILVER_LIGHT:
				$baseUrl = myPartnerUtils::getIisHost($this->entry->getPartnerId(), $this->protocol);
				break;				
		}
		
		$cdnHost = $this->cdnHost;
		if ($baseUrl)
			$cdnHost = parse_url($baseUrl, PHP_URL_HOST);

		$this->urlManager = kUrlManager::getUrlManagerByCdn($cdnHost, $this->entryId);
	}

	///////////////////////////////////////////////////////////////////////////////////
	//	Flavor array utility functions

		/**
	 * @param array $flavors
	 * @return string
	 */
	private function getMimeType($flavors)
	{
		if ($this->entry->getType() == entryType::MEDIA_CLIP &&
			count($flavors))
		{
			$isMp3 = true;
			foreach($flavors as $flavor)
			{
				if (!isset($flavor['ext']) || strtolower($flavor['ext']) != 'mp3')
					$isMp3 = false;
			}
			
			if ($isMp3)
				return 'audio/mpeg';
		}
		
		return 'video/x-flv';
	}
	
	/**
	 * 
	 * Private function which compares 2 flavors in order to sort an array.
	 * If a flavor's width and height parameters are equal to 0, it is 
	 * automatically moved down the list so the player will not start playing it by default.
	 * @param array $flavor1
	 * @param array $flavor2
	 */
	private function flavorCmpFunction ($flavor1, $flavor2)
	{
		// move the audio flavors to the end
		if ($flavor1['height'] == 0 && $flavor1['width'] == 0)
		{
			return 1;
		}
		if ($flavor2['height'] == 0 && $flavor2['width'] == 0)
		{
			return -1;
		}
		
		// if a preferred bitrate was defined place it first
		if ($this->preferredFlavor == $flavor2)
		{
			return 1;
		}
		if ($this->preferredFlavor == $flavor1)
		{
			return -1;
		}
		
		// sort the flavors in ascending bitrate order
		if ($flavor1['bitrate'] >= $flavor2['bitrate'])
		{
			return 1;
		}
		
		return -1;
	}
	
	/**
	 * @param array $flavors
	 * @return array
	 */
	private function sortFlavors($flavors)
	{
		$this->preferredFlavor = null;
		
		if ($this->preferredBitrate !== null)
		{
			foreach ($flavors as $flavor)
			{
				if ($flavor['height'] == 0 && $flavor['width'] == 0)
					continue;		// audio flavor
			
				$bitrateDiff = abs($flavor['bitrate'] - $this->preferredBitrate);
				if (!$this->preferredFlavor || $bitrateDiff < $minBitrateDiff)
				{
					$this->preferredFlavor = $flavor;
					$minBitrateDiff = $bitrateDiff;
				}
			}
		}
		
		uasort($flavors, array($this,'flavorCmpFunction'));
		
		return $flavors;
	}
	
	private function ensureUniqueBitrates(array &$flavors)
	{
		$seenBitrates = array();
		foreach ($flavors as &$flavor)
		{
			while (in_array($flavor['bitrate'], $seenBitrates))
			{
				$flavor['bitrate']++;
			}
			$seenBitrates[] = $flavor['bitrate'];
		}
	}
	
	///////////////////////////////////////////////////////////////////////////////////
	//	URL building functions
	
	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @param flavorAsset $flavorAsset
	 * @return array
	 */
	private function getFlavorAssetInfo($url, $urlPrefix = '', flavorAsset $flavorAsset = null)
	{
		$ext = null;
		if ($flavorAsset)
		{
			$ext = $flavorAsset->getFileExt();
		}
		if (!$ext)
		{
			$urlPath = parse_url($urlPrefix . $url, PHP_URL_PATH);
			$ext = pathinfo($urlPath, PATHINFO_EXTENSION);
		}

		$bitrate = ($flavorAsset ? $flavorAsset->getBitrate() : 0);
		$width =   ($flavorAsset ? $flavorAsset->getWidth()   : 0);
		$height =  ($flavorAsset ? $flavorAsset->getHeight()  : 0);
		
		return array(
			'url' => $url,
			'urlPrefix' => $urlPrefix,
			'ext' => $ext,
			'bitrate' => $bitrate,
			'width' => $width,
			'height' => $height);
	}

	/**
	 * @param FileSync $fileSync
	 * @param flavorAsset $flavorAsset
	 * @param string $format
	 */
	private function setupUrlManager(FileSync $fileSync = null, flavorAsset $flavorAsset = null, $format = null)
	{
		$this->urlManager->setClipTo($this->clipTo);
		if ($flavorAsset)
			$this->urlManager->setContainerFormat($flavorAsset->getContainerFormat());
		
		if($flavorAsset && $flavorAsset->getFileExt() !== null) // if the extension is missing use the one from the actual path
			$this->urlManager->setFileExtension($flavorAsset->getFileExt());
		else if ($fileSync)
			$this->urlManager->setFileExtension(pathinfo($fileSync->getFilePath(), PATHINFO_EXTENSION));
			
		if (!$format)
			$format = $this->format;
			
		$this->urlManager->setProtocol($format);
	}

	/**
	 * @param flavorAsset $flavorAsset
	 * @param FileSyncKey $key
	 * @return array
	 */
	private function getExternalStorageUrl(flavorAsset $flavorAsset)
	{
		$fileSync = $this->remoteFileSyncs[$flavorAsset->getId()];

		$this->setupUrlManager($fileSync, $flavorAsset);
		$this->urlManager->setSeekFromTime($this->seekFrom);
		
		$url = ltrim($this->urlManager->getFileSyncUrl($fileSync, false), "/");
		
		$urlPrefix = '';					
		if (strpos($url, "://") === false)
			$urlPrefix = rtrim($this->storageProfile->getDeliveryHttpBaseUrl(), "/") . "/";
		 			
		return $this->getFlavorAssetInfo($url, $urlPrefix, $flavorAsset);
	}

	/**
	 * @param flavorAsset $flavorAsset
	 * @return array
	 */
	private function getFlavorHttpUrl(flavorAsset $flavorAsset)
	{
		if ($this->storageId)		
			return $this->getExternalStorageUrl($flavorAsset);
			
		$this->setupUrlManager(null, $flavorAsset);
		$this->urlManager->setSeekFromTime($this->seekFrom);
		$this->urlManager->setDomain($this->cdnHost);

		$url = $this->urlManager->getAssetUrl($flavorAsset, false);
		
		if ($this->format == PlaybackProtocol::RTSP)
		{
			// the host was already added by the url manager
			return $this->getFlavorAssetInfo($url, '', $flavorAsset);
		}
		
		$urlPrefix = '';
		if (strpos($url, "/") === 0)
		{
			$flavorSizeKB = $flavorAsset->getSize();
			if ($flavorSizeKB > kConf::get("max_file_size_downloadable_from_cdn_in_KB"))
				$urlPrefix = requestUtils::getRequestHost();
			else
				$urlPrefix = $this->cdnHost;
		}

		$urlPrefix = preg_replace('/^https?:\/\//', '', $urlPrefix);
		$url = preg_replace('/^https?:\/\//', '', $url);
		
		if ($urlPrefix)
		{
			$urlPrefix = $this->protocol . '://' . $urlPrefix;
			$urlPrefix = rtrim($urlPrefix, "/") . "/";
		}
		else
		{
			$url = $this->protocol . '://' . $url;
		}
		
		$url = ltrim($url, "/");
		
		return $this->getFlavorAssetInfo($url, $urlPrefix, $flavorAsset);
	}
	
	/**
	 * @return array
	 */
	private function getSmoothStreamUrl()
	{
		$urlPrefix = myPartnerUtils::getIisHost($this->entry->getPartnerId(), $this->protocol);
		
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $urlPrefix, $matches))
		{
			$urlPrefix = $matches[1];
		}
		$urlPrefix .= '/';

		$this->setupUrlManager($this->manifestFileSync);
		$url = $this->urlManager->getFileSyncUrl($this->manifestFileSync, false);
		return $this->getFlavorAssetInfo($url, $urlPrefix);
	}
	
	/**
	 * @return array
	 */
	private function getSecureHdUrl()
	{
		if (!method_exists($this->urlManager, 'getManifestUrl'))
		{
			KalturaLog::debug('URL manager [' . get_class($this->urlManager) . '] does not support manifest URL');
			return null;
		}

		$originalFormat = $this->format;
		if ($this->format == PlaybackProtocol::APPLE_HTTP)
			$this->format = PlaybackProtocol::HTTP;	
		$flavors = $this->buildHttpFlavorsArray();
		$this->format = $originalFormat;

		if ($this->format == PlaybackProtocol::APPLE_HTTP)
			$flavors = $this->sortFlavors($flavors);	

		$this->setupUrlManager();
		
		$flavor = $this->urlManager->getManifestUrl($flavors);
		if (!$flavor)
		{
			KalturaLog::debug('URL manager [' . get_class($this->urlManager) . '] could not find flavor');
			return null;
		}
		
		if (strpos($flavor['urlPrefix'], '://') === false)
			$flavor['urlPrefix'] = $this->protocol . '://' . $flavor['urlPrefix'];

		return $flavor;
	} 
	
	///////////////////////////////////////////////////////////////////////////////////
	//	Flavor array building functions
		
	/**
	 * @param bool $oneOnly
	 * @return array
	 */
	private function buildHttpFlavorsArray()
	{
		$flavors = array();
		foreach($this->flavorAssets as $flavorAsset)
		{
			/* @var $flavorAsset flavorAsset */			
			$httpUrl = $this->getFlavorHttpUrl($flavorAsset);
			if ($httpUrl)		
				$flavors[] = $httpUrl;
		}
		return $flavors;
	}
		
	/**
	 * @param string $baseUrl
	 * @return array
	 */
	private function buildRtmpFlavorsArray(&$baseUrl)
	{
		$flavors = array();
		if($this->storageId)
		{
			$baseUrl = $this->storageProfile->getDeliveryRmpBaseUrl();

			// get all flavors with external urls
			foreach($this->flavorAssets as $flavorAsset)
			{
				$fileSync = $this->remoteFileSyncs[$flavorAsset->getId()];
				
				$this->setupUrlManager($fileSync, $flavorAsset);

				$url = $this->urlManager->getFileSyncUrl($fileSync, false);
				$url = ltrim($url, "/");
				
				$flavors[] = $this->getFlavorAssetInfo($url, '', $flavorAsset);
			}
		}
		else
		{
			$partnerId = $this->entry->getPartnerId();
			$baseUrl = myPartnerUtils::getRtmpUrl($partnerId);
			
			// get all flavors with kaltura urls
			foreach($this->flavorAssets as $flavorAsset)
			{
				/* @var $flavorAsset flavorAsset */
				
				$this->setupUrlManager(null, $flavorAsset);

				$url = $this->urlManager->getAssetUrl($flavorAsset, false);
				$url = ltrim($url, "/");
				
				$flavors[] = $this->getFlavorAssetInfo($url, '', $flavorAsset);
			}
		}
		
		if (strpos($this->protocol, "rtmp") === 0)
			$baseUrl = $this->protocol . '://' . preg_replace('/^rtmp.*?:\/\//', '', $baseUrl);
			
		$this->urlManager->finalizeUrls($baseUrl, $flavors);
		
		return $flavors;
	}
	
	/**
	 * @param string $baseUrl
	 * @return array
	 */
	private function buildRtmpLiveStreamFlavorsArray()
	{		
		$flavors = $this->entry->getStreamBitrates();
		if(count($flavors))
		{
			foreach($flavors as $index => $flavor)
			{
				$brIndex = $index + 1;
				$flavors[$index]['url'] = str_replace('%i', $brIndex, $this->entry->getStreamName());
			}
		}
		else
		{
			$flavors[0]['url'] = str_replace('%i', '1', $this->entry->getStreamName());
		}
		
		return $flavors;
	}

	///////////////////////////////////////////////////////////////////////////////////
	//	Serve functions
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveUrl()
	{
		$this->enforceAudioVideoEntry();
		
		$flavorInfo = $this->getFlavorHttpUrl(reset($this->flavorAssets));

		$renderer = new kRedirectManifestRenderer();
		$renderer->flavor = $flavorInfo;
		return $renderer;
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveHttp()
	{
		$this->enforceAudioVideoEntry();
		
		$flavors = $this->buildHttpFlavorsArray();
		
		$renderer = new kF4MManifestRenderer();
		$renderer->flavors = $flavors;
		$renderer->mimeType = $this->getMimeType($flavors);						
		return $renderer;
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveRtmp()
	{
		$baseUrl = null;
		
		$flavors = $this->buildRtmpFlavorsArray($baseUrl);		
		if(!count($flavors))
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);

		$renderer = new kF4MManifestRenderer();
		$renderer->flavors = $flavors;
		$renderer->baseUrl = $baseUrl;
		$renderer->mimeType = $this->getMimeType($flavors);
		return $renderer;
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveSilverLight()
	{
		$manifestInfo = $this->getSmoothStreamUrl();

		$renderer = new kSilverLightManifestRenderer();
		$renderer->flavor = $manifestInfo;
		return $renderer;
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveAppleHttp()
	{
		$flavor = $this->getSecureHdUrl();
		if ($flavor)
		{
			$renderer = new kRedirectManifestRenderer();
			$renderer->flavor = $flavor;
			return $renderer;
		}
		
		$flavors = $this->buildHttpFlavorsArray();
		
		$flavors = $this->sortFlavors($flavors);

		$renderer = new kM3U8ManifestRenderer();
		$renderer->flavors = $flavors;
		return $renderer;
	}

	/**
	 * @return kManifestRenderer
	 */
	private function serveHds()
	{
		$flavors = $this->buildHttpFlavorsArray();
		
		$flavors = $this->sortFlavors($flavors);

		$renderer = new kF4MManifestRenderer();
		$renderer->flavors = $flavors;
		return $renderer;		
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveHDNetworkSmil()
	{
		$flavors = $this->buildHttpFlavorsArray();
		
		// When playing HDS with Akamai HD the bitrates in the manifest must be unique
		$this->ensureUniqueBitrates($flavors); 

		$renderer = new kSmilManifestRenderer();
		$renderer->flavors = $flavors;
		return $renderer;
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveHDNetwork()
	{
		kApiCache::setConditionalCacheExpiry(600);		// the result contains a KS so we shouldn't cache it for a long time
		
		$mediaUrl = requestUtils::getHost().str_replace("f4m", "smil", str_replace("hdnetwork", "hdnetworksmil", $_SERVER["REQUEST_URI"])); 

		$renderer = new kF4MManifestRenderer();
		$renderer->mediaUrl = $mediaUrl;
		return $renderer;
	}
		
	/**
	 * @return kManifestRenderer
	 */
	private function serveHDNetworkManifest()
	{			
		$flavor = $this->getSecureHdUrl();
		if (!$flavor)
		{
			KalturaLog::debug('No flavor found');
			return null;
		}
		
		$renderer = new kF4MManifestRenderer();
		$renderer->flavors = array($flavor);
		return $renderer;
	}	

	/**
	 * @return kManifestRenderer
	 */
	private function serveRtsp()
	{
		$this->enforceAudioVideoEntry();
		
		$flavorInfo = $this->getFlavorHttpUrl(reset($this->flavorAssets));

		$renderer = new kRtspManifestRenderer();
		$renderer->flavor = $flavorInfo;
		return $renderer;
	}
	
	///////////////////////////////////////////////////////////////////////////////////
	//	Main functions

	private function serveVodEntry()
	{
		$this->initFlavorIds();
				
		if(!$this->cdnHost || $this->entry->getPartner()->getForceCdnHost())
			$this->cdnHost = myPartnerUtils::getCdnHost($this->entry->getPartnerId(), $this->protocol);

		$playbackCdnHost = $this->entry->getPartner()->getPlaybackCdnHost();
		if($playbackCdnHost)
			$this->cdnHost = preg_replace('/^https?/', $this->protocol, $playbackCdnHost);
				
		$this->initFlavorAssetArray();
		$this->initEntryDuration();
		
		if ($this->duration && $this->duration < 10 && $this->format == PlaybackProtocol::AKAMAI_HDS)
		{
			// videos shorter than 10 seconds cannot be played with HDS, fall back to HTTP
			$this->format = PlaybackProtocol::HTTP;
			$this->flavorAssets = array(reset($this->flavorAssets));
		}
		
		$this->initStorageProfile();
		$this->initUrlManager();
	
		switch($this->format)
		{
			case PlaybackProtocol::HTTP:
				return $this->serveHttp();
					
			case PlaybackProtocol::RTMP:
				return $this->serveRtmp();
					
			case PlaybackProtocol::SILVER_LIGHT:
				return $this->serveSilverLight();
					
			case PlaybackProtocol::APPLE_HTTP:
				return $this->serveAppleHttp();
		
			case PlaybackProtocol::HDS:
				return $this->serveHds();
					
			case self::URL:
				$this->format = "http"; // build url for an http delivery
				return $this->serveUrl();
					
			case PlaybackProtocol::RTSP:
				return $this->serveRtsp();
					
			case self::HDNETWORKSMIL:
				return $this->serveHDNetworkSmil();
					
			case PlaybackProtocol::AKAMAI_HD:
				return $this->serveHDNetwork();
		
			case PlaybackProtocol::AKAMAI_HDS:
				return $this->serveHDNetworkManifest();
		}
		
		return null;
	}
	
	private function getLiveEntryBaseUrl()
	{
		$liveStreamConfig = kLiveStreamConfiguration::getSingleItemByPropertyValue($this->entry, 'protocol', $this->format);
		if ($liveStreamConfig)
			return $liveStreamConfig->getUrl();
		
		switch($this->format)
		{
			case PlaybackProtocol::RTMP:
				$baseUrl = $this->entry->getStreamUrl();
				$baseUrl = rtrim($baseUrl, '/');
				if (strpos($this->protocol, "rtmp") === 0)
					$baseUrl = $this->protocol . '://' . preg_replace('/^rtmp.*?:\/\//', '', $baseUrl);
				return $baseUrl;
					
			case PlaybackProtocol::APPLE_HTTP:
				return $this->entry->getHlsStreamUrl();
		}
		return null;
	}
	
	private function serveLiveEntry()
	{		
		$baseUrl = $this->getLiveEntryBaseUrl();
		if (!$baseUrl)
			return null;
			
		$cdnHost = parse_url($baseUrl, PHP_URL_HOST);		
		$this->urlManager = kUrlManager::getUrlManagerByCdn($cdnHost, $this->entryId);
		$this->urlManager->setProtocol($this->format);
		
		$renderer = null;
		
		switch($this->format)
		{
			case PlaybackProtocol::RTMP:
				$flavors = $this->buildRtmpLiveStreamFlavorsArray();
				
				$this->urlManager->finalizeUrls($baseUrl, $flavors);
				
				$renderer = new kF4MManifestRenderer();
				$renderer->flavors = $flavors;
				$renderer->baseUrl = $baseUrl;
				$renderer->streamType = kF4MManifestRenderer::PLAY_STREAM_TYPE_LIVE;
				$renderer->mimeType = $this->getMimeType($flavors);
				break;
			
			case PlaybackProtocol::APPLE_HTTP:
				$flavor = $this->getFlavorAssetInfo('', $baseUrl);		// passing the url as urlPrefix so that only the path will be tokenized
				$renderer = new kRedirectManifestRenderer();
				$renderer->flavor = $flavor;
				break;
			
			case PlaybackProtocol::HDS:
			case PlaybackProtocol::AKAMAI_HDS:
				$flavor = $this->getFlavorAssetInfo('', $baseUrl);		// passing the url as urlPrefix so that only the path will be tokenized
				$renderer = new kF4MManifestRenderer();
				$renderer->flavors = array($flavor);
				break;
		}
				
		return $renderer;
	}
	
	/* (non-PHPdoc)
	 * @see /symfony/action/sfComponent#getRequestParameter()
	 */
	public function getRequestParameter($name, $default = null)
	{
		$val = parent::getRequestParameter($name, null);
		if (!is_null($val))
			return $val;

		if (isset(self::$shortNames[$name]))
		{
			$val = parent::getRequestParameter(self::$shortNames[$name], null);
			if (!is_null($val))
				return $val;
		}

		return $default;
	}
  
	static protected function getDefaultTagsByFormat($format)
	{
		switch ($format)
		{
		case PlaybackProtocol::SILVER_LIGHT:
			return array(
				array(assetParams::TAG_SLWEB),
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
	
	public function execute()
	{
		// Parse input parameters
		$this->seekFrom = $this->getRequestParameter ( "seekFrom" , -1);
		if ($this->seekFrom <= 0)
			$this->seekFrom = -1;

		$this->clipTo = $this->getRequestParameter ( "clipTo" , 0);
		
		$deliveryCode = $this->getRequestParameter( "deliveryCode", null );
		$playbackContext = $this->getRequestParameter( "playbackContext", null );
		$this->protocol = $this->getRequestParameter ( "protocol", null );
		if(!$this->protocol || $this->protocol === "null")
			$this->protocol = PlaybackProtocol::HTTP;
		
		$this->format = $this->getRequestParameter ( "format" );
		if(!$this->format)
			$this->format = PlaybackProtocol::HTTP;
			
		if ($this->format == self::HDNETWORKSMIL || $this->format == PlaybackProtocol::AKAMAI_HDS)
			$this->protocol = PlaybackProtocol::HTTP; // Akamai HD doesn't support any other protocol
			
		$this->tags = $this->getRequestParameter ( "tags", null );
		if (!$this->tags)
		{
			$this->tags = self::getDefaultTagsByFormat($this->format);
		}
		else
		{
			$tags = explode(',', $this->tags);
			$this->tags = array();
			foreach ($tags as $tag) 
			{
				$this->tags[] = array(trim($tag));
			}
		}
				
		$this->preferredBitrate = $this->getRequestParameter ( "preferredBitrate", null );
		$this->maxBitrate = $this->getRequestParameter ( "maxBitrate", null );
		if(($this->maxBitrate) && ((!is_numeric($this->maxBitrate)) || ($this->maxBitrate <= 0)))
			KExternalErrors::dieError(KExternalErrors::INVALID_MAX_BITRATE);

		$this->storageId = $this->getRequestParameter ( "storageId", null );
		$this->cdnHost = $this->getRequestParameter ( "cdnHost", null );
		
		// Initialize
		$this->initEntry();

		$this->enforceEncryption();
		
		$renderer = null;
		
		switch($this->entry->getType())
		{
		case entryType::MEDIA_CLIP:
			// VOD
			$renderer = $this->serveVodEntry();
			break;
			
		case entryType::LIVE_STREAM:
			// Live stream
			$renderer = $this->serveLiveEntry();
			break;
		
		default:
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
		}
				
		if (!$renderer)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'This format is unsupported');
		
		$renderer->contributors = array();
		$config = new kManifestContributorConfig();
		$config->format = $this->format;
		$config->deliveryCode = $deliveryCode;
		$config->storageId = $this->storageId;
		$config->entryId = $this->entryId;
		$contributors = KalturaPluginManager::getPluginInstances('IKalturaPlayManifestContributor');
		foreach ($contributors as $contributor)
		{
			/* @var $contributor IKalturaPlayManifestContributor */
			$renderer->contributors = array_merge($renderer->contributors, $contributor->getManifestEditors($config));
		}
			
		$renderer->entryId = $this->entryId;
		$renderer->duration = $this->duration;
		if ($this->urlManager)
			$renderer->tokenizer = $this->urlManager->getTokenizer();
		$renderer->defaultDeliveryCode = $this->entry->getPartner()->getDefaultDeliveryCode();
		
		// Handle caching
		$canCacheAccessControl = false;
		if (kConf::hasParam("force_caching_headers") && in_array($this->entry->getPartnerId(), kConf::get("force_caching_headers")))
		{
			$renderer->cachingHeadersAge = 60;
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
			$renderer->cachingHeadersAge = 60;
		}
		
		if (!$this->secureEntryHelper || !$this->secureEntryHelper->shouldDisableCache())
		{
			$cache = kPlayManifestCacher::getInstance();
			$cache->storeRendererToCache($renderer);
		}

		// Output the response
		KExternalErrors::terminateDispatch();
		$renderer->output($deliveryCode, $playbackContext);
	}
}

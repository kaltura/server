<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class playManifestAction extends kalturaAction
{
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
	 * @var string
	 */
	private $flavorId;
	
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
	 * @var string
	 */
	private $deliveryCode = null;	

	/**
	 * @var kUrlTokenizer
	 */
	private $tokenizer = null;
	
	/**
	 * @var KSecureEntryHelper
	 */
	private $secureEntryHelper = null;
	
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
			$parsedUrl = parse_url($urlPrefix . $url);
			$ext = pathinfo($parsedUrl['path'], PATHINFO_EXTENSION);
		}

		$bitrate = ($flavorAsset ? $flavorAsset->getBitrate() : 0);
		$width =   ($flavorAsset ? $flavorAsset->getWidth()	  : 0);
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
	 * @param kUrlManager $urlManager
	 * @param FileSync $fileSync
	 * @param flavorAsset $flavorAsset
	 * @param string $format
	 */
	private function setupUrlManager($urlManager, FileSync $fileSync = null, flavorAsset $flavorAsset = null, $format = null)
	{
		$urlManager->setClipTo($this->clipTo);
		if ($flavorAsset)
			$urlManager->setContainerFormat($flavorAsset->getContainerFormat());
		
		if($flavorAsset && $flavorAsset->getFileExt() !== null) // if the extension is missig use the one from the actual path
			$urlManager->setFileExtension($flavorAsset->getFileExt());
		else if ($fileSync)
			$urlManager->setFileExtension(pathinfo($fileSync->getFilePath(), PATHINFO_EXTENSION));
			
		if (!$format)
			$format = $this->format;
			
		$urlManager->setProtocol($format);
	}

	/**
	 * @param int $storageProfileId
	 * @param FileSync $fileSync
	 * @param flavorAsset $flavorAsset
	 * @param string $format
	 * @return kUrlManager
	 */
	private function getUrlManagerByStorageProfile($storageProfileId, FileSync $fileSync = null, flavorAsset $flavorAsset = null, $format = null)
	{
		$urlManager = kUrlManager::getUrlManagerByStorageProfile($storageProfileId, $this->entryId);
		$this->setupUrlManager($urlManager, $fileSync, $flavorAsset, $format);
		return $urlManager;
	}
	
	/**
	 * @param string $cdnHost
	 * @param FileSync $fileSync
	 * @param flavorAsset $flavorAsset
	 * @param string $format
	 * @return kUrlManager
	 */
	private function getUrlManagerByCdn($cdnHost, FileSync $fileSync = null, flavorAsset $flavorAsset = null, $format = null)
	{
		$urlManager = kUrlManager::getUrlManagerByCdn($cdnHost, $this->entryId);
		$this->setupUrlManager($urlManager, $fileSync, $flavorAsset, $format);
		return $urlManager;
	}	

	/**
	 * @param flavorAsset $flavorAsset
	 * @param FileSyncKey $key
	 * @return array
	 */
	private function getExternalStorageUrl(flavorAsset $flavorAsset, FileSyncKey $key)
	{
		$partner = $this->entry->getPartner();
		if(!$partner || 
			!$partner->getStorageServePriority() || 
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
			return null;
			
		if(is_null($this->storageId) && 
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
			if(kFileSyncUtils::getReadyInternalFileSyncForKey($key)) // check if having file sync on kaltura dcs
				return null;
				
		$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $this->storageId);
		if(!$fileSync)
			return null;

		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return null;
			
		$urlManager = $this->getUrlManagerByStorageProfile($fileSync->getDc(), $fileSync, $flavorAsset);
		$urlManager->setSeekFromTime($this->seekFrom);
		
		$url = ltrim($urlManager->getFileSyncUrl($fileSync, false), "/");		
        $urlPrefix = '';                    
        if (strpos($url, "://") === false)
         	$urlPrefix = rtrim($storage->getDeliveryHttpBaseUrl(), "/") . "/";
         			
		$this->tokenizer = $urlManager->getTokenizer();

		return $this->getFlavorAssetInfo($url, $urlPrefix, $flavorAsset);
	}

	/**
	 * @param flavorAsset $flavorAsset
	 * @return array
	 */
	private function getFlavorHttpUrl(flavorAsset $flavorAsset)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$externalStorageUrl = $this->getExternalStorageUrl($flavorAsset, $syncKey);
		if($externalStorageUrl)
			return $externalStorageUrl;
			
		if($this->storageId) // must be specific external storage
			return null;
			
		$partner = $this->entry->getPartner();
		if($partner && 
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
			return null;

		$urlManager = $this->getUrlManagerByCdn($this->cdnHost, null, $flavorAsset);
		$urlManager->setSeekFromTime($this->seekFrom);
		$urlManager->setDomain($this->cdnHost);

	    $url = $urlManager->getAssetUrl($flavorAsset, false);
	    
		$this->tokenizer = $urlManager->getTokenizer();
		
		if ($this->format == StorageProfile::PLAY_FORMAT_RTSP)
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
	 * @param FileSyncKey $key
	 * @return array
	 */
	private function getSmoothStreamUrl(FileSyncKey $key)
	{
		$kalturaFileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
	
		$urlPrefix = myPartnerUtils::getIisHost($this->entry->getPartnerId(), $this->protocol);
		$iisHost = parse_url($urlPrefix, PHP_URL_HOST);
		
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $urlPrefix, $matches))
		{
			$urlPrefix = $matches[1];
		}
		$urlPrefix .= '/';
		
		$kalturaUrlManager = $this->getUrlManagerByCdn($iisHost, $kalturaFileSync);
		
		$partner = $this->entry->getPartner();
		if(!$partner->getStorageServePriority() || 
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY ||
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
		{
			if($kalturaFileSync)
			{
				$this->tokenizer = $kalturaUrlManager->getTokenizer();
				$url = $kalturaUrlManager->getFileSyncUrl($kalturaFileSync, false);
				return $this->getFlavorAssetInfo($url, $urlPrefix);
			}
		}
		
		if(!$partner->getStorageServePriority() || 
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
		{
			return null;
		}
			
		$externalFileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key);		
		if($externalFileSync)
		{
			$externalUrlManager = $this->getUrlManagerByStorageProfile($externalFileSync->getDc(), $externalFileSync);
			$this->tokenizer = $externalUrlManager->getTokenizer();
			$url = $externalUrlManager->getFileSyncUrl($externalFileSync, false);
			return $this->getFlavorAssetInfo($url, $urlPrefix);
		}

		if($partner->getStorageServePriority() != StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
		{
			if($kalturaFileSync)
			{
				$this->tokenizer = $kalturaUrlManager->getTokenizer();
				$url = $kalturaUrlManager->getFileSyncUrl($kalturaFileSync, false);
				return $this->getFlavorAssetInfo($url, $urlPrefix);
			}
		}
					
		return null;
	}
	
	/**
	 * @return array
	 */
	private function getSecureHdUrl()
	{
		$urlManager = $this->getUrlManagerByCdn($this->cdnHost);
		if (!method_exists($urlManager, 'getManifestUrl'))
		{
			KalturaLog::debug('URL manager [' . get_class($urlManager) . '] does not support manifest URL for CDN [' . $this->cdnHost . ']');
			return null;
		}

		$originalFormat = $this->format;
		$this->format = StorageProfile::PLAY_FORMAT_HTTP;	
		$duration = null;		
		$flavors = $this->buildHttpFlavorsArray($duration);
		$this->format = $originalFormat;
		
		if (!$flavors)
			return null;

		if ($this->format == StorageProfile::PLAY_FORMAT_APPLE_HTTP)
			$flavors = $this->sortFlavors($flavors);	

		$flavor = $urlManager->getManifestUrl($flavors);
		if (!$flavor)
		{
			KalturaLog::debug('URL manager [' . get_class($urlManager) . '] could not find flavor');
			return null;
		}
		
		if (strpos($flavor['urlPrefix'], '://') === false)
			$flavor['urlPrefix'] = $this->protocol . '://' . $flavor['urlPrefix'];

		$urlManager->setProtocol('hdnetworkmanifest');
		$this->tokenizer = $urlManager->getTokenizer();
		
		return $flavor;
	} 
	
	///////////////////////////////////////////////////////////////////////////////////
	//	Flavor array utility functions

	/**
	 * @param array $flavorAssets
	 * @return int
	 */
	private function getDurationFromFlavorAssets($flavorAssets)
	{
		foreach($flavorAssets as $flavorAsset)
		{
			/* @var $flavorAsset flavorAsset */
			
			$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
			if($mediaInfo && ($mediaInfo->getVideoDuration() || $mediaInfo->getAudioDuration() || $mediaInfo->getContainerDuration()))
			{
				$duration = ($mediaInfo->getVideoDuration() ? $mediaInfo->getVideoDuration() : 
								($mediaInfo->getAudioDuration() ? $mediaInfo->getAudioDuration() : 
									$mediaInfo->getContainerDuration()));
				return $duration / 1000;
			}
		}
		return null;
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
	
	/**
	 * @param array $flavorAssets
	 * @return array
	 */
	private function getLocalFlavors($flavorAssets)
	{
		$localFlavors = array();
		foreach($flavorAssets as $flavorAsset)
		{
			$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
			if($fileSync)
				$localFlavors[] = $flavorAsset;
		}
		
		return $localFlavors;
	}

	/**
	 * @param array $flavorAssets
	 * @return array
	 */
	private function getRemoteFlavors($flavorAssets, $storageProfileId)
	{
		$remoteFlavors = array();
		foreach($flavorAssets as $flavorAsset)
		{
			$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $storageProfileId);
			if($fileSync)
				$remoteFlavors[] = $flavorAsset;
		}
		return $remoteFlavors;
	}
	
	/**
	 * @param array $flavors
	 * @return string
	 */
	private function getMimeType($flavors)
	{
		if ($this->entry->getType() == entryType::MEDIA_CLIP && 
			$this->entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_AUDIO &&
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

	/**
	 * @param asset $flavorAsset
	 * @return boolean
	 */
	private function assetMatchesTags(asset $flavorAsset)
	{
		foreach ($this->tags as $tagsFallback)
		{
			foreach ($tagsFallback as $tagOption)
			{
				if ($flavorAsset->hasTag($tagOption))
					return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @return array
	 */
	private function getReadyFlavorsByTags()
	{
		$allFlavors = assetPeer::retrieveReadyFlavorsByEntryId($this->entryId);
		foreach ($this->tags as $tagsFallback)
		{
			$curFlavors = array();
			
			foreach ($allFlavors as $flavorAsset)
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
	
	///////////////////////////////////////////////////////////////////////////////////
	//	Flavor array building functions

	/**
	 * @param bool $oneOnly
	 * @return array
	 */
	private function buildHttpFlavorAssetArray($oneOnly)
	{
		$flavorAssets = array();
		
		if($this->flavorId && ($flavorAsset = assetPeer::retrieveById($this->flavorId)) != null)
		{
			if ($this->assetMatchesTags($flavorAsset) &&
				$flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
				$flavorAssets[] = $flavorAsset;
		}
		elseif($oneOnly)
		{
			$webFlavorAssets = $this->getReadyFlavorsByTags();
			if(count($webFlavorAssets))
				$flavorAssets[] = reset($webFlavorAssets);
		}
		else 
		{
			if ($this->flavorIds)
			{
				$tmpFlavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($this->entryId);
				foreach($tmpFlavorAssets as $flavorAsset)
				{
					if (in_array($flavorAsset->getId(), $this->flavorIds))
						$flavorAssets[] = $flavorAsset;
				}
			}
			else 
			{
				$flavorAssets = $this->getReadyFlavorsByTags();
			}
		}
		
		return $flavorAssets;
	}
	
	/**
	 * @param bool $oneOnly
	 * @param int $duration
	 * @return array
	 */
	private function buildHttpFlavorsArray(&$duration, $oneOnly = false)
	{
		$flavorAssets = $this->buildHttpFlavorAssetArray($oneOnly);
		
		$duration = $this->entry->getDurationInt();
		$flavorDuration = $this->getDurationFromFlavorAssets($flavorAssets);
		if ($flavorDuration)
			$duration = $flavorDuration;
	
		$flavors = array();
		foreach($flavorAssets as $flavorAsset)
		{
			/* @var $flavorAsset flavorAsset */			
			$httpUrl = $this->getFlavorHttpUrl($flavorAsset);
			if ($httpUrl)		
				$flavors[] = $httpUrl;
		}
		return $flavors;
	}
	
	/**
	 * @return array
	 */
	private function buildRtmpFlavorAssetArray()
	{
		// get initial flavor list according to tags / specific flavor request
		$flavorAssets = array();
		
		if($this->flavorId)
		{
			$flavorAsset = assetPeer::retrieveById($this->flavorId);
			if(!$this->assetMatchesTags($flavorAsset))
				KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
				
			if($flavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
				KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
				
			$flavorAssets[] = $flavorAsset;
		}
		else 
		{
			$flavorAssets = $this->getReadyFlavorsByTags(); 
		}

		$flavorAssets = $this->removeMaxBitrateFlavors($flavorAssets);
		
		if ($this->storageId)
		{
			// no need to further check the flavors - caller will try this storageId and fall back to local flavors if needed
			return $flavorAssets;
		}
		
		$partner = $this->entry->getPartner();
		
		// try using local flavors if they have the higher priority
		if(!$partner->getStorageServePriority() || 
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST ||
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
		{
			$localFlavors = $this->getLocalFlavors($flavorAssets);
			
			if (count($localFlavors) ||
				!$partner->getStorageServePriority() || 
				$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
			{
				return $localFlavors;
			}
		}
		
		// try using remote flavors
		$storages = StorageProfilePeer::retrieveExternalByPartnerId($partner->getId());
		if(count($storages) == 1)
		{
			// no need to further check the flavors - caller will try this storageId and fall back to local flavors if needed
			$this->storageId = $storages[0]->getId();
			return $flavorAssets;
		}
		
		if(count($storages))
		{
			// use the storage profile with the highest number of flavors
			$storagesFlavors = array();
			foreach($storages as $storage)
			{
				$storagesFlavors[$storage->getId()] = $this->getRemoteFlavors($flavorAssets, $storage->getId());
			}
			
			$remoteFlavors = array();
			$maxCount = 0;
			foreach($storagesFlavors as $storageId => $storageFlavors)
			{
				$count = count($storageFlavors);
				if($count > $maxCount)
				{
					$this->storageId = $storageId;
					$remoteFlavors = $storageFlavors;
					$maxCount = $count;
				}
			}
			
			if (count($remoteFlavors))
			{
				return $remoteFlavors;
			}
		}
		
		if ($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
		{
			// could not find any external flavors and the serve priority is external only
			return array();
		}
		
		// could not find any external flavors, try local flavors
		return $this->getLocalFlavors($flavorAssets);
	}
	
	/**
	 * @param int $duration
	 * @param string $baseUrl
	 * @return array
	 */
	private function buildRtmpFlavorsArray(&$duration, &$baseUrl)
	{
		$flavorAssets = $this->buildRtmpFlavorAssetArray();
	
		$duration = $this->entry->getDurationInt();
		$flavorDuration = $this->getDurationFromFlavorAssets($flavorAssets);
		if ($flavorDuration)
			$duration = $flavorDuration;
			
		$flavors = array();
		if($this->storageId)
		{
			$storage = StorageProfilePeer::retrieveByPK($this->storageId);
			if(!$storage)
				die;
					
			$baseUrl = $storage->getDeliveryRmpBaseUrl();

			$urlManager = $this->getUrlManagerByStorageProfile($this->storageId);

			// get all flavors with external urls
			foreach($flavorAssets as $flavorAsset)
			{
				$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $this->storageId);
				if(!$fileSync)
					continue;
				
				$this->setupUrlManager($urlManager, $fileSync, $flavorAsset);

				$url = $urlManager->getFileSyncUrl($fileSync, false);
				$url = ltrim($url, "/");
				
				$flavors[] = $this->getFlavorAssetInfo($url, '', $flavorAsset);
			}
		}
		
		$partner = $this->entry->getPartner();
		if (!$this->storageId || 
			(!count($flavors) && $partner->getStorageServePriority() != StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)) 
		{
			$partnerId = $this->entry->getPartnerId();
			$subpId = $this->entry->getSubpId();
			$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
			$baseUrl = myPartnerUtils::getRtmpUrl($partnerId);
			
			// allow to replace {deliveryCode} place holder with the deliveryCode parameter passed to the action
			// a publisher with a rtmpUrl set to {deliveryCode}.example.com/ondemand will be able to use different
			// cdn configuration for different sub publishers by passing a different deliveryCode to the KDP

			if ($this->deliveryCode)
				$baseUrl = str_replace("{deliveryCode}", $this->deliveryCode, $baseUrl);
		
			$rtmpHost = parse_url($baseUrl, PHP_URL_HOST);

			$urlManager = $this->getUrlManagerByCdn($rtmpHost);

			// get all flavors with kaltura urls
			foreach($flavorAssets as $flavorAsset)
			{
				/* @var $flavorAsset flavorAsset */
				
				$this->setupUrlManager($urlManager, null, $flavorAsset);

				$url = $urlManager->getAssetUrl($flavorAsset, false);
				$url = ltrim($url, "/");
				
				$flavors[] = $this->getFlavorAssetInfo($url, '', $flavorAsset);
			}
		}
		
		if (strpos($this->protocol, "rtmp") === 0)
			$baseUrl = $this->protocol . '://' . preg_replace('/^rtmp.*?:\/\//', '', $baseUrl);
			
		$urlManager->finalizeUrls($baseUrl, $flavors);
		
		$this->tokenizer = $urlManager->getTokenizer();
		
		return $flavors;
	}
	
	/**
	 * @param string $baseUrl
	 * @return array
	 */
	private function buildRtmpLiveStreamFlavorsArray(&$baseUrl)
	{
		$streamId = $this->entry->getStreamRemoteId();
		$streamUsername = $this->entry->getStreamUsername();
		
		$baseUrl = $this->entry->getStreamUrl();
		$baseUrl = rtrim($baseUrl, '/');

		$rtmpHost = parse_url($baseUrl, PHP_URL_HOST);
		$urlManager = $this->getUrlManagerByCdn($rtmpHost);
		
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
		
		if (strpos($this->protocol, "rtmp") === 0)
			$baseUrl = $this->protocol . '://' . preg_replace('/^rtmp.*?:\/\//', '', $baseUrl);
		
		$urlManager->finalizeUrls($baseUrl, $flavors);

		$this->tokenizer = $urlManager->getTokenizer();

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
	
	/**
	 * @return flavorAsset
	 */
	private function getFlavorAsset()
	{
		if($this->entry->getType() != entryType::MEDIA_CLIP)
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);

		switch($this->entry->getType())
		{
			case entryType::MEDIA_CLIP:
				switch($this->entry->getMediaType())
				{					
					case entry::ENTRY_MEDIA_TYPE_VIDEO:
					case entry::ENTRY_MEDIA_TYPE_AUDIO:	
						if($this->flavorId && ($flavorAsset = assetPeer::retrieveById($this->flavorId)) != null)
						{
							return $flavorAsset;
						}
						KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
				}
				
			default:
				break;
		}
		
		KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
	}
	
	///////////////////////////////////////////////////////////////////////////////////
	//	Serve functions
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveUrl()
	{
		$flavorAsset = $this->getFlavorAsset();
		$flavorInfo = $this->getFlavorHttpUrl($flavorAsset);

		$renderer = new kRedirectManifestRenderer();
		$renderer->entryId = $this->entryId;
		$renderer->tokenizer = $this->tokenizer;
		$renderer->flavor = $flavorInfo;
		return $renderer;
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveHttp()
	{
		if($this->entry->getType() != entryType::MEDIA_CLIP)
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);

		$duration = null;
		switch($this->entry->getType())
		{
			case entryType::MEDIA_CLIP:
				switch($this->entry->getMediaType())
				{					
					case entry::ENTRY_MEDIA_TYPE_IMAGE:
						// TODO - create sequence manifest
						break;
						
					case entry::ENTRY_MEDIA_TYPE_VIDEO:
					case entry::ENTRY_MEDIA_TYPE_AUDIO:	
						$flavors = $this->buildHttpFlavorsArray($duration, true);
						
						$renderer = new kF4MManifestRenderer();
						$renderer->entryId = $this->entryId;
						$renderer->tokenizer = $this->tokenizer;						
						$renderer->flavors = $flavors;
						$renderer->duration = $duration;
						$renderer->mimeType = $this->getMimeType($flavors);						
						return $renderer;
				}
				
			default:
				break;
		}
		
		KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveRtmp()
	{
		$baseUrl = null;
		$duration = null;
		switch($this->entry->getType())
		{
			case entryType::MEDIA_CLIP:
				
				$flavors = $this->buildRtmpFlavorsArray($duration, $baseUrl);
				
				if(!count($flavors))
					KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);

				$renderer = new kF4MManifestRenderer();
				$renderer->entryId = $this->entryId;
				$renderer->tokenizer = $this->tokenizer;
				$renderer->flavors = $flavors;
				$renderer->baseUrl = $baseUrl;
				$renderer->duration = $duration;
				$renderer->mimeType = $this->getMimeType($flavors);
				return $renderer;

			case entryType::LIVE_STREAM:
				
				$flavors = $this->buildRtmpLiveStreamFlavorsArray($baseUrl);

				$renderer = new kF4MManifestRenderer();
				$renderer->entryId = $this->entryId;
				$renderer->tokenizer = $this->tokenizer;
				$renderer->flavors = $flavors;
				$renderer->baseUrl = $baseUrl;
				$renderer->streamType = kF4MManifestRenderer::PLAY_STREAM_TYPE_LIVE;
				$renderer->mimeType = $this->getMimeType($flavors);
				$renderer->deliveryCode = $this->deliveryCode;
				return $renderer;
				
		}
		
		KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
	}

	/**
	 * @return kManifestRenderer
	 */
	private function serveSilverLight()
	{
		$duration = $this->entry->getDurationInt();		
		$syncKey = $this->entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
		$manifestInfo = $this->getSmoothStreamUrl($syncKey);

		$renderer = new kSilverLightManifestRenderer();
		$renderer->entryId = $this->entryId;
		$renderer->tokenizer = $this->tokenizer;
		$renderer->flavor = $manifestInfo;
		$renderer->duration = $duration;
		return $renderer;
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveAppleHttp()
	{
	    $entry = entryPeer::retrieveByPK($this->entryId);
	    if ($entry->getType() == entryType::LIVE_STREAM)
	    {
	        $renderer = new kRedirectManifestRenderer();
			$renderer->entryId = $this->entryId;
			$renderer->flavor = array ("url" => $entry->getHlsStreamUrl());
			return $renderer;
	    }
	    
		$flavor = $this->getSecureHdUrl();
		if ($flavor)
		{
			$renderer = new kRedirectManifestRenderer();
			$renderer->entryId = $this->entryId;
			$renderer->tokenizer = $this->tokenizer;
			$renderer->flavor = $flavor;
			return $renderer;
		}
		
		$duration = null;
		$flavors = $this->buildHttpFlavorsArray($duration);
		
		$flavors = $this->sortFlavors($flavors);

		$renderer = new kM3U8ManifestRenderer();
		$renderer->entryId = $this->entryId;
		$renderer->tokenizer = $this->tokenizer;
		$renderer->flavors = $flavors;
		return $renderer;
	}

	/**
	 * @return kManifestRenderer
	 */
	private function serveHds()
	{
	    $duration = null;
		$flavors = $this->buildHttpFlavorsArray($duration);
		
		$flavors = $this->sortFlavors($flavors);

		$renderer = new kF4Mv2ManifestRenderer();
		$renderer->entryId = $this->entryId;
		$renderer->tokenizer = $this->tokenizer;						
		$renderer->flavors = $flavors;
		$renderer->duration = $duration;
		return $renderer;		
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveHDNetworkSmil()
	{
	    $duration = null;
		$flavors = $this->buildHttpFlavorsArray($duration);
		
		$this->ensureUniqueBitrates($flavors);		// When playing HDS with Akamai HD the bitrates in the manifest must be unique 

		$renderer = new kSmilManifestRenderer();
		$renderer->entryId = $this->entryId;
		$renderer->tokenizer = $this->tokenizer;
		$renderer->flavors = $flavors;
		$renderer->duration = $duration;
		return $renderer;
	}
	
	/**
	 * @return kManifestRenderer
	 */
	private function serveHDNetwork()
	{
		$duration = $this->entry->getDurationInt();
		$mediaUrl = requestUtils::getHost().str_replace("f4m", "smil", str_replace("hdnetwork", "hdnetworksmil", $_SERVER["REQUEST_URI"])); 

		$renderer = new kF4MManifestRenderer();
		$renderer->entryId = $this->entryId;
		$renderer->duration = $duration;
		$renderer->mediaUrl = $mediaUrl;
		return $renderer;
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
		
	/**
	 * @return kManifestRenderer
	 */
	private function serveHDNetworkManifest()
	{			
		$flavor = $this->getSecureHdUrl();
		if (!$flavor)
		{
			KalturaLog::debug('No flaovr found');
			return null;
		}
			
		$duration = $this->entry->getDurationInt();
		
		$renderer = new kF4MManifestRenderer();
		$renderer->entryId = $this->entryId;
		$renderer->duration = $duration;
		$renderer->flavors = array($flavor);
		$renderer->tokenizer = $this->tokenizer;
		return $renderer;
	}	

	/**
	 * @return kManifestRenderer
	 */
	private function serveRtsp()
	{
		$flavorAsset = $this->getFlavorAsset();
		$flavorInfo = $this->getFlavorHttpUrl($flavorAsset);

		$renderer = new kRtspManifestRenderer();
		$renderer->entryId = $this->entryId;
		$renderer->tokenizer = $this->tokenizer;
		$renderer->flavor = $flavorInfo;
		return $renderer;
	}
	
	///////////////////////////////////////////////////////////////////////////////////
	//	Main functions

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
  
	public function validateStorageId()
	{
		if(!$this->storageId)
			return true;
			
		$storage = StorageProfilePeer::retrieveByPK($this->storageId);
		
		// no storage found
		if(!$storage)
			die;
		
		$partner = $this->entry->getPartner();
		
		// partner configured to use kaltura data centers only
		if($partner->getStorageServePriority() ==  StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
			die;
		
		// storage doesn't belong to the partner
		if($storage->getPartnerId() != $partner->getId())
			die;
	}
	
	protected function initFlavorIds()
	{
		$flavorIdsStr = $this->getRequestParameter ( "flavorIds", null );
		if ($flavorIdsStr)
			$this->flavorIds = explode(",", $flavorIdsStr);
		
		$this->flavorId = $this->getRequestParameter ( "flavorId", null );
		if (!$this->flavorId) // in case a flavorId wasnt specified checking for a flavorParamId 
		{ 
			$flavorParamIds = $this->getRequestParameter ( "flavorParamIds", null );
			if ($flavorParamIds !== null)
			{
				$this->flavorIds = assetPeer::retrieveReadyFlavorsIdsByEntryId($this->entry->getId(), explode(",", $flavorParamIds));
				if (!$this->flavorIds || count($this->flavorIds) == 0)
				{
					KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
				}
			}
			else
			{
				$flavorParamId = $this->getRequestParameter ( "flavorParamId", null );
				if ($flavorParamId || $flavorParamId === "0")
				{
					$flavorAsset = assetPeer::retrieveByEntryIdAndParams($this->entry->getId(), $flavorParamId);
					if(!$flavorAsset)
					{
						KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
					}
				
					$this->flavorId = $flavorAsset->getId();
				}
			}
		}	
	}

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
		
		// replace space in the base64 string with + as space is invalid in base64 strings and caused
		// by symfony calling str_parse to replace + with spaces.
		// this happens only with params passed in the url path and not the query strings. specifically the ~ char at
		// a columns divided by 3 causes this issue (e.g. http://www.xyzw.com/~xxx)
		$referrer = base64_decode(str_replace(" ", "+", $base64Referrer));
		if (!is_string($referrer)) 
			$referrer = ""; // base64_decode can return binary data
			
		$this->secureEntryHelper = new KSecureEntryHelper($this->entry, $ksStr, $referrer, accessControlContextType::PLAY);
		if ($this->secureEntryHelper->shouldPreview())
		{
			$this->clipTo = $this->secureEntryHelper->getPreviewLength() * 1000;
		}
		else
		{
			$this->secureEntryHelper->validateForPlay();
		}
	}
	
	public function execute()
	{
		$this->seekFrom = $this->getRequestParameter ( "seekFrom" , -1);
		if ($this->seekFrom <= 0)
			$this->seekFrom = -1;

		$this->clipTo = $this->getRequestParameter ( "clipTo" , 0);
		
		$this->deliveryCode = $this->getRequestParameter( "deliveryCode", null );
		$playbackContext = $this->getRequestParameter( "playbackContext", null );
		
		$this->protocol = $this->getRequestParameter ( "protocol", null );
		if(!$this->protocol || $this->protocol === "null")
			$this->protocol = StorageProfile::PLAY_FORMAT_HTTP;
		
		$this->format = $this->getRequestParameter ( "format" );
		if(!$this->format)
			$this->format = StorageProfile::PLAY_FORMAT_HTTP;
			
		$this->tags = $this->getRequestParameter ( "tags", null );
		if (!$this->tags)
		{
			switch ($this->format)
			{
			case StorageProfile::PLAY_FORMAT_SILVER_LIGHT:
				$this->tags = array(
					array(assetParams::TAG_SLWEB),
				);
				break;
				
			case StorageProfile::PLAY_FORMAT_APPLE_HTTP:
			case StorageProfile::PLAY_FORMAT_HDS:
				$this->tags = array(
					array(assetParams::TAG_APPLEMBR),
				 	array('ipadnew', 'iphonenew'),
				 	array('ipad', 'iphone'),
				);
				break;
				
			default:
				$this->tags = array(
					array(assetParams::TAG_MBR),
					array(assetParams::TAG_WEB),
				);
			}
		}
		else
		{
			$this->tags = array(array($this->tags));
		}
		
		$this->cdnHost = $this->getRequestParameter ( "cdnHost", null );
		
		$this->preferredBitrate = $this->getRequestParameter ( "preferredBitrate", null );
		$this->maxBitrate = $this->getRequestParameter ( "maxBitrate", null );
		if(($this->maxBitrate) && ((!is_numeric($this->maxBitrate)) || ($this->maxBitrate <= 0)))
			KExternalErrors::dieError(KExternalErrors::INVALID_MAX_BITRATE);
			
		$this->initEntry();
		
		$this->initFlavorIds();
		
		$this->storageId = $this->getRequestParameter ( "storageId", null );
		$this->validateStorageId();		
		
		$partner = $this->entry->getPartner();
		
		if(!$this->cdnHost || $partner->getForceCdnHost())
			$this->cdnHost = myPartnerUtils::getCdnHost($this->entry->getPartnerId(), $this->protocol);

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
			
		$renderer = null;
	
		switch($this->format)
		{
			case StorageProfile::PLAY_FORMAT_HTTP:
				$renderer = $this->serveHttp();
				break;
				
			case StorageProfile::PLAY_FORMAT_RTMP:
				$renderer = $this->serveRtmp();
				break;
				
			case StorageProfile::PLAY_FORMAT_SILVER_LIGHT:
				$renderer = $this->serveSilverLight();
				break;
				
			case StorageProfile::PLAY_FORMAT_APPLE_HTTP:
				$renderer = $this->serveAppleHttp();
				break;

			case StorageProfile::PLAY_FORMAT_HDS:
				$renderer = $this->serveHds();
				break;
				
			case "url":
				$this->format = "http"; // build url for an http delivery
				$renderer = $this->serveUrl();
				break;
				
			case "rtsp":
				$renderer = $this->serveRtsp();
				break;				
				
			case "hdnetworksmil":
				$renderer = $this->serveHDNetworkSmil();
				break;
				
			case "hdnetwork":
				$renderer = $this->serveHDNetwork();
				break;

			case "hdnetworkmanifest":
				$renderer = $this->serveHDNetworkManifest();
				break;
		}
		
		if (!$renderer)
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, 'This format is unsupported');
		
		$canCacheAccessControl = false;
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

		$renderer->output($playbackContext);
	}
}

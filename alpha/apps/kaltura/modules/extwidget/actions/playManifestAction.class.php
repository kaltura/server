<?php
class playManifestAction extends kalturaAction
{
	/**
	 * @var string
	 */
	private $format;
	
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
	 * @var array
	 */
	private $flavorIds = null;
	
	/**
	 * @var string
	 */
	private $deliveryCode = null;
	
	const PLAY_STREAM_TYPE_LIVE = 'live';
	const PLAY_STREAM_TYPE_RECORDED = 'recorded';
	const PLAY_STREAM_TYPE_ANY = 'any';

	private function buildXml($streamType, array $flavors, $duration = null, $baseUrl = null, $mediaUrl = null)
	{
		$durationXml = ($duration ? "<duration>$duration</duration>" : '');
		$baseUrlXml = ($baseUrl ? "<baseURL>".htmlspecialchars($baseUrl)."</baseURL>" : '');
		$flvaorsXml = '';
		
		$deliveryCodeStr = '';
		if ($streamType == self::PLAY_STREAM_TYPE_LIVE && $this->deliveryCode)
		{
			$deliveryCodeStr = '?deliveryCode='.$this->deliveryCode;
		}
		
		foreach($flavors as $flavor)
		{
			$url = $flavor['url'];
			$bitrate	= isset($flavor['bitrate'])	? $flavor['bitrate']	: 0;
			$width		= isset($flavor['width'])	? $flavor['width']		: 0;
			$height		= isset($flavor['height'])	? $flavor['height']		: 0;
			
						
			$url = htmlspecialchars($url);
			$url .= $deliveryCodeStr;
			$flvaorsXml .= "<media url=\"$url\" bitrate=\"$bitrate\" width=\"$width\" height=\"$height\"/>";
		}		
		
		$mimeType = 'video/x-flv';
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
				$mimeType = 'audio/mpeg';
		}
		
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<manifest xmlns=\"http://ns.adobe.com/f4m/1.0\">
					<id>$this->entryId</id>
					<mimeType>$mimeType</mimeType>
					<streamType>$streamType</streamType>					
					$durationXml
					$baseUrlXml
					$flvaorsXml
					$mediaUrl
				</manifest>";
					
		// <drmMetadata url=\"$metaDataUrl\"/>
	}
	
	private function removeMaxBitrateFlavors($flavors)
	{
		if (!$this->maxBitrate)			
			return $flavors;
			
		$returnedFlavors = array();		
			
		foreach ($flavors as $flavor){
			if ($flavor->getBitrate() <= $this->maxBitrate){
				$returnedFlavors[] = $flavor;
			}
		}
	
		return $returnedFlavors;
	}
	
	private function buildFlavorsArray(&$duration, $oneOnly = false)
	{
		$flavorAssets = array();
		
		if($this->flavorId && ($flavorAsset = assetPeer::retrieveById($this->flavorId)) != null)
		{
			if($this->format != StorageProfile::PLAY_FORMAT_SILVER_LIGHT && $flavorAsset->hasTag(assetParams::TAG_WEB) && $flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
				$flavorAssets[] = $flavorAsset;
				
			if($this->format == StorageProfile::PLAY_FORMAT_SILVER_LIGHT && $flavorAsset->hasTag(assetParams::TAG_SLWEB) && $flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
				$flavorAssets[] = $flavorAsset;
		}
		elseif($oneOnly)
		{
			$flavorAsset = assetPeer::retrieveBestPlayByEntryId($this->entryId);
			
			if(!$flavorAsset)
			{
				$tag = assetParams::TAG_WEB;
				if($this->format == StorageProfile::PLAY_FORMAT_SILVER_LIGHT)
					$tag = assetParams::TAG_SLWEB;
				elseif($this->format == StorageProfile::PLAY_FORMAT_APPLE_HTTP)
					$tag = assetParams::TAG_APPLEMBR;
					
				$webFlavorAssets = assetPeer::retrieveReadyByEntryIdAndTag($this->entryId, $tag);
				if(count($webFlavorAssets))
					$flavorAsset = reset($webFlavorAssets);
			}
				
			if($flavorAsset)
				$flavorAssets[] = $flavorAsset;
		}
		else 
		{
			if ($this->flavorIds)
			{
				$tmpFlavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($this->entryId);
				$flavorAssets = array();
				foreach($tmpFlavorAssets as $flavorAsset)
				{
					if (in_array($flavorAsset->getId(), $this->flavorIds))
						$flavorAssets[] = $flavorAsset;
				}
			}
			else 
			{
				$tag = assetParams::TAG_MBR;
				if($this->format == StorageProfile::PLAY_FORMAT_SILVER_LIGHT)
					$tag = assetParams::TAG_SLWEB;
				elseif($this->format == StorageProfile::PLAY_FORMAT_APPLE_HTTP)
					$tag = assetParams::TAG_APPLEMBR;
				$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryIdAndTag($this->entryId, $tag);

				if(!count($flavorAssets) && $tag == assetParams::TAG_MBR)
					$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryIdAndTag($this->entryId, assetParams::TAG_WEB);
				
				// if format is APPLE HTTP and there aren't any segmented flavors use ipad and iphone with
				// akamai hd on the fly segmenter
				if(!count($flavorAssets) && $this->format == StorageProfile::PLAY_FORMAT_APPLE_HTTP)
				{
					$tmpFlavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($this->entryId);
					$flavorAssets = array();
					
					$tagVersions = array("new", "");

					// try first the ipadnew and iphonenew, optimized for segmenting (with fixed GOP and IDR frames)
					// if no such flavors were found, use the ipad,iphone and pray
					foreach($tagVersions as $tagVersion)
					{
						foreach($tmpFlavorAssets as $flavorAsset)
						{
							if ($flavorAsset->hasTag("ipad$tagVersion") || $flavorAsset->hasTag("iphone$tagVersion"))
								$flavorAssets[] = $flavorAsset;
						}
						
						if (count($flavorAssets))
							break;
					}
				}
			}
		}
		
		$flavors = array();
		$durationSet = false;
		foreach($flavorAssets as $flavorAsset)
		{
			/* @var $flavorAsset flavorAsset */
			
			if(!$durationSet)
			{
				$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
				if($mediaInfo && ($mediaInfo->getVideoDuration() || $mediaInfo->getAudioDuration() || $mediaInfo->getContainerDuration()))
				{
					$duration = ($mediaInfo->getVideoDuration() ? $mediaInfo->getVideoDuration() : ($mediaInfo->getAudioDuration() ? $mediaInfo->getAudioDuration() : $mediaInfo->getContainerDuration()));
					$duration /= 1000;
					$durationSet = true;
				}
			}
			
			$url = $this->getFlavorHttpUrl($flavorAsset);
			$ext = $flavorAsset->getFileExt();
			if(!$ext)
			{
				$parsedUrl = parse_url($url);
				$ext = pathinfo($parsedUrl['path'], PATHINFO_EXTENSION);
			}
			
			$flavors[] = array(
				'url' => $url,
				'ext' => $ext,
				'bitrate' => $flavorAsset->getBitrate(),
				'width' => $flavorAsset->getWidth(),
				'height' => $flavorAsset->getHeight(),
			);
		}
		return $flavors;
	}
	
	private function getExternalStorageUrl(flavorAsset $flavorAsset, FileSyncKey $key)
	{
		$partner = $this->entry->getPartner();
		if(!$partner || !$partner->getStorageServePriority() || $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
			return null;
			
		if(is_null($this->storageId) && $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
			if(kFileSyncUtils::getReadyInternalFileSyncForKey($key)) // check if having file sync on kaltura dcs
				return null;
				
		$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $this->storageId);
		if($fileSync)
		{
			$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
			if(!$storage)
				return null;
				
			$urlManager = kUrlManager::getUrlManagerByStorageProfile($fileSync->getDc());
			$urlManager->setClipTo($this->clipTo);
			$urlManager->setSeekFromTime($this->seekFrom);
			$urlManager->setFileExtension($flavorAsset->getFileExt());
			
			$url = rtrim($storage->getDeliveryHttpBaseUrl(), "/");
			$url .= "/". ltrim($urlManager->getFileSyncUrl($fileSync), "/");
			
			return $url;
		}
			
		return null;
	}
	
	private function getSmoothStreamUrl(FileSyncKey $key)
	{
		$kalturaFileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);

		$iisHost = parse_url(myPartnerUtils::getIisHost($this->entry->getPartnerId(), $this->protocol), PHP_URL_HOST);	
		$kalturaUrlManager = kUrlManager::getUrlManagerByCdn($iisHost);
		$kalturaUrlManager->setClipTo($this->clipTo);
		$kalturaUrlManager->setProtocol(StorageProfile::PLAY_FORMAT_SILVER_LIGHT);
		
		$partner = $this->entry->getPartner();
		if(!$partner->getStorageServePriority() || $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
		{
			if($kalturaFileSync)
				return $kalturaUrlManager->getFileSyncUrl($kalturaFileSync);
				
			return null;
		}
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
		{
			if($kalturaFileSync)
				return $kalturaUrlManager->getFileSyncUrl($kalturaFileSync);
		}
				
		$externalFileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key);
		$externalUrlManager = kUrlManager::getUrlManagerByStorageProfile($externalFileSync->getDc());
		$externalUrlManager->setClipTo($this->clipTo);
		$externalUrlManager->setProtocol(StorageProfile::PLAY_FORMAT_SILVER_LIGHT);
		
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST)
		{
			if($externalFileSync)
				return $externalUrlManager->getFileSyncUrl($externalFileSync);
				
			if($kalturaFileSync)
				return $kalturaUrlManager->getFileSyncUrl($kalturaFileSync);
		}
		
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
		{
			if($externalFileSync)
				return $externalUrlManager->getFileSyncUrl($externalFileSync);
				
			return null;
		}
			
		return null;
	}
	
	private function getFlavorHttpUrl(flavorAsset $flavorAsset)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$externalStorageUrl = $this->getExternalStorageUrl($flavorAsset, $syncKey);
		if($externalStorageUrl)
			return $externalStorageUrl;
			
		if($this->storageId) // must be specific external storage
			return null;
			
		$partner = $this->entry->getPartner();
		if($partner && $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
			return null;

		$urlManager = kUrlManager::getUrlManagerByCdn($this->cdnHost);
		$urlManager->setClipTo($this->clipTo);
		$urlManager->setSeekFromTime($this->seekFrom);
		$urlManager->setDomain($this->cdnHost);
		$urlManager->setProtocol($this->format);

	    $url = $urlManager->getFlavorAssetUrl($flavorAsset);
	    		
		if ($this->format == StorageProfile::PLAY_FORMAT_RTSP)
		{
			echo '<html><head><meta http-equiv="refresh" content="0;url='.$url.'"></head></html>';
			die;			
		}
		
		if (strpos($url, "/") === 0)
			$url = $this->cdnHost . $url;
			
		$url = preg_replace('/^https?:\/\//', '', $url);
		$url = str_replace('//', '/', $url);
		return $this->protocol . '://' . $url;
	}
	
	private function serveUrl()
	{
		
		if($this->entry->getType() != entryType::MEDIA_CLIP)
		{
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
		}
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
						if($this->flavorId && ($flavorAsset = assetPeer::retrieveById($this->flavorId)) != null)
						{
							$url = $this->getFlavorHttpUrl($flavorAsset);
							header("location:$url");
							die;
						}
						KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
				}
				
			default:
				break;
		}
		
		KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
	}
	
	private function serveHttp()
	{
	    KalturaLog::debug("entry type: ".$this->entry->getType());
		if($this->entry->getType() != entryType::MEDIA_CLIP)
			KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);

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
						$duration = $this->entry->getDurationInt();
						$flavors = $this->buildFlavorsArray($duration, true);
						KalturaLog::debug("retrieved entry duration: [$duration]");
						return $this->buildXml(self::PLAY_STREAM_TYPE_RECORDED, $flavors, $duration);
				}
				
			default:
				break;
		}
		
		KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
	}
	
	private function serveRtmp()
	{
		switch($this->entry->getType())
		{
			case entryType::MEDIA_CLIP:
				
				$duration = $this->entry->getDurationInt();
				$flavorAssets = array();
				
				if($this->flavorId)
				{
					$flavorAsset = assetPeer::retrieveById($this->flavorId);
					if(!$flavorAsset->hasTag(assetParams::TAG_WEB))
						KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
						
					if(!$flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
						KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
						
					$flavorAssets[] = $flavorAsset;
				}
				else 
				{
					$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryIdAndTag($this->entryId, assetParams::TAG_MBR);
					if(!count($flavorAssets))
						$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryIdAndTag($this->entryId, assetParams::TAG_WEB);
				}

				$flavorAssets = $this->removeMaxBitrateFlavors($flavorAssets);
				
				if(!$this->storageId)
				{
					$partner = $this->entry->getPartner();
					$finalFlavors = array();
					if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
					{
						foreach($flavorAssets as $flavorAsset)
						{
							$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
							$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
							if($fileSync)
								$finalFlavors[] = $flavorAsset;
						}
					}
					if(!count($finalFlavors) && $partner->getStorageServePriority() && $partner->getStorageServePriority() != StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
					{
						$storages = StorageProfilePeer::retrieveExternalByPartnerId($partner->getId());
						if(count($storages) == 1)
						{
							$this->storageId = $storages[0]->getId();
						}
						elseif(count($storages))
						{
							$storagesFlavors = array();
							foreach($storages as $storage)
							{
								$storagesFlavors[$storage->getId()] = array();
								foreach($flavorAssets as $flavorAsset)
								{
									$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
									$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $storage->getId());
									if($fileSync)
										$storagesFlavors[$storage->getId()][] = $flavorAsset;
								}
							}
							
							$maxCount = 0;
							foreach($storagesFlavors as $storageId => $storageFlavors)
							{
								$count = count($storageFlavors);
								if($count > $maxCount)
								{
									$maxCount = $count;
									$this->storageId = $storageId;
									$finalFlavors = $storageFlavors;
								}
							}
							
							if (!count($finalFlavors) && $partner->getStorageServePriority() != StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
							{ //take flavors from kaltura storage
								foreach($flavorAssets as $flavorAsset)
								{
									$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
									$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
									if($fileSync)
										$finalFlavors[] = $flavorAsset;
								}
							}
							
							$flavorAssets = $finalFlavors;
						}
						else
						{
							foreach($flavorAssets as $flavorAsset)
							{
								$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
								$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
								if($fileSync)
									$finalFlavors[] = $flavorAsset;
							}
						}
					}
				}
				
				foreach($flavorAssets as $flavorAsset)
				{
					$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
					if($mediaInfo && ($mediaInfo->getVideoDuration() || $mediaInfo->getAudioDuration() || $mediaInfo->getContainerDuration()))
					{
						$duration = ($mediaInfo->getVideoDuration() ? $mediaInfo->getVideoDuration() : ($mediaInfo->getAudioDuration() ? $mediaInfo->getAudioDuration() : $mediaInfo->getContainerDuration()));
						$duration /= 1000;
						break;
					}
				}
					
				$baseUrl = null;
				$flavors = array();
				if($this->storageId)
				{
					$storage = StorageProfilePeer::retrieveByPK($this->storageId);
					if(!$storage)
						die;
							
					$baseUrl = $storage->getDeliveryRmpBaseUrl();
					// get all flavors with external urls
					foreach($flavorAssets as $flavorAsset)
					{
						$key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
						$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $this->storageId);
						if(!$fileSync)
							continue;
						
						$urlManager = kUrlManager::getUrlManagerByStorageProfile($fileSync->getDc());
						$urlManager->setClipTo($this->clipTo);
						$urlManager->setContainerFormat($flavorAsset->getContainerFormat());
						if($flavorAsset->getFileExt() === null) // if the extension is missig use the one from the actual path
        					$urlManager->setFileExtension(pathinfo($fileSync->getFilePath(), PATHINFO_EXTENSION));
        				else
							$urlManager->setFileExtension($flavorAsset->getFileExt());
        					
						$urlManager->setProtocol(StorageProfile::PLAY_FORMAT_RTMP);
						
						$url = $urlManager->getFileSyncUrl($fileSync);
						$url = preg_replace('/^\//', '', $url);
						
						$flavors[] = array(
							'url' => $url,
							'bitrate' => $flavorAsset->getBitrate(),
							'width' => $flavorAsset->getWidth(),
							'height' => $flavorAsset->getHeight(),
						    'extension' => $flavorAsset->getFileExt(),
						);
					}
				}
				
				if (!$this->storageId || (!count($flavors) && $partner->getStorageServePriority() != StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)) 
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

					$urlManager = kUrlManager::getUrlManagerByCdn($rtmpHost);						
		
					// get all flavors with kaltura urls
					foreach($flavorAssets as $flavorAsset)
					{
						/* @var $flavorAsset flavorAsset */
						
						$urlManager->setClipTo($this->clipTo);
						$urlManager->setFileExtension($flavorAsset->getFileExt());
						$urlManager->setContainerFormat($flavorAsset->getContainerFormat());
						$urlManager->setProtocol(StorageProfile::PLAY_FORMAT_RTMP);
						$url = $urlManager->getFlavorAssetUrl($flavorAsset);
						$url = preg_replace('/^\//', '', $url);
						
						$ext = $flavorAsset->getFileExt();
						if(!$ext)
						{
							$parsedUrl = parse_url($url);
							$ext = pathinfo($parsedUrl['path'], PATHINFO_EXTENSION);
						}
						
						$flavors[] = array(
							'url' => $url,
							'ext' => $ext,
							'bitrate' => $flavorAsset->getBitrate(),
							'width' => $flavorAsset->getWidth(),
							'height' => $flavorAsset->getHeight(),
						);
					}
				}
				
				if(!count($flavors))
					KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
					
				if (strpos($this->protocol, "rtmp") === 0)
					$baseUrl = $this->protocol . '://' . preg_replace('/^rtmp.*?:\/\//', '', $baseUrl);
					
				$urlManager->finalizeUrls($baseUrl, $flavors);
					
				return $this->buildXml(self::PLAY_STREAM_TYPE_RECORDED, $flavors, $duration, $baseUrl);

			case entryType::LIVE_STREAM:
				
				$streamId = $this->entry->getStreamRemoteId();
				$streamUsername = $this->entry->getStreamUsername();
				
				$baseUrl = $this->entry->getStreamUrl();
				$baseUrl = rtrim($baseUrl, '/');
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
					
				return $this->buildXml(self::PLAY_STREAM_TYPE_LIVE, $flavors, null, $baseUrl);
		}
		
		KExternalErrors::dieError(KExternalErrors::INVALID_ENTRY_TYPE);
	}

	private function serveSilverLight()
	{
		$duration = $this->entry->getDurationInt();
		$flavors = $this->buildFlavorsArray($duration);
		$streamType = self::PLAY_STREAM_TYPE_RECORDED;
		
		$durationXml = ($duration ? "<duration>$duration</duration>" : '');
		$flvaorsXml = '';
		foreach($flavors as $flavor)
		{
			$url = $flavor['url'];
			$bitrate	= isset($flavor['bitrate'])	? $flavor['bitrate']	: 0;
			$width		= isset($flavor['width'])	? $flavor['width']		: 0;
			$height		= isset($flavor['height'])	? $flavor['height']		: 0;
			
			$url = htmlspecialchars($url);
			$flvaorsXml .= "<media url=\"$url\" bitrate=\"$bitrate\" width=\"$width\" height=\"$height\"/>";
		}
			
		$url = '';
		
		$syncKey = $this->entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
		$url = $this->getSmoothStreamUrl($syncKey);
		$url = htmlspecialchars($url);
			
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<manifest url=\"$url\">
					<id>$this->entryId</id>
					<streamType>$streamType</streamType>
					$durationXml
					$flvaorsXml
				</manifest>";
					
//		header("Location: $url/manifest");
//		die;
	}

    private function serveAppleHttp()
	{
		$content = "#EXTM3U\n";
		$duration = null;
		$flavors = $this->buildFlavorsArray($duration);
		uasort(&$flavors, array($this,'flavorCmpFunction'));
		foreach($flavors as $flavor)
		{
			$bitrate = (isset($flavor['bitrate']) ? $flavor['bitrate'] : 0) * 1000;
			$content .= "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=".$bitrate."\n";
			$content .= $flavor['url']."\n";
		}

		return $content;
	}
	
	/**
	 * 
	 * Private function which compares 2 flavors in order to sort an array.
	 * If a flavor's width and height parameters are equal to 0, it is 
	 * automatically moved down the list so the player will not start playing it by default.
	 * @param asset $flavor1
	 * @param asset $flavor2
	 */
    private function flavorCmpFunction ($flavor1, $flavor2)
	{
	    if ($flavor1['height'] == 0 && $flavor1['width'] == 0)
	    {
	        return 1;
	    }
	    if ($flavor2['height'] == 0 && $flavor2['width'] == 0)
	    {
	        return -1;
	    }
	    $bitrate1 = isset($flavor1['bitrate']) ? $flavor1['bitrate'] : 0;
	    $bitrate2 = isset($flavor2['bitrate']) ? $flavor2['bitrate'] : 0;
	    if ($bitrate1 >= $bitrate2)
	    {
	        return 1;
	    }
	    
        return -1;
	}
	
	private function serveHDNetwork()
	{
		$duration = $this->entry->getDurationInt();
		$flavors = $this->buildFlavorsArray($duration);
		
		$durationXml = ($duration ? "<duration>$duration</duration>" : '');
		$flavorsXml = '';
		$domain = '';
		foreach($flavors as $flavor)
		{
			$url = $flavor['url'];
			$bitrate = isset($flavor['bitrate'])	? $flavor['bitrate']	: 0;

			$domain = parse_url($url, PHP_URL_SCHEME)."://".parse_url($url, PHP_URL_HOST);
			$url = parse_url($url, PHP_URL_PATH);
			
			$url = htmlspecialchars($url);
			$flavorsXml .= "<video src=\"$url\" system-bitrate=\"".($bitrate * 1000)."\"/>"; 
		}
			
		return '<?xml version="1.0"?>
<!DOCTYPE smil PUBLIC "-//W3C//DTD SMIL 2.0//EN" "http://www.w3.org/2001/SMIL20/SMIL20.dtd">
<smil xmlns="http://www.w3.org/2001/SMIL20/Language">
	<head>
		<meta name="title" content="" />
		<meta name="httpBase" content="'.$domain.'" />
		<meta name="vod" content="true"/>
	</head>
	<body>
		<switch id="video">'.$flavorsXml.'</switch>
	</body></smil>';
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
	
	public function execute()
	{
		$this->entryId = $this->getRequestParameter ( "entryId", null );
		$this->flavorId = $this->getRequestParameter ( "flavorId", null );
		$this->storageId = $this->getRequestParameter ( "storageId", null );
		$this->maxBitrate = $this->getRequestParameter ( "maxBitrate", null );
		$this->deliveryCode = $this->getRequestParameter( "deliveryCode", null );
		
		$flavorIdsStr = $this->getRequestParameter ( "flavorIds", null );
		if ($flavorIdsStr)
			$this->flavorIds = explode(",", $flavorIdsStr);
		
		
		$this->entry = entryPeer::retrieveByPKNoFilter( $this->entryId );
		if ( ! $this->entry )
		{
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
		}
		
		if (!$this->flavorId) // in case a flavorId wasnt specified checking for a flavorParamId 
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
		
		$this->validateStorageId();
		
		$this->protocol = $this->getRequestParameter ( "protocol", null );
		if(!$this->protocol)
			$this->protocol = StorageProfile::PLAY_FORMAT_HTTP;
		
		$this->format = $this->getRequestParameter ( "format" );
		if(!$this->format)
			$this->format = StorageProfile::PLAY_FORMAT_HTTP;
		
		$this->cdnHost = $this->getRequestParameter ( "cdnHost", null );
		$partner = $this->entry->getPartner();
		
		if(!$this->cdnHost || $partner->getForceCdnHost())
			$this->cdnHost = myPartnerUtils::getCdnHost($this->entry->getPartnerId(), $this->protocol);
		
		if(($this->maxBitrate) && ((!is_numeric($this->maxBitrate)) || ($this->maxBitrate <= 0)))
			KExternalErrors::dieError(KExternalErrors::INVALID_MAX_BITRATE);
			
		$ksStr = $this->getRequestParameter("ks");
	
		$base64Referrer = $this->getRequestParameter("referrer");
		
		// replace space in the base64 string with + as space is invalid in base64 strings and caused
		// by symfony calling str_parse to replace + with spaces.
		// this happens only with params passed in the url path and not the query strings. specifically the ~ char at
		// a columns divided by 3 causes this issue (e.g. http://www.xyzw.com/~xxx)
		$referrer = base64_decode(str_replace(" ", "+", $base64Referrer));
		if (!is_string($referrer)) 
			$referrer = ""; // base64_decode can return binary data
			
		$securyEntryHelper = new KSecureEntryHelper($this->entry, $ksStr, $referrer, accessControlContextType::PLAY);
		if ($securyEntryHelper->shouldPreview())
		{
			$this->clipTo = $securyEntryHelper->getPreviewLength() * 1000;
		}
		else
		{
			$securyEntryHelper->validateForPlay($this->entry, $ksStr);
		}
		
		// grab seekFrom parameter and normalize url
		$this->seekFrom = $this->getRequestParameter ( "seekFrom" , -1);
		
		if ($this->seekFrom <= 0)
			$this->seekFrom = -1;
		
		if ( $this->entry->getStatus() == entryStatus::DELETED )
		{
			// because the fiter was turned off - a manual check for deleted entries must be done.
			die;
		}
				
		$xml = null;
		switch($this->format)
		{
		    
			case StorageProfile::PLAY_FORMAT_HTTP:
				$xml = $this->serveHttp();
				break;
				
			case StorageProfile::PLAY_FORMAT_RTMP:
				$xml = $this->serveRtmp();
				break;
				
			case StorageProfile::PLAY_FORMAT_SILVER_LIGHT:
				$xml = $this->serveSilverLight();
				break;
				
			case StorageProfile::PLAY_FORMAT_APPLE_HTTP:
				$xml = $this->serveAppleHttp();
				break;
				
			case "url":
				$this->format = "http"; // build url for an http delivery
				return $this->serveUrl();
				break;
				
			case "rtsp":
				return $this->serveUrl();
				break;				
				
			case "hdnetworksmil":
				$xml = $this->serveHDNetwork();
				break;
				
			case "hdnetwork":
				$duration = $this->entry->getDurationInt();
				$mediaUrl = "<media url=\"".requestUtils::getHost().str_replace("f4m", "smil", str_replace("hdnetwork", "hdnetworksmil", $_SERVER["REQUEST_URI"]))."\"/>"; 
						
				$xml =$this->buildXml(self::PLAY_STREAM_TYPE_RECORDED, array(), $duration, null, $mediaUrl);
				break;
		}
		
		if($this->format == StorageProfile::PLAY_FORMAT_APPLE_HTTP)
		{
			header("Content-Type: text/plain; charset=UTF-8");
		}
		else
		{
			header("Content-Type: text/xml; charset=UTF-8");
			header("Content-Disposition: inline; filename=manifest.xml");
		}

		// for now add caching headers only for specific partners listed in kConf
		// later caching will be used for all partners, and url tokenization will be done in the caching layer
		if (kConf::hasParam("optimized_playback"))
		{
			$partnerId = $this->entry->getPartnerId();
			$optimizedPlayback = kConf::get("optimized_playback");
			if (isset($optimizedPlayback[$partnerId]))
			{
				$params = null;
				parse_str($optimizedPlayback[$partner_id], $params);
				if (isset($params['cache_playmanifest']) && $params['cache_playmanifest'])
					requestUtils::sendCachingHeaders(60);
			}
		}
		
		echo $xml;
		die;
	}
}

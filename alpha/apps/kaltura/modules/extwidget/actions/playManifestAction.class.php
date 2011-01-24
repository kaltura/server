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
	
	const PLAY_STREAM_TYPE_LIVE = 'live';
	const PLAY_STREAM_TYPE_RECORDED = 'recorded';
	const PLAY_STREAM_TYPE_ANY = 'any';

	private function buildXml($streamType, array $flavors, $mimeType = 'video/x-flv', $duration = null, $baseUrl = null)
	{
		$durationXml = ($duration ? "<duration>$duration</duration>" : '');
		$baseUrlXml = ($baseUrl ? "<baseURL>$baseUrl</baseURL>" : '');
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
		
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<manifest xmlns=\"http://ns.adobe.com/f4m/1.0\">
					<id>$this->entryId</id>
					<mimeType>$mimeType</mimeType>
					<streamType>$streamType</streamType>					
					$durationXml
					$baseUrlXml
					$flvaorsXml
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
		
		if($this->flavorId && ($flavorAsset = flavorAssetPeer::retrieveById($this->flavorId)) != null)
		{
			if($this->format != StorageProfile::PLAY_FORMAT_SILVER_LIGHT && $flavorAsset->hasTag(flavorParams::TAG_WEB) && $flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
				$flavorAssets[] = $flavorAsset;
				
			if($this->format == StorageProfile::PLAY_FORMAT_SILVER_LIGHT && $flavorAsset->hasTag(flavorParams::TAG_SLWEB) && $flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
				$flavorAssets[] = $flavorAsset;
		}
		elseif($oneOnly)
		{
			$flavorAsset = flavorAssetPeer::retrieveBestPlayByEntryId($this->entryId);
			
			if(!$flavorAsset)
			{
				$tag = flavorParams::TAG_WEB;
				if($this->format == StorageProfile::PLAY_FORMAT_SILVER_LIGHT)
					$tag = flavorParams::TAG_SLWEB;
					
				$webFlavorAssets = flavorAssetPeer::retreiveReadyByEntryIdAndTag($this->entryId, $tag);
				if(count($webFlavorAssets))
					$flavorAsset = reset($webFlavorAssets);
			}
				
			if($flavorAsset)
				$flavorAssets[] = $flavorAsset;
		}
		else 
		{
			$tag = flavorParams::TAG_MBR;
			if($this->format == StorageProfile::PLAY_FORMAT_SILVER_LIGHT)
				$tag = flavorParams::TAG_SLWEB;
				
			$flavorAssets = flavorAssetPeer::retreiveReadyByEntryIdAndTag($this->entryId, $tag);
			
			if(!count($flavorAssets) && $tag == flavorParams::TAG_MBR)
				$flavorAssets = flavorAssetPeer::retreiveReadyByEntryIdAndTag($this->entryId, flavorParams::TAG_WEB);
		}
		
		$flavorAssets = $this->removeMaxBitrateFlavors($flavorAssets);
		
		$flavors = array();
		$durationSet = false;
		foreach($flavorAssets as $flavorAsset)
		{
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
			
			$flavors[] = array(
				'url' => $this->getFlavorHttpUrl($flavorAsset),
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
			$url = $storage->getDeliveryHttpBaseUrl() . '/' . $urlManager->getFileSyncUrl($fileSync);
			
			return $url;
		}
			
		return null;
	}
	
	private function getSmoothStreamUrl(FileSyncKey $key)
	{
		$kalturaFileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
	
		$iisHost = myPartnerUtils::getIisHost($this->entry->getPartnerId(), $this->protocol);
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
		$url = $urlManager->getFlavorAssetUrl($flavorAsset);
		
		$url = $this->cdnHost . $url;
		$url = preg_replace('/^https?:\/\//', '', $url);
		return $this->protocol . '://' . $url;
	}
	
	private function serveHttp()
	{
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
						return $this->buildXml(self::PLAY_STREAM_TYPE_RECORDED, $flavors, 'video/x-flv', $duration);
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
					$flavorAsset = flavorAssetPeer::retrieveById($this->flavorId);
					if(!$flavorAsset->hasTag(flavorParams::TAG_WEB))
						KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
						
					if(!$flavorAsset->getStatus() == flavorAsset::FLAVOR_ASSET_STATUS_READY)
						KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
						
					$flavorAssets[] = $flavorAsset;
				}
				else 
				{
					$flavorAssets = flavorAssetPeer::retreiveReadyByEntryIdAndTag($this->entryId, flavorParams::TAG_MBR);
					if(!count($flavorAssets))
						$flavorAssets = flavorAssetPeer::retreiveReadyByEntryIdAndTag($this->entryId, flavorParams::TAG_WEB);
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
						$urlManager->setFileExtension($flavorAsset->getFileExt());
						$urlManager->setProtocol(StorageProfile::PLAY_FORMAT_RTMP);
						$url = $urlManager->getFileSyncUrl($fileSync);
						$url = preg_replace('/^\//', '', $url);
						
						$flavors[] = array(
							'url' => $url,
							'bitrate' => $flavorAsset->getBitrate(),
							'width' => $flavorAsset->getWidth(),
							'height' => $flavorAsset->getHeight(),
						);
					}
				}
				else 
				{
					$partnerId = $this->entry->getPartnerId();
					$subpId = $this->entry->getSubpId();
					$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
					$baseUrl = myPartnerUtils::getRtmpUrl($partnerId);
				
					$urlManager = kUrlManager::getUrlManagerByCdn($this->cdnHost);
		
					// get all flavors with kaltura urls
					foreach($flavorAssets as $flavorAsset)
					{
						$urlManager->setClipTo($this->clipTo);
						$urlManager->setFileExtension($flavorAsset->getFileExt());
						$urlManager->setProtocol(StorageProfile::PLAY_FORMAT_RTMP);
						$url = $urlManager->getFlavorAssetUrl($flavorAsset);
						$url = preg_replace('/^\//', '', $url);
						
						$flavors[] = array(
							'url' => $url,
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
					
				return $this->buildXml(self::PLAY_STREAM_TYPE_RECORDED, $flavors, 'video/x-flv', $duration, $baseUrl);

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
					
				return $this->buildXml(self::PLAY_STREAM_TYPE_LIVE, $flavors, 'video/x-flv', null, $baseUrl);
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
		
		$this->entry = entryPeer::retrieveByPKNoFilter( $this->entryId );
		if ( ! $this->entry )
		{
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
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
		
		$ksStr = $this->getRequestParameter("ks");
	
		$base64Referrer = $this->getRequestParameter("referrer");
		$referrer = base64_decode($base64Referrer);
		if (!is_string($referrer)) 
			$referrer = ""; // base64_decode can return binary data
			
		$securyEntryHelper = new KSecureEntryHelper($this->entry, $ksStr, $referrer);
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
		}
		
		header("Content-Type: text/xml; charset=UTF-8");
		header("Content-Disposition: inline; filename=manifest.xml");
		echo $xml;
		die;
	}
}

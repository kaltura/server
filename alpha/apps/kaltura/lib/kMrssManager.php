<?php
class kMrssManager
{
	private static $mrssContributors = null;
	
	/**
	 * @param string $string
	 * @return string
	 */
	private static function stringToSafeXml($string)
	{
		$string = @iconv('utf-8', 'utf-8', $string);
		$partially_safe = kString::xmlEncode($string);
		$safe = str_replace(array('*', '/', '[', ']'), '',$partially_safe);
		
		return $safe;
	}
	
	/**
	 * @return array<IKalturaMrssContributor>
	 */
	public static function getMrssContributors()
	{
		if(self::$mrssContributors)
			return self::$mrssContributors;
			
		self::$mrssContributors = KalturaPluginManager::getPluginInstances('IKalturaMrssContributor');
		return self::$mrssContributors;
	}
	
	/**
	 * @param string $title
	 * @param string $link
	 * @param string $description
	 * @return string
	 */
	public static function getMrss($title, $link = null, $description = null)
	{
		$mrss = self::getMrssXml($title, $link, $description);
		return $mrss->asXML();
	}
	
	/**
	 * @param string $title
	 * @param string $link
	 * @param string $description
	 * @return SimpleXMLElement
	 */
	public static function getMrssXml($title, $link = null, $description = null)
	{
		$mrss = new SimpleXMLElement('<rss/>');
		$mrss->addAttribute('version', '2.0');
		$mrss->addAttribute('xmlns:content', 'http://www.w3.org/2001/XMLSchema-instance');
		
		$channel = $mrss->addChild('channel');
		$channel->addChild('title', self::stringToSafeXml($title));
		$channel->addChild('link', $link);
		$channel->addChild('description', self::stringToSafeXml($description));
		
		return $mrss;
	}
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 */
	protected static function appendMediaEntryMrss(entry $entry, SimpleXMLElement $mrss)
	{
		$media = $mrss->addChild('media');
		$media->addChild('mediaType', $entry->getMediaType());
		$media->addChild('duration', $entry->getLengthInMsecs());
		$media->addChild('conversionProfileId', $entry->getConversionProfileId());
		$media->addChild('flavorParamsIds', $entry->getFlavorParamsIds());
	}
	
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 */
	protected static function appendMixEntryMrss(entry $entry, SimpleXMLElement $mrss)
	{
		
	}
	
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 */
	protected static function appendPlaylistEntryMrss(entry $entry, SimpleXMLElement $mrss)
	{
		
	}
	
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 */
	protected static function appendDataEntryMrss(entry $entry, SimpleXMLElement $mrss)
	{
		$media = $mrss->addChild('livestream');
		$media->addChild('mediaType', $entry->getMediaType());
		$media->addChild('duration', $entry->getLengthInMsecs());
	}
	
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 */
	protected static function appendLiveStreamEntryMrss(entry $entry, SimpleXMLElement $mrss)
	{
		/*$bitrates = $entry->getStreamBitrates();
		foreach ($bitrates as $bitrate)
		{
			$content = $mrss->addChild('content');			
			$content->addAttribute('url', $entry->getPrimaryBroadcastingUrl);
			$content->addAttribute('height', $entry->getHeight());
			$content->addAttribute('width', $flavorParams->getWidth());
		}*/
	}

	private static function getExternalStorageUrl(Partner $partner, asset $asset, FileSyncKey $key)
	{
		if(!$partner->getStorageServePriority() || $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
			return null;
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
			if(kFileSyncUtils::getReadyInternalFileSyncForKey($key)) // check if having file sync on kaltura dcs
				return null;
				
		$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key);
		if(!$fileSync)
			return null;
			
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return null;
			
		$urlManager = kUrlManager::getUrlManagerByStorageProfile($fileSync->getDc());
		$urlManager->setFileExtension($asset->getFileExt());
		$url = $storage->getDeliveryHttpBaseUrl() . '/' . $urlManager->getFileSyncUrl($fileSync);
		
		return $url;
	}
	
	/**
	 * @param asset $asset
	 * @return string
	 */
	protected static function getAssetUrl(asset $asset)
	{
		$partner = PartnerPeer::retrieveByPK($asset->getPartnerId());
		if(!$partner)
			return null;
	
		$syncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$externalStorageUrl = self::getExternalStorageUrl($partner, $asset, $syncKey);
		if($externalStorageUrl)
			return $externalStorageUrl;
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
			return null;
		
		$cdnHost = myPartnerUtils::getCdnHost($asset->getPartnerId());
		
		$urlManager = kUrlManager::getUrlManagerByCdn($cdnHost);
		$urlManager->setDomain($cdnHost);
		$url = $urlManager->getAssetUrl($asset);
		$url = $cdnHost . $url;
		$url = preg_replace('/^https?:\/\//', '', $url);
			
		return 'http://' . $url;
	}
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 * @param string $link
	 * @return string
	 */
	public static function getEntryMrss(entry $entry, SimpleXMLElement $mrss = null, $link = null)
	{
		$mrss = self::getEntryMrssXml($entry, $mrss, $link);
		return $mrss->asXML();
	}
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 * @param string $link
	 * @param string $filterFlavors
	 * @return SimpleXMLElement
	 */
	public static function getEntryMrssXml(entry $entry, SimpleXMLElement $mrss = null, $link = null, $fitlerByFlovorParams = null)
	{
		if(!$mrss)
		{
			$mrss = new SimpleXMLElement('<item/>');
		}
		
		$mrss->addChild('entryId', $entry->getId());
		$mrss->addChild('createdAt', $entry->getCreatedAt(null));
		$mrss->addChild('title', self::stringToSafeXml($entry->getName()));
		$mrss->addChild('link', $link . $entry->getId());
		$mrss->addChild('type', $entry->getType());
		$mrss->addChild('licenseType', $entry->getLicenseType());
		$mrss->addChild('userId', $entry->getPuserId(true));
		$mrss->addChild('name', self::stringToSafeXml($entry->getName()));
		$mrss->addChild('description', self::stringToSafeXml($entry->getDescription()));
		$thumbnailUrl = $mrss->addChild('thumbnailUrl');
		$thumbnailUrl->addAttribute('url', $entry->getThumbnailUrl());
		$tags = $mrss->addChild('tags');
		foreach(explode(',', $entry->getTags()) as $tag)
			$tags->addChild('tag', self::stringToSafeXml($tag));
			
		$mrss->addChild('partnerData', self::stringToSafeXml($entry->getPartnerData()));
		$mrss->addChild('accessControlId', $entry->getAccessControlId());
		
		$categories = explode(',', $entry->getCategories());
		foreach($categories as $category)
			if($category)
				$mrss->addChild('category', self::stringToSafeXml($category));
		
		if($entry->getStartDate(null))
			$mrss->addChild('startDate', $entry->getStartDate(null));
		
		if($entry->getEndDate(null))
			$mrss->addChild('endDate', $entry->getEndDate(null));
		
		switch($entry->getType())
		{
			case entryType::MEDIA_CLIP:
				self::appendMediaEntryMrss($entry, $mrss);
				break;
				
			case entryType::MIX:
				self::appendMixEntryMrss($entry, $mrss);
				break;
				
			case entryType::PLAYLIST:
				self::appendPlaylistEntryMrss($entry, $mrss);
				break;
				
			case entryType::DATA:
				self::appendDataEntryMrss($entry, $mrss);
				break;
				
			case entryType::LIVE_STREAM:
				self::appendLiveStreamEntryMrss($entry, $mrss);
				break;
				
			default:
				break;
		}
			
		$flavorAssets = flavorAssetPeer::retreiveReadyByEntryId($entry->getId());
		foreach($flavorAssets as $flavorAsset)
		{
			if (!is_null($fitlerByFlovorParams) && $flavorAsset->getFlavorParamsId() != $fitlerByFlovorParams)
				continue;
				 
			$content = $mrss->addChild('content');
			$content->addAttribute('url', self::getAssetUrl($flavorAsset));
			$content->addAttribute('flavorAssetId', $flavorAsset->getId());
			$content->addAttribute('isSource', $flavorAsset->getIsOriginal() ? 'true' : 'false');
			$content->addAttribute('containerFormat', $flavorAsset->getContainerFormat());
			$content->addAttribute('extension', $flavorAsset->getFileExt());
			
			if(!is_null($flavorAsset->getFlavorParamsId()))
			{
				$content->addAttribute('flavorParamsId', $flavorAsset->getFlavorParamsId());
				$flavorParams = flavorParamsPeer::retrieveByPK($flavorAsset->getFlavorParamsId());
				if($flavorParams)
				{
					$content->addAttribute('flavorParamsName', $flavorParams->getName());
					$content->addAttribute('format', $flavorParams->getFormat());
					$content->addAttribute('videoBitrate', $flavorParams->getVideoBitrate());
					$content->addAttribute('videoCodec', $flavorParams->getVideoCodec());
					$content->addAttribute('audioBitrate', $flavorParams->getAudioBitrate());
					$content->addAttribute('audioCodec', $flavorParams->getAudioCodec());
					$content->addAttribute('frameRate', $flavorParams->getFrameRate());
					$content->addAttribute('height', $flavorParams->getHeight());
					$content->addAttribute('width', $flavorParams->getWidth());
				}
			}
				
			$tags = $content->addChild('tags');
			foreach(explode(',', $flavorAsset->getTags()) as $tag)
				$tags->addChild('tag', self::stringToSafeXml($tag));
		}
			
		$thumbAssets = thumbAssetPeer::retreiveReadyByEntryId($entry->getId());
		foreach($thumbAssets as $thumbAsset)
		{
			$thumbnail = $mrss->addChild('thumbnail');
			$thumbnail->addAttribute('url', self::getAssetUrl($thumbAsset));
			$thumbnail->addAttribute('thumbAssetId', $thumbAsset->getId());
			$thumbnail->addAttribute('isDefault', $thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB) ? 'true' : 'false');
			$thumbnail->addAttribute('format', $thumbAsset->getContainerFormat());
			if($thumbAsset->getFlavorParamsId())
				$thumbnail->addAttribute('thumbParamsId', $thumbAsset->getFlavorParamsId());
				
			$tags = $thumbnail->addChild('tags');
			foreach(explode(',', $thumbAsset->getTags()) as $tag)
				$tags->addChild('tag', self::stringToSafeXml($tag));
		}
		
		$mrssContributors = self::getMrssContributors();
		if(count($mrssContributors))
			foreach($mrssContributors as $mrssContributor)
				$mrssContributor->contribute($entry, $mrss);
		
		return $mrss;
	}
}
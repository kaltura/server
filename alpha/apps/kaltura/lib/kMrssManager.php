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
		
	}
	
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 */
	protected static function appendLiveStreamEntryMrss(entry $entry, SimpleXMLElement $mrss)
	{
		
	}

	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected static function getFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$partner = PartnerPeer::retrieveByPK($flavorAsset->getPartnerId());
		if(!$partner)
			return null;
	
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$externalStorageUrl = $this->getExternalStorageUrl($partner, $flavorAsset, $syncKey);
		if($externalStorageUrl)
			return $externalStorageUrl;
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
			return null;
		
		$this->protocol = StorageProfile::PLAY_FORMAT_HTTP;
		$this->cdnHost = myPartnerUtils::getCdnHost($this->syndicationFeed->partnerId, $this->protocol);
		
		$urlManager = kUrlManager::getUrlManagerByCdn($this->cdnHost);
		$urlManager->setDomain($this->cdnHost);
		$url = $urlManager->getFlavorAssetUrl($flavorAsset);
		$url = $this->cdnHost . $url;
		$url = preg_replace('/^https?:\/\//', '', $url);
			
		return $this->protocol . '://' . $url;
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
	 * @return SimpleXMLElement
	 */
	public static function getEntryMrssXml(entry $entry, SimpleXMLElement $mrss = null, $link = null)
	{
		if(!$mrss)
		{
			$mrss = new SimpleXMLElement('<item/>');
		}
		
		$mrss->addChild('title', self::stringToSafeXml($entry->getName()));
		$mrss->addChild('link', $link . $entry->getId());
		$mrss->addChild('type', $entry->getType());
		$mrss->addChild('licenseType', $entry->getLicenseType());
		$mrss->addChild('userId', $entry->getPuserId(true));
		$mrss->addChild('name', self::stringToSafeXml($entry->getName()));
		$mrss->addChild('description', self::stringToSafeXml($entry->getDescription()));
		$mrss->addChild('tags', self::stringToSafeXml($entry->getTags()));
		$mrss->addChild('partnerData', self::stringToSafeXml($entry->getPartnerData()));
		$mrss->addChild('accessControlId', $entry->getAccessControlId());
		
		$categories = explode(',', $entry->getCategories());
		foreach($categories as $category)
			if($category)
				$mrss->addChild('category', self::stringToSafeXml($category));
		
		if($entry->getStartDate(null))
			$mrss->addChild('startDate', $entry->getStartDate());
		
		if($entry->getEndDate(null))
			$mrss->addChild('endDate', $entry->getEndDate());
		
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
			$content = $mrss->addChild('content');
			$content->addAttribute('url', self::getFlavorAssetUrl($flavorAsset));
			$content->addAttribute('flavorAssetId', $flavorAsset->getId());
			$content->addAttribute('isSource', (bool) $flavorAsset->getIsOriginal());
			$content->addChild('tags', $flavorAsset->getTags());
		}
			
		$thumbAssets = thumbAssetPeer::retreiveReadyByEntryId($entry->getId());
		foreach($thumbAssets as $thumbAsset)
		{
			$thumbnail = $mrss->addChild('thumbnail');
			$thumbnail->addAttribute('url', self::getFlavorAssetUrl($thumbAsset));
			$thumbnail->addAttribute('thumbAssetId', $thumbAsset->getId());
			$thumbnail->addAttribute('isDefault', (bool) $thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB));
			$thumbnail->addChild('tags', $thumbAsset->getTags());
		}
		
		$mrssContributors = self::getMrssContributors();
		foreach($mrssContributors as $mrssContributor)
			$mrssContributor->contribute($entry, $mrss);
		
		return $mrss;
	}
}
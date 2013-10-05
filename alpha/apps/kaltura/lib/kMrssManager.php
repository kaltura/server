<?php
class kMrssManager
{
	const FORMAT_DATETIME = 'Y-m-d\TH:i:s';
	
	/**
	 * @var array<IKalturaMrssContributor>
	 */
	private static $mrssContributors = null;
	
	private static $addedIsmUrl = false;
	
	/**
	 * Array of XML objects
	 *
	 * @var array<SimpleXMLElement>
	 */
	protected static $instancesPool = array();
	
	/**
	 * Indicates that the static instances pool caching is enabled
	 *
	 * @var bool
	 */
	protected static $instancesEnabled = true;

	/**
	 * Enables or disables the static instances pool caching
	 *
	 * @param bool $enabled
	 */
	public static function setInstancePoolingEnabled($enabled = true)
	{
		self::$instancesEnabled = $enabled;
	}

	/**
	 * Indicates if the static instances pool caching is enabled
	 *
	 * @return boolean
	 */
	protected static function isInstancePoolingEnabled()
	{
		return self::$instancesEnabled;
	}
	
	/**
	 * Generates instance pool key
	 * @param string $objectId
	 * @param kMrssParameters $mrssParams
	 * @param string $features
	 * 
	 * returns instance key for pool
	 */
	
	protected static function generateInstanceKey($objectId, kMrssParameters $mrssParams = null, $features = null)
	{
		$instanceKey = $objectId;
		
		if(!is_null($mrssParams))
			$instanceKey .= md5(serialize($mrssParams));
			
		if(!is_null($features))
			$instanceKey .= md5(serialize($features));
			
		return $instanceKey;
	}
	
	/**
	 * Adds the supplied XML object to the instance pool.
	 *
	 * @param string $entryId
	 * @param SimpleXMLElement $xml
	 */
	protected static function addInstanceToPool($entryId, SimpleXMLElement $xml)
	{
		if (self::isInstancePoolingEnabled())
		{
			self::$instancesPool[$entryId] = $xml;
			kMemoryManager::registerPeer('kMrssManager');
		}
	}

	/**
	 * Gets XML object from the instance pool according to the supplied entry id.
	 *
	 * @param string $entryId
	 * @return SimpleXMLElement
	 */
	protected static function getInstanceFromPool($entryId)
	{
		if (self::isInstancePoolingEnabled() && isset(self::$instancesPool[$entryId]))
			return self::$instancesPool[$entryId];
		
		return null;
	}

	/**
	 * Removes all XML objects from the instance pool.
	 */
	public static function clearInstancePool()
	{
		self::$instancesPool = array();
	}
	
	/**
	 * @param string $string
	 * @return string
	 */
	public static function stringToSafeXml($string)
	{
		$string = @iconv('utf-8', 'utf-8', $string);
		$safe = kString::xmlEncode($string);
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
		$mrss = new SimpleXMLElement('<rss
			version="2.0"
			xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
			xsi:noNamespaceSchemaLocation="http://' . kConf::get('cdn_host') . '/api_v3/service/schema/action/serve/type/' . SchemaType::SYNDICATION . '"
		/>');
//		$mrss->addAttribute('xmlns:content', 'http://www.w3.org/2001/XMLSchema-instance');
		
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

	private static function getExternalStorageUrl(Partner $partner, asset $asset, FileSyncKey $key, $storageId = null)
	{
		if(!$partner->getStorageServePriority() || $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
			return null;
			
		if(is_null($storageId) && $partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
			if(kFileSyncUtils::getReadyInternalFileSyncForKey($key)) // check if having file sync on kaltura dcs
				return null;
				
		$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $storageId);
		if(!$fileSync)
			return null;
			
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return null;
			
		$urlManager = kUrlManager::getUrlManagerByStorageProfile($fileSync->getDc(), $asset->getEntryId());
		$urlManager->setFileExtension($asset->getFileExt());
		$url = $storage->getDeliveryHttpBaseUrl() . '/' . $urlManager->getFileSyncUrl($fileSync);
		
		return $url;
	}
	
	/**
	 * @param asset $asset
	 * @return string
	 */
	public static function getAssetUrl(asset $asset, $storageId = null)
	{
		$partner = PartnerPeer::retrieveByPK($asset->getPartnerId());
		if(!$partner)
			return null;
	
		$syncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$externalStorageUrl = self::getExternalStorageUrl($partner, $asset, $syncKey, $storageId);
		if($externalStorageUrl)
			return $externalStorageUrl;
			
		if($partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
			return null;
		
		$cdnHost = myPartnerUtils::getCdnHost($asset->getPartnerId());
		
		$urlManager = kUrlManager::getUrlManagerByCdn($cdnHost, $asset->getEntryId());
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
		$mrssParams = new kMrssParameters;
		$mrssParams->setLink($link);
		$mrss = self::getEntryMrssXml($entry, $mrss, $mrssParams);
		return $mrss->asXML();
	}
	
	/**
	 * @param thumbAsset $thumbAsset
	 * @param SimpleXMLElement $mrss
	 * @return SimpleXMLElement
	 */
	protected static function appendThumbAssetMrss(thumbAsset $thumbAsset, SimpleXMLElement $mrss = null)
	{
		if(!$mrss)
			$mrss = new SimpleXMLElement('<item/>');
			
		$thumbnail = $mrss->addChild('thumbnail');
		$thumbnail->addAttribute('url', self::getAssetUrl($thumbAsset));
		$thumbnail->addAttribute('thumbAssetId', $thumbAsset->getId());
		$thumbnail->addAttribute('isDefault', $thumbAsset->hasTag(thumbParams::TAG_DEFAULT_THUMB) ? 'true' : 'false');
		$thumbnail->addAttribute('format', $thumbAsset->getContainerFormat());
		$thumbnail->addAttribute('extension', $thumbAsset->getFileExt());
		$thumbnail->addAttribute('height', $thumbAsset->getHeight());
		$thumbnail->addAttribute('width', $thumbAsset->getWidth());
		$thumbnail->addAttribute('fileSize',$thumbAsset->getSize());
		$thumbnail->addAttribute('createdAt', $thumbAsset->getCreatedAt());
		if($thumbAsset->getFlavorParamsId())
			$thumbnail->addAttribute('thumbParamsId', $thumbAsset->getFlavorParamsId());
			
		$tags = $thumbnail->addChild('tags');
		foreach(explode(',', $thumbAsset->getTags()) as $tag)
			$tags->addChild('tag', self::stringToSafeXml($tag));
	}
	
	/**
	 * @param flavorAsset $flavorAsset
	 * @param SimpleXMLElement $mrss
	 * @return SimpleXMLElement
	 */
	protected static function appendFlavorAssetMrss(flavorAsset $flavorAsset, SimpleXMLElement $mrss = null, kMrssParameters $mrssParams = null)
	{
		if(!$mrss)
			$mrss = new SimpleXMLElement('<item/>');
		
		$content = $mrss->addChild('content');
		$content->addAttribute('url', self::getAssetUrl($flavorAsset, ($mrssParams) ? $mrssParams->getStorageId() : null));
		$content->addAttribute('flavorAssetId', $flavorAsset->getId());
		$content->addAttribute('isSource', $flavorAsset->getIsOriginal() ? 'true' : 'false');
		$content->addAttribute('containerFormat', $flavorAsset->getContainerFormat());
		$content->addAttribute('extension', $flavorAsset->getFileExt());
		$content->addAttribute('createdAt', $flavorAsset->getCreatedAt());

		$mediaParams = array(
			'format' => $flavorAsset->getContainerFormat(),
			'videoBitrate' => $flavorAsset->getBitrate(),
			'fileSize' => $flavorAsset->getSize() * 1024, // mrss spec is in bytes
			'videoCodec' => $flavorAsset->getVideoCodecId(),
			'audioBitrate' => 0,
			'audioCodec' => '',
			'frameRate' => $flavorAsset->getFrameRate(),
			'height' => $flavorAsset->getHeight(),
			'width' => $flavorAsset->getWidth(),
		);
		
		if(!is_null($flavorAsset->getFlavorParamsId()))
		{
			$content->addAttribute('flavorParamsId', $flavorAsset->getFlavorParamsId());
			$flavorParams = assetParamsPeer::retrieveByPK($flavorAsset->getFlavorParamsId());
			if($flavorParams)
			{
				$content->addAttribute('flavorParamsName', $flavorParams->getName());

				$flavorParamsDetails = array(
					'format' => 		$flavorParams->getFormat(),
					'videoBitrate' => 	$flavorParams->getVideoBitrate(),
					'videoCodec' => 	$flavorParams->getVideoCodec(),
					'audioBitrate' => 	$flavorParams->getAudioBitrate(),
					'audioCodec' => 	$flavorParams->getAudioCodec(),
					'frameRate' => 		$flavorParams->getFrameRate(),
					'height' => 		$flavorParams->getHeight(),
					'width' => 			$flavorParams->getWidth(),
				);

				// merge the flavar param details with the flavor asset details
				// the flavor asset details take precedence whenever they exist
				$mediaParams = array_merge($flavorParamsDetails, array_filter($mediaParams));
			}
		}
		
		foreach ($mediaParams as $key => $value)
		{
			$content->addAttribute($key, $value);
		}
			
		$tags = $content->addChild('tags');
		foreach(explode(',', $flavorAsset->getTags()) as $tag)
			$tags->addChild('tag', self::stringToSafeXml($tag));
				
		if ($flavorAsset->hasTag(assetParams::TAG_SLWEB))
			self::addIsmLink($flavorAsset->getentry(), $mrss);
	}
	
	//if the one of the flavors is an .ismv file we will add to the mrss a url of the entry's .ism file.
	private static function addIsmLink (entry $entry ,SimpleXMLElement $mrss ){
		if (self::$addedIsmUrl)
			return;
		self::$addedIsmUrl = true;
		$syncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
		
		$kalturaFileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
	
		$urlPrefix = myPartnerUtils::getIisHost($entry->getPartnerId(), PlaybackProtocol::HTTP);
		$iisHost = parse_url($urlPrefix, PHP_URL_HOST);
		
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $urlPrefix, $matches))
		{
			$urlPrefix = $matches[1];
		}
		$urlPrefix = rtrim($urlPrefix,'/').'/';
		$urlManager = kUrlManager::getUrlManagerByCdn($iisHost, $entry->getId());
		if ($kalturaFileSync)
			$urlManager->setFileExtension(pathinfo($kalturaFileSync->getFilePath(), PATHINFO_EXTENSION));
		$urlManager->setProtocol(PlaybackProtocol::SILVER_LIGHT);
		
		
		$partner = $entry->getPartner();
		if(!$partner->getStorageServePriority() ||
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY ||
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST)
		{
			if($kalturaFileSync)
			{
				$url = $urlManager->getFileSyncUrl($kalturaFileSync, false);
				$mrss->addChild('ismUrl',$urlPrefix.$url);
				return;
			}
		}
		
		if(!$partner->getStorageServePriority() ||
			$partner->getStorageServePriority() == StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY)
		{
			return null;
		}
			
		$externalFileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($syncKey);
		if($externalFileSync)
		{
			$urlManager = kUrlManager::getUrlManagerByStorageProfile($externalFileSync->getDc(), $entry->getId());
			$urlManager->setFileExtension(pathinfo($externalFileSync->getFilePath(), PATHINFO_EXTENSION));
			$urlManager->setProtocol(PlaybackProtocol::SILVER_LIGHT);
			$url = $urlManager->getFileSyncUrl($externalFileSync, false);
			$url = ltrim($url,'/');
        	if (strpos($url, "://") !== false)
         		$urlPrefix = '';
			$mrss->addChild('ismUrl',$urlPrefix.$url);
			return;
		}

		if($partner->getStorageServePriority() != StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY)
		{
			if($kalturaFileSync)
			{
				$url = $urlManager->getFileSyncUrl($kalturaFileSync, false);
				$mrss->addChild('ismUrl',$urlPrefix.$url);
				return;
			}
		}
	}
	
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 * @param kMrssParameters $mrssParams
	 * @params string $features
	 * @return SimpleXMLElement
	 */
	public static function getEntryMrssXml(entry $entry, SimpleXMLElement $mrss = null, kMrssParameters $mrssParams = null, $features = null)
	{
		$instanceKey = self::generateInstanceKey($entry->getId(), $mrssParams, $features);
		
		if(is_null($mrss))
		{
			$mrss = self::getInstanceFromPool($instanceKey);
			if($mrss)
				return $mrss;
				
			$encoding = 'UTF-8';
			if($mrssParams && !is_null($mrssParams->getEncoding()))
				$encoding = $mrssParams->getEncoding();
				
			if($encoding)
				$mrss = new SimpleXMLElement('<?xml version="1.0" encoding="' . $encoding . '"?><item/>');
			else
				$mrss = new SimpleXMLElement('<item/>');
		}
		
		$mrss->addChild('entryId', $entry->getId());
		if($entry->getReferenceID())
			$mrss->addChild('referenceID', self::stringToSafeXml($entry->getReferenceID()));
		$mrss->addChild('createdAt', $entry->getCreatedAt(null));
		$mrss->addChild('updatedAt', $entry->getUpdatedAt(null));
		$mrss->addChild('title', self::stringToSafeXml($entry->getName()));
		if($mrssParams && !is_null($mrssParams->getLink()))
			$mrss->addChild('link', $mrssParams->getLink() . $entry->getId());
		$mrss->addChild('type', $entry->getType());
		$mrss->addChild('licenseType', $entry->getLicenseType());
		$mrss->addChild('userId', $entry->getPuserId());
		$mrss->addChild('name', self::stringToSafeXml($entry->getName()));
		$mrss->addChild('status', self::stringToSafeXml($entry->getStatus()));
		$mrss->addChild('description', self::stringToSafeXml($entry->getDescription()));
		$thumbnailUrl = $mrss->addChild('thumbnailUrl');
		$thumbnailUrl->addAttribute('url', $entry->getThumbnailUrl());
		if(trim($entry->getTags(), " \r\n\t"))
		{
			$tags = $mrss->addChild('tags');
			foreach(explode(',', $entry->getTags()) as $tag)
				$tags->addChild('tag', self::stringToSafeXml($tag));
		}
			
		$categories = explode(',', $entry->getCategories());
		foreach($categories as $category)
		{
			$category = trim($category);
			if($category)
			{
				$categoryNode = $mrss->addChild('category', self::stringToSafeXml($category));
				if(strrpos($category, '>') > 0)
					$categoryNode->addAttribute('name', self::stringToSafeXml(substr($category, strrpos($category, '>') + 1)));
				else
					$categoryNode->addAttribute('name', self::stringToSafeXml($category));
			}
		}
		
		$mrss->addChild('partnerData', self::stringToSafeXml($entry->getPartnerData()));
		if($entry->getAccessControlId())
			$mrss->addChild('accessControlId', $entry->getAccessControlId());
		if($entry->getConversionProfileId())
			$mrss->addChild('conversionProfileId', $entry->getConversionProfileId());
		
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
			
		$assetsStatuses = array(asset::ASSET_STATUS_READY);
		if($mrssParams && $mrssParams->getStatuses())
			$assetsStatuses = $mrssParams->getStatuses();
			
		$assets = assetPeer::retrieveReadyByEntryId($entry->getId(), null, $assetsStatuses);
		foreach($assets as $asset)
		{
			if ($mrssParams &&
				!is_null($mrssParams->getFilterByFlavorParams()) &&
				$asset->getFlavorParamsId() != $mrssParams->getFilterByFlavorParams())
				continue;

			if($asset instanceof flavorAsset)
				self::appendFlavorAssetMrss($asset, $mrss, $mrssParams);
				
			if($asset instanceof thumbAsset)
				self::appendThumbAssetMrss($asset, $mrss);
		}
			
		$mrssContributors = self::getMrssContributors();
		if(count($mrssContributors))
		{
			foreach($mrssContributors as $mrssContributor)
			{
				try
				{
					if (!count($features) || in_array($mrssContributor->getObjectFeatureType(), $features))
						$mrssContributor->contribute($entry, $mrss, $mrssParams);
				}
				catch(kCoreException $ex)
				{
					KalturaLog::err("Unable to add MRSS element for contributor [".get_class($mrssContributor)."] message [".$ex->getMessage()."]");
				}
			}
		}
		
		if ($mrssParams &&
			$mrssParams->getIncludePlayerTag())
		{
			$uiconfId = (!is_null($mrssParams->getPlayerUiconfId()))? '/ui_conf_id/'.$mrssParams->getPlayerUiconfId(): '';
			$playerUrl = kConf::get('apphome_url').
							'/kwidget/wid/_'.$entry->getPartnerId().
							'/entry_id/'.$entry->getId().'/ui_conf' . ($uiconfId ? "/$uiconfId" : '');
	
			$player = $mrss->addChild('player');
			$player->addAttribute('url', $playerUrl);
		}

		if ($mrssParams && $mrssParams->getItemXpathsToExtend())
		{
			self::addExtendingItemsToMrss($mrss, $mrssParams);
		}
		self::addInstanceToPool($instanceKey, $mrss);
		return $mrss;
	}
	
	
	/**
	 * Function returns MRSS XML for the object based on its identifier
	 * @param BaseObject $object
	 * @param string $identifierValue
	 * @param SimpleXMLElement $mrss
	 * @param string $nodeName
	 * @param kMrssParameters $mrssParams
	 * @param string $features
	 * @return SimpleXMLElement
	 */
	protected static function addExtendingItemNode (BaseObject $object, $identifierValue, SimpleXMLElement $mrss, $nodeName = null, kMrssParameters $mrssParams = null, $features = null)
	{
		if ($object instanceof category)
		{
			$categoryItem = $mrss->addChild("category_item");
			$categoryItem->addAttribute('identifier', $identifierValue);
			return self::getCategoryMrssXml($object, $categoryItem , $mrssParams, $features);
		}
		if ($object instanceof entry)
		{
			if (!$nodeName)
			{
				$nodeName = 'entry';
			}
			$newNode = $mrss->addChild("{$nodeName}_item");
			$newNode->addAttribute('identifier', $identifierValue);
			return self::getEntryMrssXml($object, $newNode, $mrssParams, $features);
		}
		
	}
	
	/**
	 * Function calculates and returns the MRSS XML of a category
	 * @param category $category
	 * @param SimpleXMLElement $mrss
	 * @param kMrssParameters $mrssParams
	 * @param string $features
	 * @return SimpleXMLElement
	 */
	public static function getCategoryMrssXml (category $category, SimpleXMLElement $mrss = null, kMrssParameters $mrssParams = null, $features = null)
	{
		$instanceKey = self::generateInstanceKey($category->getId(), $mrssParams, $features);
		
		if(is_null($mrss))
		{
			$mrss = self::getInstanceFromPool($instanceKey);
			if($mrss)
				return $mrss;
				
			$mrss = new SimpleXMLElement('<item/>');
		}
		
		if (!$features || in_array(ObjectFeatureType::METADATA, $features))
		{
			$mrss->addChild("id", $category->getId());
			$mrss->addChild("name", $category->getName());
			$mrss->addChild("referenceId", $category->getReferenceId());
			$mrss->addChild("fullName", $category->getFullName());
		}
		
		$mrssContributors = self::getMrssContributors();
		if(count($mrssContributors))
		{
			foreach($mrssContributors as $mrssContributor)
			{
				/* @var $mrssContributor IKalturaMrssContributor */
				try
				{
					if (!$features || in_array($mrssContributor->getObjectFeatureType(), $features))
						$mrssContributor->contribute($category, $mrss, $mrssParams);
				}
				catch(kCoreException $ex)
				{
					KalturaLog::err("Unable to add MRSS element for contributor [".get_class($mrssContributor)."] message [".$ex->getMessage()."]");
				}
			}
		}
		
		if ($features && in_array(ObjectFeatureType::ANCESTOR_RECURSIVE, $features))
		{
			$ancestorIds = explode(">", $category->getFullIds());
			$ancestorCategories = categoryPeer::retrieveByPKs($ancestorIds);
			array_pop($ancestorCategories);
			//find and delete the ANCESTOR_RECURSIVE from the features array
			for ($i = 0; $i < count($features); $i++)
			{
				if ($features[$i] == ObjectFeatureType::ANCESTOR_RECURSIVE)
					unset($features[$i]);
			}
			//retrieve mrss for each ancestor category
			$parentCategories = $mrss->addChild('parent_categories');
			foreach ($ancestorCategories as $ancestorCategory )
			{
				$ancestorMrss = $parentCategories->addChild('category_item');
				$ancestorMrss = self::getCategoryMrssXml($ancestorCategory, $ancestorMrss, $mrssParams, $features);
			}
		}
		
		if ($mrssParams && $mrssParams->getItemXpathsToExtend())
		{
			self::addExtendingItemsToMrss($mrss, $mrssParams);
		}
		
		self::addInstanceToPool($instanceKey, $mrss);
		return $mrss;
	}
	
	/**
	 * Function goes over finished MRSS XML and adds the required extending items in the appropriate slots
	 * @param SimpleXMLElement $mrss
	 * @param kMrssParameters $mrssParams
	 * @return SimpleXMLElement
	 */
	protected static function addExtendingItemsToMrss (SimpleXMLElement $mrss, kMrssParameters $mrssParams)
	{
		foreach($mrssParams->getItemXpathsToExtend() as $itemXPathToExtend)
		{
			/* @var $itemXPathToExtend KExtendingItemMrssParameter */
			$xmlNodesToExtend = $mrss->xpath($itemXPathToExtend->getXpath()); //metdata/entryIdX   /entry/customMetadata/metadata/entryIdY
			foreach ($xmlNodesToExtend as $xmlNodeToExtend)
			{
				/* @var $xmlNodeToExtend SimpleXMLElement */
				$identifierValue = strval($xmlNodeToExtend[0]);
				$extendingObject = $itemXPathToExtend->getIdentifier()->retrieveByIdentifier($identifierValue);
				if ($extendingObject)
				{
					$mrssParams->setItemXpathsToExtend(array());
					$featuresArray = strlen($itemXPathToExtend->getIdentifier()->getExtendedFeatures()) ? explode(',',$itemXPathToExtend->getIdentifier()->getExtendedFeatures()) : null;
					if ($itemXPathToExtend->getExtensionMode() == MrssExtensionMode::APPEND)
					{
						$parents = $xmlNodeToExtend->xpath("parent::*");
						self::addExtendingItemNode($extendingObject, $identifierValue, $parents[0], $xmlNodeToExtend->getName(), $mrssParams, $featuresArray);
					}
					else
					{
						$xmlNodeToExtend[0] = null;
						self::addExtendingItemNode($extendingObject, $identifierValue, $xmlNodeToExtend, $xmlNodeToExtend->getName(), $mrssParams, $featuresArray);
					}
					
				}
			}
		}
		
		return $mrss;
	}
	
}
<?php
/**
 * @package plugins.doubleClickDistribution
 * @subpackage lib
 */
class DoubleClickFeed
{

	/**
	 * @var DOMDocument
	 */
	protected $doc;
	
	/**
	 * @var DOMXPath
	 */
	protected $xpath;
	
	/**
	 * @var DOMElement
	 */
	protected $item;
	
	/**
	 * @var DOMElement
	 */
	protected $content;
	
	/**
	 * @var DOMElement
	 */
	protected $thumbnail;
	
	/**
	 * @var DOMElement
	 */
	protected $category;
	
	/**
	 * @var DoubleClickDistributionProfile
	 */
	protected $distributionProfile;

	/**
	 * @var bool
	 */
	protected $version_2;

	/**
	 * @var DOMElement
	 */
	protected $caption;

	/**
	 * @param $templateName
	 * @param DoubleClickDistributionProfile $profile
	 * @param $version_2
	 * @param $distributionProfile
	 */
	public function __construct($templateName, DoubleClickDistributionProfile $profile, $version_2)
	{
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml/' . $templateName;
		$this->distributionProfile = $profile;
		$this->version_2 = $version_2;
		
		$this->doc = new KDOMDocument('1.0', 'UTF-8');
		$this->doc->formatOutput = true;
		$this->doc->preserveWhiteSpace = false;
		$this->doc->load($xmlTemplate);
		
		$this->xpath = new DOMXPath($this->doc);
		$this->addDfpMrssNameSpaces($version_2);
		$this->setTemplateNodes();
		$this->setProfileProperties($profile, $version_2);
		$this->setItemsPerPage($profile->getItemsPerPage());
	}

	public function setTemplateNodes()
	{
		// item node template
		$node = $this->xpath->query('/rss/channel/item')->item(0);
		$this->item = $node->cloneNode(true);
		$node->parentNode->removeChild($node);

		if($this->version_2)
		{
			$contentNode = $this->xpath->query('media:content', $this->item)->item(0);
			$thumbnailNode = $this->xpath->query('media:thumbnail', $this->item)->item(0);
			$categoryNode = $this->xpath->query('dfpvideo:keyvalues[@key="category"]', $this->item)->item(0);
			$captionNode = $this->xpath->query('dfpvideo:closedCaptionUrl', $this->item)->item(0);

			// caption node template
			$this->caption = $captionNode->cloneNode(true);
			$captionNode->parentNode->removeChild($captionNode);
		}
		else
		{
			$contentNode = $this->xpath->query('media:group/media:content', $this->item)->item(0);
			$thumbnailNode = $this->xpath->query('media:group/media:thumbnail', $this->item)->item(0);
			$categoryNode  = $this->xpath->query('media:group/media:category', $this->item)->item(0);
		}

		// content node template
		$this->content = $contentNode->cloneNode(true);
		$contentNode->parentNode->removeChild($contentNode);
		// thumbnail node template
		$this->thumbnail = $thumbnailNode->cloneNode(true);
		$thumbnailNode->parentNode->removeChild($thumbnailNode);
		// category node template
		$this->category = $categoryNode->cloneNode(true);
		$categoryNode->parentNode->removeChild($categoryNode);
	}


	/**
	 * @param $version_2
	 */
	public function addDfpMrssNameSpaces($version_2)
	{
		$this->xpath->registerNamespace('media', 'http://search.yahoo.com/mrss/');
		$this->xpath->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
		$this->xpath->registerNamespace('openSearch', 'http://a9.com/-/spec/opensearchrss/1.0/');
		$this->xpath->registerNamespace('dfpvideo', 'http://api.google.com/dfpvideo');
		if($version_2)
			$this->xpath->registerNamespace('version', '2.0');
	}

	/**
	 * @param DoubleClickDistributionProfile $profile
	 * @param $version_2
	 */
	public function setProfileProperties(DoubleClickDistributionProfile $profile, $version_2)
	{
		if($version_2)
		{
			kXml::setNodeValue($this->xpath,'/rss/channel/title', $profile->getChannelTitle());
			kXml::setNodeValue($this->xpath,'/rss/channel/dfpvideo:keyvalues[@key="description"]/@value', $profile->getChannelDescription());
			kXml::setNodeValue($this->xpath,'/rss/channel/dfpvideo:keyvalues[@key="link"]/@value', $profile->getChannelLink());
		}
		else
		{
			kXml::setNodeValue($this->xpath,'/rss/channel/title', $profile->getChannelTitle());
			kXml::setNodeValue($this->xpath,'/rss/channel/description', $profile->getChannelDescription());
			kXml::setNodeValue($this->xpath,'/rss/channel/link', $profile->getChannelLink());
		}
	}
	
	/**
	 * @param array $values
	 * @param array $flavorAssets
	 * @param array $thumbAssets
	 * @param array $cuePoints
	 */
	public function addItem(array $values, array $flavorAssets = null, array $thumbAssets = null, array $cuePoints)
	{
		$item = $this->getItem($values, $flavorAssets, $thumbAssets, $cuePoints);
		$channelNode = $this->xpath->query('/rss/channel', $item)->item(0);
		$channelNode->appendChild($item);
	}

	public function addItemXml($xml)
	{
		$tempDoc = new KDOMDocument('1.0', 'UTF-8');
		$tempDoc->loadXML($xml);

		$importedItem = $this->doc->importNode($tempDoc->firstChild, true);
		$channelNode = $this->xpath->query('/rss/channel')->item(0);
		$channelNode->appendChild($importedItem);
	}

	public function getItemXml(array $values, array $flavorAssets = null, array $thumbAssets = null, array $cuePoints, $captionAssets = null, entry $entry = null)
	{
		$item = $this->getItem($values, $flavorAssets, $thumbAssets, $cuePoints, $captionAssets, $entry);
		return $this->doc->saveXML($item);
	}

	public function getItem(array $values, array $flavorAssets = null, array $thumbAssets = null, array $cuePoints, $captionAssets = null, entry $entry = null)
	{
		$item = $this->item->cloneNode(true);

		kXml::setNodeValue($this->xpath,'pubDate', date('r', $values[DoubleClickDistributionField::PUB_DATE]), $item);
		kXml::setNodeValue($this->xpath,'title', $values[DoubleClickDistributionField::TITLE], $item);
		kXml::setNodeValue($this->xpath,'dfpvideo:contentID', $values[DoubleClickDistributionField::GUID], $item);

		if($this->version_2)
			$this->setUniqueVersion2Elements($values, $item, $entry);
		else
			$this->setUniqueDeprecatedVersionElements($values, $item);

		$this->setCategories($values, $item);
		$this->setCuePoints($item, $cuePoints);
		$this->setDynamicMetadata($item, $values);

		if (is_array($flavorAssets))
			$this->setFlavorAssets($item, $flavorAssets);

		if (is_array($thumbAssets))
			$this->setThumbAssets($item, $thumbAssets);

		if (is_array($captionAssets))
			$this->setCaptionAssets($item, $captionAssets);

		return $item;
	}


	public function addKeyvaluesElement($item, $type, $key, $value)
	{
		$keyvaluesElement = $this->doc->createElement('dfpvideo:keyvalues');
		$keyvaluesElement->setAttribute('type', $type);
		$keyvaluesElement->setAttribute('key', $key);
		$keyvaluesElement->setAttribute('value', $value);
		$item->appendChild($keyvaluesElement);
	}


	public function setUniqueVersion2Elements($values, $item, $entry)
	{
		kXml::setNodeValue($this->xpath, 'dfpvideo:lastModifiedDate', date('r', $values[DoubleClickDistributionField::LAST_MEDIA_MODIFIED_DATE]), $item);
		kXml::setNodeValue($this->xpath, 'dfpvideo:keyvalues[@key="description"]/@value', $values[DoubleClickDistributionField::DESCRIPTION], $item);
		kXml::setNodeValue($this->xpath, 'dfpvideo:keyvalues[@key="link"]/@value', $values[DoubleClickDistributionField::LINK], $item);
		kXml::setNodeValue($this->xpath, 'dfpvideo:keyvalues[@key="author"]/@value', $values[DoubleClickDistributionField::AUTHOR], $item);
		kXml::setNodeValue($this->xpath, 'dfpvideo:keyvalues[@key="keywords"]/@value', $values[DoubleClickDistributionField::KEYWORDS], $item);
		kXml::setNodeValue($this->xpath, 'dfpvideo:lastMediaModifiedDate', date('r', $values[DoubleClickDistributionField::LAST_MEDIA_MODIFIED_DATE]), $item);
		kXml::setNodeValue($this->xpath, 'dfpvideo:status', $values[DoubleClickDistributionField::STATUS], $item);
		kXml::setNodeValue($this->xpath, 'dfpvideo:fw_caid', $values[DoubleClickDistributionField::FW_CAID], $item);

		if($entry)
		{
			$ingestUrl = myEntryUtils::getIngestUrl($entry);
			kXml::setNodeValue($this->xpath, 'dfpvideo:ingestUrl', $ingestUrl, $item);
		}
		$this->setStatsVersion2Elements($values, $item);
	}


	public function setStatsVersion2Elements($values, $item)
	{
		$statsInfo = array(
			'totalViewCount' => $values[DoubleClickDistributionField::TOTAL_VIEW_COUNT],
			'previousDayViewCount' => $values[DoubleClickDistributionField::PREVIOUS_DAY_VIEW_COUNT],
			'previousWeekViewCount'=> $values[DoubleClickDistributionField::PREVIOUS_WEEK_VIEW_COUNT],
			'favoriteCount' => $values[DoubleClickDistributionField::FAVORITE_COUNT],
			'likeCount' => $values[DoubleClickDistributionField::LIKE_COUNT],
			'dislikeCount' => $values[DoubleClickDistributionField::DISLIKE_COUNT]
		);

		foreach ($statsInfo as $key=>$value)
		{
			if ($value)
				$this->addKeyvaluesElement($item, 'int', $key, $value);
		}
	}


	public function setUniqueDeprecatedVersionElements($values, $item)
	{
		kXml::setNodeValue($this->xpath, 'guid', $values[DoubleClickDistributionField::GUID], $item);
		kXml::setNodeValue($this->xpath, 'description', $values[DoubleClickDistributionField::DESCRIPTION], $item);
		kXml::setNodeValue($this->xpath, 'link', $values[DoubleClickDistributionField::LINK], $item);
		kXml::setNodeValue($this->xpath, 'author', $values[DoubleClickDistributionField::AUTHOR], $item);
		kXml::setNodeValue($this->xpath, 'media:title', $values[DoubleClickDistributionField::TITLE], $item);
		kXml::setNodeValue($this->xpath, 'media:description', $values[DoubleClickDistributionField::DESCRIPTION], $item);
		kXml::setNodeValue($this->xpath, 'media:keywords', $values[DoubleClickDistributionField::KEYWORDS], $item);
		kXml::setNodeValue($this->xpath, 'dfpvideo:monetizable', $values[DoubleClickDistributionField::MONETIZABLE], $item);

		$statsNode = $this->xpath->query('dfpvideo:stats', $item)->item(0);
		$this->setOptionalAttribute($statsNode, 'totalViewCount', $values[DoubleClickDistributionField::TOTAL_VIEW_COUNT]);
		$this->setOptionalAttribute($statsNode, 'previousDayViewCount', $values[DoubleClickDistributionField::PREVIOUS_DAY_VIEW_COUNT]);
		$this->setOptionalAttribute($statsNode, 'previousWeekViewCount', $values[DoubleClickDistributionField::PREVIOUS_WEEK_VIEW_COUNT]);
		$this->setOptionalAttribute($statsNode, 'favoriteCount', $values[DoubleClickDistributionField::FAVORITE_COUNT]);
		$this->setOptionalAttribute($statsNode, 'likeCount', $values[DoubleClickDistributionField::LIKE_COUNT]);
		$this->setOptionalAttribute($statsNode, 'dislikeCount', $values[DoubleClickDistributionField::DISLIKE_COUNT]);
	}


	public function setCategories($values, $item)
	{
		$categories = explode(',', $values[DoubleClickDistributionField::CATEGORIES]);
		foreach ($categories as $category) {
			$category = trim($category);
			if (!$category)
				continue;
			$categoryNode = $this->category->cloneNode(true);
			if(!$this->version_2)
			{
				$categoryNode->nodeValue = $category;
				$mediaGroupNode = $item->getElementsByTagName('group')->item(0);
				if ($mediaGroupNode)
					$mediaGroupNode->appendChild($categoryNode);
			}
			else
			{
				kXml::setNodeValue($this->xpath,'@value', $category, $categoryNode);
				$item->appendChild($categoryNode);
			}
		}
	}

	public function getAssetUrl(asset $asset)
	{
		$urlManager = DeliveryProfilePeer::getDeliveryProfile($asset->getEntryId());
		if($asset instanceof flavorAsset)
			$urlManager->initDeliveryDynamicAttributes(null, $asset);
		$url = $urlManager->getFullAssetUrl($asset);
		$url = preg_replace('/^https?:\/\//', '', $url);
		return 'http://' . $url;
	}
	
	public function getXml()
	{
		return $this->doc->saveXML();
	}
	
	public function setDynamicMetadata(DOMElement $item, array $values)
	{
		$fieldConfigArray = $this->distributionProfile->getFieldConfigArray();
		foreach($fieldConfigArray as $fieldConfig)
		{
			/* @var $fieldConfig DistributionFieldConfig */
			if (strpos($fieldConfig->getFieldName(), 'DFP_METADATA_') !== 0)
				continue;
				
			$key = $fieldConfig->getUserFriendlyFieldName();
			if (!$key)
				continue;
				
			$value = isset($values[$fieldConfig->getFieldName()]) ? $values[$fieldConfig->getFieldName()] : '';
			if (!$value)
				continue;
			
			$keyvaluesElement = $this->doc->createElement('dfpvideo:keyvalues');
			$keyvaluesElement->setAttribute('type', 'string');
			$keyvaluesElement->setAttribute('key', $key);
			$keyvaluesElement->setAttribute('value', $value);
			
			$item->appendChild($keyvaluesElement);
		}
	}
	
	/**
	 * @param DOMElement $item
	 * @param array $cuePoints
	 */
	public function setCuePoints(DOMElement $item, array $cuePoints)
	{
		$cuePointsProvider = $this->distributionProfile->getCuePointsProvider();
		$times = array();
		foreach($cuePoints as $cuePoint)
		{
			/* @var $cuePoint AdCuePoint */
			if ($cuePoint->getAdType() != AdType::VIDEO)
				continue;
				
			$tags = explode(',', $cuePoint->getTags()); // KMC saves cue points provider as a tag
			foreach($tags as &$tempTag)
				$tempTag = trim($tempTag);
				
			if ($cuePointsProvider && !in_array($cuePointsProvider, $tags))
				continue;
				
			$times[] = floor($cuePoint->getStartTime() / 1000);
		}
		
		kXml::setNodeValue($this->xpath,'dfpvideo:cuepoints', implode(',', $times), $item);
	}
	
	/**
	 * @param array $flavorAssets
	 */
	public function setFlavorAssets(DOMElement $item, array $flavorAssets)
	{
		$first = true;
		foreach($flavorAssets as $flavorAsset) 
		{
			/* @var $flavorAsset flavorAsset */
			$content = $this->content->cloneNode(true);
			if(!$this->version_2)
			{
				$mediaGroup = $this->xpath->query('media:group', $item)->item(0);
				$mediaGroup->appendChild($content);
			}
			else
				$item->appendChild($content);
			
				
			$url = $this->getAssetUrl($flavorAsset);
			$type = '';
			switch ($flavorAsset->getFileExt())
			{
				// covers most cases
				case 'mp4':
					$type = 'video/mp4';
					break;
				case 'flv':
					$type = 'video/x-flv';
					break;
				// try get using the deprecated  mime_content_type 
				// which should be switched to Fileinfo once we have php5.3
				default:
					// mime type function is not available on our enviroment, we will only cover mp4 & flv mime types
					//$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
					//if(kFileSyncUtils::fileSync_exists($syncKey))
					//{
					//$path = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
					//$type = mime_content_type($path);
					//}
					break;
			} 
			
			kXml::setNodeValue($this->xpath,'@url', $url, $content);
			kXml::setNodeValue($this->xpath,'@duration', (int)$flavorAsset->getentry()->getDuration(), $content);
			if(!$this->version_2)
			{
				kXml::setNodeValue($this->xpath,'@type', $type, $content);
				kXml::setNodeValue($this->xpath,'@fileSize', (int)$flavorAsset->getSize(), $content);
				kXml::setNodeValue($this->xpath,'@width', $flavorAsset->getWidth(), $content);
				kXml::setNodeValue($this->xpath,'@height', $flavorAsset->getHeight(), $content);
				kXml::setNodeValue($this->xpath,'@bitrate', $flavorAsset->getBitrate(), $content);
				kXml::setNodeValue($this->xpath,'@isDefault', ($first) ? 'true' : 'false', $content);
			}

			$first = false;
		}
	}
	
	/**
	 * @param array $flavorAssets
	 */
	public function setThumbAssets(DOMElement $item, array $thumbAssets)
	{
		foreach($thumbAssets as $thumbAsset) 
		{
			/* @var $flavorAsset flavorAsset */
			$content = $this->thumbnail->cloneNode(true);
			if(!$this->version_2)
			{
				$mediaGroup = $this->xpath->query('media:group', $item)->item(0);
				$mediaGroup->appendChild($content);
			}
			else
				$item->appendChild($content);

			$url = $this->getAssetUrl($thumbAsset);

			kXml::setNodeValue($this->xpath,'@url', $url, $content);
			kXml::setNodeValue($this->xpath,'@width', $thumbAsset->getWidth(), $content);
			kXml::setNodeValue($this->xpath,'@height', $thumbAsset->getHeight(), $content);
		}
	}


	public function setCaptionAssets($item, $captionAssets)
	{
		foreach($captionAssets as $captionAsset)
		{
			/* @var $captionAsset captionAsset */
			$type = '';
			if($captionAsset->getFileExt() == 'xml')
				$type = 'application/ttaf+xml';
			elseif($captionAsset->getFileExt() == 'vtt')
				$type = 'text/vtt';
			else
				continue;

			$content = $this->caption->cloneNode(true);
			$item->appendChild($content);
			$url = $this->getAssetUrl($captionAsset);

			$content->nodeValue = $url;
			kXml::setNodeValue($this->xpath,'@type', $type, $content);
			kXml::setNodeValue($this->xpath,'@language', $captionAsset->getLanguage(), $content);
		}
	}
	
	public function setTotalResult($v)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/openSearch:totalResults', $v);
	}
	
	public function setStartIndex($v)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/openSearch:startIndex', $v);
	}
	
	public function setItemsPerPage($v)
	{
		kXml::setNodeValue($this->xpath,'/rss/channel/openSearch:itemsPerPage', $v);
	}
	
	public function setSelfLink($href)
	{
		$linkElement = $this->doc->createElement('atom:link');
		$linkElement->setAttribute('rel', 'self');
		$linkElement->setAttribute('href', $href);
		$channelNode = $this->xpath->query('/rss/channel')->item(0);
		$channelNode->appendChild($linkElement);
	}
	
	public function setNextLink($href)
	{
		$linkElement = $this->doc->createElement('atom:link');
		$linkElement->setAttribute('rel', 'next');
		$linkElement->setAttribute('href', $href);
		$channelNode = $this->xpath->query('/rss/channel')->item(0);
		$channelNode->appendChild($linkElement);
	}
	
	/**
	 * @param string $xpath
	 * @param string $value
	 */
	public function setNodeValue($xpath, $value, DOMNode $contextnode = null)
	{
		if ($contextnode)
			$node = $this->xpath->query($xpath, $contextnode)->item(0);
		else 
			$node = $this->xpath->query($xpath)->item(0);
		if (!is_null($node))
		{
			// if CDATA inside, set the value of CDATA
			if ($node->childNodes->length > 0 && $node->childNodes->item(0)->nodeType == XML_CDATA_SECTION_NODE)
				$node->childNodes->item(0)->nodeValue = $value;
			else
			{
				$textNode = $this->doc->createTextNode($value);
				$node->appendChild($textNode);
			}

		}
	}
	
	/**
	 * @param string $xpath
	 * @param string $value
	 */
	public function getNodeValue($xpath)
	{
		$node = $this->xpath->query($xpath)->item(0);
		if (!is_null($node))
			return $node->nodeValue;
		else
			return null;
	}
	
	public function setOptionalAttribute(DOMElement $element, $attribute, $value)
	{
		if ($value)
			$element->setAttribute($attribute, $value);
	}

}
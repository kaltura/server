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
	 * @param $templateName
	 * @param DoubleClickDistributionProfile $profile
	 * @param $distributionProfile
	 */
	public function __construct($templateName, DoubleClickDistributionProfile $profile)
	{
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml/' . $templateName;
		$this->distributionProfile = $profile;
		
		$this->doc = new KDOMDocument('1.0', 'UTF-8');
		$this->doc->formatOutput = true;
		$this->doc->preserveWhiteSpace = false;
		$this->doc->load($xmlTemplate);
		
		$this->xpath = new DOMXPath($this->doc);
		$this->xpath->registerNamespace('media', 'http://search.yahoo.com/mrss/');
		$this->xpath->registerNamespace('atom', 'http://www.w3.org/2005/Atom');
		$this->xpath->registerNamespace('openSearch', 'http://a9.com/-/spec/opensearchrss/1.0/');
		$this->xpath->registerNamespace('dfpvideo', 'http://api.google.com/dfpvideo');
		
		// item node template
		$node = $this->xpath->query('/rss/channel/item')->item(0);
		$this->item = $node->cloneNode(true);
		$node->parentNode->removeChild($node);

		// content node template
		$node = $this->xpath->query('media:group/media:content', $this->item)->item(0);
		$this->content = $node->cloneNode(true);
		$node->parentNode->removeChild($node);
		
		// thumbnail node template
		$node = $this->xpath->query('media:group/media:thumbnail', $this->item)->item(0);
		$this->thumbnail = $node->cloneNode(true);
		$node->parentNode->removeChild($node);
		
		// category node template
		$node = $this->xpath->query('media:group/media:category', $this->item)->item(0);
		$this->category = $node->cloneNode(true);
		$node->parentNode->removeChild($node);
		
		// set profile properties
		kXml::setNodeValue($this->xpath,'/rss/channel/title', $profile->getChannelTitle());
		kXml::setNodeValue($this->xpath,'/rss/channel/description', $profile->getChannelDescription());
		kXml::setNodeValue($this->xpath,'/rss/channel/link', $profile->getChannelLink());
		
		$this->setItemsPerPage($profile->getItemsPerPage());
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

	public function getItemXml(array $values, array $flavorAssets = null, array $thumbAssets = null, array $cuePoints)
	{
		$item = $this->getItem($values, $flavorAssets, $thumbAssets, $cuePoints);
		return $this->doc->saveXML($item);
	}

	public function getItem(array $values, array $flavorAssets = null, array $thumbAssets = null, array $cuePoints)
	{
		$item = $this->item->cloneNode(true);

		kXml::setNodeValue($this->xpath,'guid', $values[DoubleClickDistributionField::GUID], $item);
		kXml::setNodeValue($this->xpath,'pubDate', date('r', $values[DoubleClickDistributionField::PUB_DATE]), $item);
		kXml::setNodeValue($this->xpath,'title', $values[DoubleClickDistributionField::TITLE], $item);
		kXml::setNodeValue($this->xpath,'description', $values[DoubleClickDistributionField::DESCRIPTION], $item);
		kXml::setNodeValue($this->xpath,'link', $values[DoubleClickDistributionField::LINK], $item);
		kXml::setNodeValue($this->xpath,'author', $values[DoubleClickDistributionField::AUTHOR], $item);
		kXml::setNodeValue($this->xpath,'media:title', $values[DoubleClickDistributionField::TITLE], $item);
		kXml::setNodeValue($this->xpath,'media:description', $values[DoubleClickDistributionField::DESCRIPTION], $item);
		kXml::setNodeValue($this->xpath,'media:keywords', $values[DoubleClickDistributionField::KEYWORDS], $item);

		$categories = explode(',', $values[DoubleClickDistributionField::CATEGORIES]);
		foreach($categories as $category)
		{
			$category = trim($category);
			if (!$category)
				continue;
			$categoryNode = $this->category->cloneNode(true);
			$categoryNode->nodeValue = $category;
			$mediaGroupNode = $item->getElementsByTagName('group')->item(0);
			if ($mediaGroupNode)
				$mediaGroupNode->appendChild($categoryNode);
		}

		kXml::setNodeValue($this->xpath,'dfpvideo:contentID', $values[DoubleClickDistributionField::GUID], $item);
		kXml::setNodeValue($this->xpath,'dfpvideo:monetizable', $values[DoubleClickDistributionField::MONETIZABLE], $item);

		$statsNode = $this->xpath->query('dfpvideo:stats', $item)->item(0);
		$this->setOptionalAttribute($statsNode, 'totalViewCount', $values[DoubleClickDistributionField::TOTAL_VIEW_COUNT]);
		$this->setOptionalAttribute($statsNode, 'previousDayViewCount', $values[DoubleClickDistributionField::PREVIOUS_DAY_VIEW_COUNT]);
		$this->setOptionalAttribute($statsNode, 'previousWeekViewCount', $values[DoubleClickDistributionField::PREVIOUS_WEEK_VIEW_COUNT]);
		$this->setOptionalAttribute($statsNode, 'favoriteCount', $values[DoubleClickDistributionField::FAVORITE_COUNT]);
		$this->setOptionalAttribute($statsNode, 'likeCount', $values[DoubleClickDistributionField::LIKE_COUNT]);
		$this->setOptionalAttribute($statsNode, 'dislikeCount', $values[DoubleClickDistributionField::DISLIKE_COUNT]);

		$this->setCuePoints($item, $cuePoints);
		$this->setDynamicMetadata($item, $values);

		if (is_array($flavorAssets))
			$this->setFlavorAssets($item, $flavorAssets);

		if (is_array($thumbAssets))
			$this->setThumbAssets($item, $thumbAssets);

		return $item;
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
			$mediaGroup = $this->xpath->query('media:group', $item)->item(0);
			$mediaGroup->appendChild($content);
				
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

			$entry = $flavorAsset->getentry();
			$duration = $flavorAsset->getentry()->getDuration();

			if($entry->getSequenceEntryIds())
				$duration += myEntryUtils::getSequenceTotalDuration($entry->getId());

			kXml::setNodeValue($this->xpath,'@url', $url, $content);
			kXml::setNodeValue($this->xpath,'@type', $type, $content);
			kXml::setNodeValue($this->xpath,'@fileSize', (int)$flavorAsset->getSize(), $content);
			kXml::setNodeValue($this->xpath,'@duration', (int)$duration, $content);
			kXml::setNodeValue($this->xpath,'@width', $flavorAsset->getWidth(), $content);
			kXml::setNodeValue($this->xpath,'@height', $flavorAsset->getHeight(), $content);
			kXml::setNodeValue($this->xpath,'@bitrate', $flavorAsset->getBitrate(), $content);
			kXml::setNodeValue($this->xpath,'@isDefault', ($first) ? 'true' : 'false', $content);
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
			$mediaGroup = $this->xpath->query('media:group', $item)->item(0);
			$mediaGroup->appendChild($content);
			$url = $this->getAssetUrl($thumbAsset);
			
			kXml::setNodeValue($this->xpath,'@url', $url, $content);
			kXml::setNodeValue($this->xpath,'@width', $thumbAsset->getWidth(), $content);
			kXml::setNodeValue($this->xpath,'@height', $thumbAsset->getHeight(), $content);
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
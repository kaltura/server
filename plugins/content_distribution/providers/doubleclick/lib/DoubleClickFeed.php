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
		
		$this->doc = new DOMDocument();
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
		$this->setNodeValue('/rss/channel/title', $profile->getChannelTitle());
		$this->setNodeValue('/rss/channel/description', $profile->getChannelDescription());
		$this->setNodeValue('/rss/channel/link', $profile->getChannelLink());
		
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
		$item = $this->item->cloneNode(true);
		$channelNode = $this->xpath->query('/rss/channel', $item)->item(0);
		$channelNode->appendChild($item);
		
		$this->setNodeValue('guid', $values[DoubleClickDistributionField::GUID], $item);
		$this->setNodeValue('pubDate', date('r', $values[DoubleClickDistributionField::PUB_DATE]), $item);
		$this->setNodeValue('title', $values[DoubleClickDistributionField::TITLE], $item);
		$this->setNodeValue('description', $values[DoubleClickDistributionField::DESCRIPTION], $item);
		$this->setNodeValue('link', $values[DoubleClickDistributionField::LINK], $item);
		$this->setNodeValue('author', $values[DoubleClickDistributionField::AUTHOR], $item);
		$this->setNodeValue('media:title', $values[DoubleClickDistributionField::TITLE], $item);
		$this->setNodeValue('media:description', $values[DoubleClickDistributionField::DESCRIPTION], $item);
		$this->setNodeValue('media:keywords', $values[DoubleClickDistributionField::KEYWORDS], $item);
		
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

		$this->setNodeValue('dfpvideo:contentID', $values[DoubleClickDistributionField::GUID], $item);
		$this->setNodeValue('dfpvideo:monetizable', $values[DoubleClickDistributionField::MONETIZABLE], $item);
		
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
	}
	
	public function getAssetUrl(asset $asset)
	{
		$cdnHost = myPartnerUtils::getCdnHost($asset->getPartnerId());
		
		$urlManager = kUrlManager::getUrlManagerByCdn($cdnHost);
		$urlManager->setDomain($cdnHost);
		$url = $urlManager->getAssetUrl($asset);
		$url = $cdnHost . $url;
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
				
			$tags = explode(',', $cuePoint->getTags()); // KMC saved cue points provider as a tag
			foreach($tags as &$tempTag)
				$tempTag = trim($tempTag);
				
			if ($cuePointsProvider && !in_array($cuePointsProvider, $tags))
				continue;
				
			$times[] = floor($cuePoint->getStartTime() / 1000);
		}
		
		$this->setNodeValue('dfpvideo:cuepoints', implode(',', $times), $item);
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
			$type = $this->getContentTypeFromUrl($url);
			
			$this->setNodeValue('@url', $url, $content);
			$this->setNodeValue('@type', $type, $content);
			$this->setNodeValue('@fileSize', (int)$flavorAsset->getSize(), $content);
			$this->setNodeValue('@duration', (int)$flavorAsset->getentry()->getDuration(), $content);
			$this->setNodeValue('@width', $flavorAsset->getWidth(), $content);
			$this->setNodeValue('@height', $flavorAsset->getHeight(), $content);
			$this->setNodeValue('@bitrate', $flavorAsset->getBitrate(), $content);
			$this->setNodeValue('@isDefault', ($first) ? 'true' : 'false', $content);
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
			
			$this->setNodeValue('@url', $url, $content);
			$this->setNodeValue('@width', $thumbAsset->getWidth(), $content);
			$this->setNodeValue('@height', $thumbAsset->getHeight(), $content);
		}
	}
	
	public function setTotalResult($v)
	{
		$this->setNodeValue('/rss/channel/openSearch:totalResults', $v);
	}
	
	public function setStartIndex($v)
	{
		$this->setNodeValue('/rss/channel/openSearch:startIndex', $v);
	}
	
	public function setItemsPerPage($v)
	{
		$this->setNodeValue('/rss/channel/openSearch:itemsPerPage', $v);
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
				$node->childNodes->item(0)->nodeValue = htmlentities($value);
			else
				$node->nodeValue = htmlentities($value);
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
	
		
	protected function getContentTypeFromUrl($url)
	{
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_HEADER, true);
		curl_setopt($this->ch, CURLOPT_NOBODY, true);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		$headers = curl_exec($this->ch);
		if (preg_match('/Content-Type: (.*)/', $headers, $matched))
		{
			return trim($matched[1]);
		}
		else
		{
			KalturaLog::alert('"Content-Type" header was not found for the following URL: '. $url);
			return null;
		}
	}
}
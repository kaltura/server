<?php
/**
 * @package plugins.comcastMrssDistribution
 * @subpackage lib
 */
class ComcastMrssFeed
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
	 * @var ComcastMrssDistributionProfile
	 */
	protected $distributionProfile;
	
	/**
	 * @param $templateName
	 * @param $distributionProfile
	 */
	public function __construct($templateName)
	{
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml/' . $templateName;
		$this->doc = new KDOMDocument();
		$this->doc->formatOutput = true;
		$this->doc->preserveWhiteSpace = false;
		$this->doc->load($xmlTemplate);
		
		
		$this->xpath = new DOMXPath($this->doc);
		$this->xpath->registerNamespace('media', 'http://search.yahoo.com/mrss/');
		$this->xpath->registerNamespace('dcterms', 'http://purl.org/dc/terms/');
		$this->xpath->registerNamespace('cim', 'http://labs.comcast.net/cim_mrss/');
		
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
				$node->childNodes->item(0)->nodeValue = htmlspecialchars($value,ENT_QUOTES,'UTF-8');
			else
				$node->nodeValue = htmlspecialchars($value,ENT_QUOTES,'UTF-8');
		}
	}
	
	/**
	 * @param string $xpath
	 * @param DOMNode $contextnode
	 */
	public function removeNode($xpath, DOMNode $contextnode = null)
	{
		if ($contextnode)
			$node = $this->xpath->query($xpath, $contextnode)->item(0);
		else 
			$node = $this->xpath->query($xpath)->item(0);
			
		if (!is_null($node))
			$node->parentNode->removeChild($node);
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
	
	/**
	 * @param ComcastMrssDistributionProfile $profile
	 */
	public function setDistributionProfile(ComcastMrssDistributionProfile $profile)
	{
		$this->distributionProfile = $profile;
		
		kXml::setNodeValue($this->xpath,'/rss/channel/title', $profile->getFeedTitle());
		if ($profile->getFeedLink())
			kXml::setNodeValue($this->xpath,'/rss/channel/link', $profile->getFeedLink());
		else
			$this->removeNode('/rss/channel/link');
		kXml::setNodeValue($this->xpath,'/rss/channel/description', $profile->getFeedDescription());
		kXml::setNodeValue($this->xpath,'/rss/channel/lastBuildDate', $profile->getFeedLastBuildDate());
	}
	
	
	public function addItemXml($xml)
	{
		$tempDoc = new DOMDocument('1.0', 'UTF-8');
		$tempDoc->loadXML($xml);

		$importedItem = $this->doc->importNode($tempDoc->firstChild, true);
		$channelNode = $this->xpath->query('/rss/channel')->item(0);
		$channelNode->appendChild($importedItem);
	}

	public function getItemXml(array $values, array $flavorAssets = null, array $thumbAssets = null)
	{
		$item = $this->getItem($values, $flavorAssets, $thumbAssets);
		return $this->doc->saveXML($item);
	}
	
	public function addItem(array $values, array $flavorAssets = null, array $thumbAssets = null)
	{
		$item = $this->getItem($values, $flavorAssets, $thumbAssets);
		$channelNode = $this->xpath->query('/rss/channel', $item)->item(0);
		$channelNode->appendChild($item);
	}
	
	/**
	 * @param array $values
	 * @param array $flavorAssets
	 * @param array $thumbAssets
	 */
	public function getItem(array $values, array $flavorAssets = null, array $thumbAssets = null)
	{
		$item = $this->item->cloneNode(true);
		
		kXml::setNodeValue($this->xpath,'title', $values[ComcastMrssDistributionField::TITLE], $item);
		kXml::setNodeValue($this->xpath,'description', $values[ComcastMrssDistributionField::DESCRIPTION], $item);
		if ($values[ComcastMrssDistributionField::LINK])
			kXml::setNodeValue($this->xpath,'link', $values[ComcastMrssDistributionField::LINK], $item);
		else
			$this->removeNode('link', $item);
		kXml::setNodeValue($this->xpath,'pubDate', $this->formatComcastDate($values[ComcastMrssDistributionField::PUB_DATE]), $item);
		kXml::setNodeValue($this->xpath,'lastBuildDate', $this->formatComcastDate($values[ComcastMrssDistributionField::LAST_BUILD_DATE]), $item);
		kXml::setNodeValue($this->xpath,'guid', $values[ComcastMrssDistributionField::GUID_ID], $item);
		kXml::setNodeValue($this->xpath,'media:group/media:rating', $values[ComcastMrssDistributionField::MEDIA_RATING], $item);
		kXml::setNodeValue($this->xpath,'media:group/media:keywords', $values[ComcastMrssDistributionField::MEDIA_KEYWORDS], $item);
		kXml::setNodeValue($this->xpath,'cim:link', $values[ComcastMrssDistributionField::COMCAST_LINK], $item);
		kXml::setNodeValue($this->xpath,'cim:brand', $values[ComcastMrssDistributionField::COMCAST_BRAND], $item);
		kXml::setNodeValue($this->xpath,'cim:videoContentType', $values[ComcastMrssDistributionField::COMCAST_VIDEO_CONTENT_TYPE], $item);
		
		/*
		$categories = explode(',', $values[ComcastMrssDistributionField::MEDIA_CATEGORIES]);
		foreach($categories as $category)
		{
			$categoryNode = $this->category->cloneNode(true);
			$categoryNode->nodeValue = $category;
			$item->appendChild($categoryNode);
		}
		*/
		$categoryNode = $this->category->cloneNode(true);
		$categoryNode->nodeValue = $values[ComcastMrssDistributionField::MEDIA_CATEGORY];
		$item->appendChild($categoryNode);
		
		kXml::setNodeValue($this->xpath,'cim:tvSeries/@name', $values[ComcastMrssDistributionField::COMCAST_TV_SERIES], $item);
		kXml::setNodeValue($this->xpath,'cim:tvSeries/@id', $this->getTvSeriesId($values[ComcastMrssDistributionField::COMCAST_TV_SERIES]), $item);
		
		$startTime = date('c', $values[ComcastMrssDistributionField::START_TIME]);
		$endTime = date('c', $values[ComcastMrssDistributionField::END_TIME]);
		$dcTerms = "start=$startTime; end=$endTime; scheme=W3C-DTF";
		kXml::setNodeValue($this->xpath,'dcterms:valid', $dcTerms, $item);

		if (is_array($flavorAssets))
			$this->setFlavorAssets($item, $flavorAssets);
			
		if (is_array($thumbAssets))
			$this->setThumbAssets($item, $thumbAssets);
			
		return $item;
	}
	
	public function getAssetUrl(asset $asset)
	{
		$cdnHost = myPartnerUtils::getCdnHost($asset->getPartnerId());
		
		$urlManager = kUrlManager::getUrlManagerByCdn($cdnHost, $asset->getEntryId());
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
	
	/**
	 * @param array $flavorAssets
	 */
	public function setFlavorAssets(DOMElement $item, array $flavorAssets)
	{
		foreach($flavorAssets as $flavorAsset) 
		{
			/* @var $flavorAsset flavorAsset */
			$content = $this->content->cloneNode(true);
			$mediaGroup = $this->xpath->query('media:group', $item)->item(0);
			$mediaGroup->appendChild($content);
			$url = $this->getAssetUrl($flavorAsset);
			//replace the default file name (a) with the entryId
			$url = preg_replace('/\/a\./','/'.$flavorAsset->getEntryId().'.',$url); 
			
			// we don't have a way to identify the mime type of the file
			// as there is no guarantee that the file exists in the current data center
			// so we will just use those hardcoded conditions
			switch($flavorAsset->getFileExt())
			{
				case 'flv':
					$mimeType = 'video/x-flv';
					break;
				case 'mp4':
					$mimeType = 'video/mp4';
					break;
				case 'mpeg':
				case 'mpg':
					$mimeType = 'video/mpeg';
					break;
				default:
					$mimeType = '';
			}
			
			kXml::setNodeValue($this->xpath,'@url', $url, $content);
			kXml::setNodeValue($this->xpath,'@type', $mimeType, $content);
			kXml::setNodeValue($this->xpath,'@fileSize', (int)$flavorAsset->getSize(), $content);
			kXml::setNodeValue($this->xpath,'@duration', (int)$flavorAsset->getentry()->getDuration(), $content);
			kXml::setNodeValue($this->xpath,'@width', $flavorAsset->getWidth(), $content);
			kXml::setNodeValue($this->xpath,'@height', $flavorAsset->getHeight(), $content);
			kXml::setNodeValue($this->xpath,'@bitrate', $flavorAsset->getBitrate(), $content);
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
	
	/**
	 * comcast used Z for UTC timezone in their example (2008-04-11T12:30:00Z)
	 * @param int $time
	 */
	protected function formatComcastDate($time)
	{
		$date = new DateTime('@'.$time, new DateTimeZone('UTC'));
		return str_replace('+0000', 'Z', $date->format(DateTime::ISO8601)); 
	}
	
	protected function getTvSeriesId($name)
	{
		$mappingArray = $this->distributionProfile->getCPlatformTvSeries();
		foreach($mappingArray as $id => $val)
		{
			if ($val == $name)
				return (int)$id;
		}
		return -1;
	}
}
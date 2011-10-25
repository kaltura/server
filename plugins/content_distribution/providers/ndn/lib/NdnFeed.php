<?php
/**
 * @package plugins.ndnDistribution
 * @subpackage lib
 */
class NdnFeed
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
	protected $thumbnail;
	
	/**
	 * @var DOMElement
	 */
	protected $category;
	
	/**
	 * @var NdnDistributionProfile
	 */
	protected $distributionProfile;
	
	/**
	 * @param $templateName
	 * @param $distributionProfile
	 */
	public function __construct($templateName)
	{
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml/' . $templateName;
		$this->doc = new DOMDocument();
		$this->doc->formatOutput = true;
		$this->doc->preserveWhiteSpace = false;
		$this->doc->load($xmlTemplate);
		
		//namespaces
		$this->xpath = new DOMXPath($this->doc);
		$this->xpath->registerNamespace('live', 'http://live.com/schema/media/');
		$this->xpath->registerNamespace('media', 'http://search.yahoo.com/mrss/');
		$this->xpath->registerNamespace('abcnews', 'http://abcnews.com/content/');
				
		// item node template
		$node = $this->xpath->query('/rss/channel/item')->item(0);
		$this->item = $node->cloneNode(true);
		$node->parentNode->removeChild($node);

		// category node template
		$node = $this->xpath->query('media:category', $this->item)->item(0);
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
	
	/**
	 * @param NdnDistributionProfile $profile
	 */
	public function setDistributionProfile(NdnDistributionProfile $profile)
	{
		$this->distributionProfile = $profile;
	}
	
	public function setChannelFields ()
	{
		$this->setNodeValue('/rss/channel/title', $this->distributionProfile->getChannelTitle());
		$this->setNodeValue('/rss/channel/link', $this->distributionProfile->getChannelLink());
		$this->setNodeValue('/rss/channel/description', $this->distributionProfile->getChannelDescription());
		$this->setNodeValue('/rss/channel/language', $this->distributionProfile->getChannelLanguage());
		$this->setNodeValue('/rss/channel/copyright', $this->distributionProfile->getChannelCopyright());
		$this->setNodeValue('/rss/channel/image/title', $this->distributionProfile->getChannelImageTitle());
		$this->setNodeValue('/rss/channel/image/url', $this->distributionProfile->getChannelImageUrl());
		$this->setNodeValue('/rss/channel/image/link', $this->distributionProfile->getChannelImageLink());				
		$this->setNodeValue('/rss/channel/pubDate', date('r',$this->distributionProfile->getCreatedAt(null)));
		
	}
	
	public function setChannelLastBuildDate($lastBuildDate)
	{
		$this->setNodeValue('/rss/channel/lastBuildDate', date('r', $lastBuildDate));
	}
	
	/**
	 * @param array $values
	 * @param array $flavorAssets
	 * @param array $thumbAssets
	 */
	public function addItem(array $values, array $flavorAssets = null, array $thumbAssets = null, $entry)
	{		
		$item = $this->item->cloneNode(true);
		$channelNode = $this->xpath->query('/rss/channel', $item)->item(0);
		$channelNode->appendChild($item);		
		//if no thumbnail exists take the default one
		$thumbnailUrl = '';
		if (!$thumbAssets)
		{
			$thumbnailUrl = $entry->getThumbnailUrl();
		}
		$mediaType = $this->getMediaTypeString($entry->getMediaType());	
		$this->setNodeValue('guid', $values[NdnDistributionField::ITEM_GUID], $item);
		$this->setNodeValue('title', $values[NdnDistributionField::ITEM_TITLE], $item);
		$this->setNodeValue('link', $values[NdnDistributionField::ITEM_LINK], $item);
		$this->setNodeValue('description', $values[NdnDistributionField::ITEM_DESCRIPTION], $item);
		$pubDate = date('r', $values[NdnDistributionField::ITEM_PUB_DATE]);
		$this->setNodeValue('pubDate', $pubDate, $item);
		$endTime = date('r', $values[NdnDistributionField::ITEM_EXPIRATION_DATE]);
		$this->setNodeValue('expirationDate', $endTime, $item);
		$origReleaseDate = date('r', $values[NdnDistributionField::ITEM_LIVE_ORIGINAL_RELEASE_DATE]);
		$this->setNodeValue('live:origReleaseDate',$origReleaseDate, $item);
		$this->setNodeValue('media:title', $values[NdnDistributionField::ITEM_MEDIA_TITLE], $item);
		$this->setNodeValue('media:description', $values[NdnDistributionField::ITEM_MEDIA_DESCRIPTION], $item);
		$this->setNodeValue('media:keywords', $values[NdnDistributionField::ITEM_MEDIA_KEYWORDS], $item);
		$this->setNodeValue('media:rating', $values[NdnDistributionField::ITEM_MEDIA_RATING], $item);		
		if (!is_null($flavorAssets) && is_array($flavorAssets) && count($flavorAssets)>0)
		{
			$this->setFlavorAsset($item, $flavorAssets, $mediaType, $values[NdnDistributionField::ITEM_CONTENT_LANG]);
		}			
		if (!is_null($thumbAssets) && is_array($thumbAssets) && count($thumbAssets)>0)
		{
			$this->setThumbAsset($item, $thumbAssets, $values[NdnDistributionField::ITEM_THUMBNAIL_CREDIT]);			
		}
		else
		{
			$this->setThumbAssetUrl($item, $thumbnailUrl, $values[NdnDistributionField::ITEM_THUMBNAIL_CREDIT]);
		}		
		$this->addCategory($item,$values[NdnDistributionField::ITEM_MEDIA_CATEGORY]);
		$this->setNodeValue('media:copyright', $values[NdnDistributionField::ITEM_MEDIA_COPYRIGHT], $item);	
		$this->setNodeValue('media:copyright/@url', $values[NdnDistributionField::ITEM_MEDIA_COPYRIGHT_URL], $item);			
	}
	
	private function getMediaTypeString($mediaType)
	{
		$mediaTypeString=null;
		switch($mediaType){
			case(1):
				$mediaTypeString = 'video';
				break;			
			case(5):
				$mediaTypeString = 'audio';
				break;		
			default:
				break;		
		}
		return $mediaTypeString;
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
	
	public function addCategory($item, $categoryValue)
	{	
		$categories = explode(',', $categoryValue);
		if ($categories)
		{			
			foreach ($categories as $category)
			{				
				if ($category){	
					$categoryNode = $this->category->cloneNode(true);				
					$categoryNode->nodeValue = $category;	
					$beforeNode = $this->xpath->query('media:copyright', $item)->item(0);		
					$item->insertBefore($categoryNode, $beforeNode);
				}
			}
		}	
	}
	
	public function setFlavorAsset(DOMElement $item, array $flavorAssets, $mediaType, $flavorLang)
	{
		$flavorAsset = $flavorAssets[0];		
		/* @var $flavorAsset flavorAsset */
		$url = $this->getAssetUrl($flavorAsset);
		$this->setNodeValue('media:content/@url', $url, $item);
		$this->setNodeValue('media:content/@width', $flavorAsset->getWidth(), $item);
		$this->setNodeValue('media:content/@height', $flavorAsset->getHeight(), $item);
		$this->setNodeValue('media:content/@type', $mediaType, $item);
		if (!empty($flavorLang))
		{
			$this->setNodeValue('media:content/@lang', $flavorLang, $item);
		}
	}
	
	public function setThumbAsset(DOMElement $item, array $thumbAssets, $thumbnailCredit)
	{
		/** @var $thumbAsset thumbAsset */ 
		$thumbAsset = $thumbAssets[0];
		$url = $this->getAssetUrl($thumbAsset);
		$this->setNodeValue('media:thumbnail/@url', $url, $item);
		$this->setNodeValue('media:thumbnail/@width', $thumbAsset->getWidth(), $item);
		$this->setNodeValue('media:thumbnail/@height', $thumbAsset->getHeight(), $item);
		if (!empty($thumbnailCredit))
		{
			$this->setNodeValue('media:thumbnail/@credit', $thumbnailCredit, $item);
		}
	}
	
	public function setThumbAssetUrl($item, $thumbnailUrl, $thumbnailCredit)
	{
		$this->setNodeValue('media:thumbnail/@url', $thumbnailUrl, $item);
		$this->setNodeValue('media:thumbnail/@width', '320', $item);
		$this->setNodeValue('media:thumbnail/@height', '240', $item);
		if (!empty($thumbnailCredit))
		{
			$this->setNodeValue('media:thumbnail/@credit', $thumbnailCredit, $item);
		}
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
	
	/**
	 * ndn used Z for UTC timezone in their example (2008-04-11T12:30:00Z)
	 * @param int $time
	 */
	protected function formatNdnDate($time)
	{
		$date = new DateTime('@'.$time, new DateTimeZone('UTC'));
		return str_replace('+0000', 'Z', $date->format(DateTime::ISO8601)); 
	}
	
}
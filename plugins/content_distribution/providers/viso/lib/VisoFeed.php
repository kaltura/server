<?php
/**
 * @package plugins.visoDistribution
 * @subpackage lib
 */
class VisoFeed
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
	 * @var VisoDistributionProfile
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
		$this->doc->load($xmlTemplate);
		
		$this->xpath = new DOMXPath($this->doc);
		$this->xpath->registerNamespace('media', 'http://search.yahoo.com/mrss/');
		
		$node = $this->xpath->query('/rss/channel/item')->item(0);
		$this->item = $node->cloneNode(true);
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
				$node->childNodes->item(0)->nodeValue = $value;
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
	 * @param VisoDistributionProfile $profile
	 */
	public function setDistributionProfile(VisoDistributionProfile $profile)
	{
		$this->distributionProfile = $profile;
		
		$this->setNodeValue('/rss/channel/title', $profile->getFeedTitle());
		$this->setNodeValue('/rss/channel/link', $profile->getFeedLink());
		$this->setNodeValue('/rss/channel/description', $profile->getFeedDescription());
	}
	
	/**
	 * @param array $values
	 */
	public function addItem(array $values, flavorAsset $flavorAsset = null, thumbAsset $thumbAsset = null)
	{
		$item = $this->item->cloneNode(true);
		$channelNode = $this->xpath->query('/rss/channel', $item)->item(0);
		$channelNode->appendChild($item);
		
		$this->setNodeValue('title', $values[VisoDistributionField::ITEM_TITLE], $item);
		$this->setNodeValue('link', $values[VisoDistributionField::ITEM_LINK], $item);
		$this->setNodeValue('media:content/media:player', $values[VisoDistributionField::MEDIA_PLAYER], $item);
		$this->setNodeValue('media:content/media:rating', $values[VisoDistributionField::MEDIA_RATING], $item);
		$this->setNodeValue('media:content/media:title', $values[VisoDistributionField::MEDIA_TITLE], $item);
		$this->setNodeValue('media:content/media:description', $values[VisoDistributionField::MEDIA_DESCRIPTION], $item);
		
		if ($flavorAsset)
		{
			$url = $this->getAssetUrl($flavorAsset);
			$type = $this->getContentTypeFromUrl($url);
			
			$this->setNodeValue('media:content/@url', $url, $item);
			$this->setNodeValue('media:content/@type', $type, $item);
			$this->setNodeValue('media:content/@fileSize', $flavorAsset->getSize(), $item);
			$this->setNodeValue('media:content/@width', $flavorAsset->getWidth(), $item);
			$this->setNodeValue('media:content/@height', $flavorAsset->getHeight(), $item);
			$this->setNodeValue('media:content/@duration', date('i:s', (int)$flavorAsset->getentry()->getDuration()), $item);
		}
		
		if ($thumbAsset)
		{
			$this->setNodeValue('media:content/media:thumbnail/@url', $this->getAssetUrl($thumbAsset), $item);
		}
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
	
	public function getXml()
	{
		return $this->doc->saveXML();
	}
}
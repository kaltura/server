<?php
/**
 * @package plugins.tvComDistribution
 * @subpackage lib
 */
class TVComFeed
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
	 * @var TVComDistributionProfile
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
		$this->doc->load($xmlTemplate);
		
		$this->xpath = new DOMXPath($this->doc);
		$this->xpath->registerNamespace('media', 'http://search.yahoo.com/mrss/');
		$this->xpath->registerNamespace('dcterms', 'http://purl.org/dc/terms/');
		
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
				$node->nodeValue = $value;
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
	 * @param TVComDistributionProfile $profile
	 */
	public function setDistributionProfile(TVComDistributionProfile $profile)
	{
		$this->distributionProfile = $profile;
		
		kXml::setNodeValue($this->xpath,'/rss/channel/title', $profile->getFeedTitle());
		kXml::setNodeValue($this->xpath,'/rss/channel/link', htmlentities($profile->getFeedLink()));
		kXml::setNodeValue($this->xpath,'/rss/channel/description', $profile->getFeedDescription());
		kXml::setNodeValue($this->xpath,'/rss/channel/language', $profile->getFeedLanguage());
		kXml::setNodeValue($this->xpath,'/rss/channel/copyright', $profile->getFeedCopyright());
		kXml::setNodeValue($this->xpath,'/rss/channel/image/title', $profile->getFeedImageTitle());
		kXml::setNodeValue($this->xpath,'/rss/channel/image/url', $profile->getFeedImageUrl());
		kXml::setNodeValue($this->xpath,'/rss/channel/image/link', $profile->getFeedImageLink());
		kXml::setNodeValue($this->xpath,'/rss/channel/image/width', $profile->getFeedImageWidth());
		kXml::setNodeValue($this->xpath,'/rss/channel/image/height', $profile->getFeedImageHeight());
	}
	
	public function addItemXml($xml)
	{
		$tempDoc = new DOMDocument('1.0', 'UTF-8');
		$tempDoc->loadXML($xml);

		$importedItem = $this->doc->importNode($tempDoc->firstChild, true);
		$channelNode = $this->xpath->query('/rss/channel')->item(0);
		$channelNode->appendChild($importedItem);
	}

	public function getItemXml(array $values, flavorAsset $flavorAsset = null, thumbAsset $thumbAsset = null , array $additionalAssets = null)
	{
		$item = $this->getItem($values, $flavorAsset,$thumbAsset, $additionalAssets);
		return $this->doc->saveXML($item);
	}
	
	public function addItem(array $values, flavorAsset $flavorAsset = null, thumbAsset $thumbAsset = null , array $additionalAssets = null)
	{
		$item = $this->getItem($values, $flavorAsset,$thumbAsset, $additionalAssets);
		$channelNode = $this->xpath->query('/rss/channel', $item)->item(0);
		$channelNode->appendChild($item);
	}
	
	/**
	 * @param array $values
	 */
	public function getItem(array $values, flavorAsset $flavorAsset = null, thumbAsset $thumbAsset = null , array $additionalAssets = null)
	{
		$item = $this->item->cloneNode(true);
		
		$pubDate = date('c', $values[TVComDistributionField::ITEM_PUB_DATE]);
		$expDate = date('c', $values[TVComDistributionField::ITEM_EXP_DATE]);
		$node = kXml::setNodeValue($this->xpath,'guid', $values[TVComDistributionField::GUID_ID], $item);
		$node = kXml::setNodeValue($this->xpath,'pubDate', $pubDate, $item);
		$node = kXml::setNodeValue($this->xpath,'expDate', $expDate, $item);
		$node = kXml::setNodeValue($this->xpath,'link', $values[TVComDistributionField::ITEM_LINK], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:title', $values[TVComDistributionField::MEDIA_TITLE], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:description', $values[TVComDistributionField::MEDIA_DESCRIPTION], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:keywords', $values[TVComDistributionField::MEDIA_KEYWORDS], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:copyright', $values[TVComDistributionField::MEDIA_COPYRIGHT], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:rating', $values[TVComDistributionField::MEDIA_RATING], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:restriction/@relationship', $values[TVComDistributionField::MEDIA_RESTRICTION_TYPE], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:restriction', $values[TVComDistributionField::MEDIA_RESTRICTION_COUNTRIES], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:category[@scheme=\'urn:tvcom:show-tmsid\']', $values[TVComDistributionField::MEDIA_CATEGORY_SHOW_TMSID], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:category[@scheme=\'urn:tvcom:show-tmsid\']/@label', $values[TVComDistributionField::MEDIA_CATEGORY_SHOW_TMSID_LABEL], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:category[@scheme=\'urn:tvcom:episode-tmsid\']', $values[TVComDistributionField::MEDIA_CATEGORY_EPISODE_TMSID], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:category[@scheme=\'urn:tvcom:episode-tmsid\']/@label', $values[TVComDistributionField::MEDIA_CATEGORY_EPISODE_TMSID_LABEL], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:category[@scheme=\'urn:tvcom:episodetype\']', $values[TVComDistributionField::MEDIA_CATEGORY_EPISODE_TYPE], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:category[@scheme=\'urn:tvcom:original_air_date\']', $values[TVComDistributionField::MEDIA_CATEGORY_ORIGINAL_AIR_DATE], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:category[@scheme=\'urn:tvcom:video_format\']', $values[TVComDistributionField::MEDIA_CATEGORY_VIDEO_FORMAT], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:category[@scheme=\'urn:tvcom:season_number\']', $values[TVComDistributionField::MEDIA_CATEGORY_SEASON_NUMBER], $item);
		$node = kXml::setNodeValue($this->xpath,'media:group/media:category[@scheme=\'urn:tvcom:episode_number\']', $values[TVComDistributionField::MEDIA_CATEGORY_EPISODE_NUMBER], $item);
		
		$dcTerms = "start=$pubDate; end=$expDate; scheme=W3C-DTF";
		$node = kXml::setNodeValue($this->xpath,'dcterms:valid', $dcTerms, $item);

		if ($flavorAsset)
		{
			$node = kXml::setNodeValue($this->xpath,'media:group/media:content/@url', $this->getAssetUrl($flavorAsset), $item);
			$type = '';
			switch ($flavorAsset->getFileExt())
			{
				case 'mp4':
					$type = 'video/mp4';
					break;
				case 'flv':
					$type = 'video/x-flv';
					break;
			} 
			$node = kXml::setNodeValue($this->xpath,'media:group/media:content/@type', $type, $item);
			$node = kXml::setNodeValue($this->xpath,'media:group/media:content/@fileSize', $flavorAsset->getSize(), $item);
			$node = kXml::setNodeValue($this->xpath,'media:group/media:content/@expression', $values[TVComDistributionField::MEDIA_CATEGORY_EPISODE_TYPE], $item);
			$node = kXml::setNodeValue($this->xpath,'media:group/media:content/@duration', floor($flavorAsset->getentry()->getDuration()), $item);
		}
		
		if ($thumbAsset)
		{
			$node = kXml::setNodeValue($this->xpath,'media:group/media:thumbnail/@url', $this->getAssetUrl($thumbAsset), $item);
			$node = kXml::setNodeValue($this->xpath,'media:group/media:thumbnail/@width', $thumbAsset->getWidth(), $item);
			$node = kXml::setNodeValue($this->xpath,'media:group/media:thumbnail/@height', $thumbAsset->getHeight(), $item);
		}
		if(is_array($additionalAssets)){
			foreach ($additionalAssets as $additionalAsset){
				/* @var $additionalAsset asset */
				$assetType = $additionalAsset->getType();
				switch($assetType){
					case CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION):
						/* @var $captionPlugin CaptionPlugin */
						$captionPlugin = KalturaPluginManager::getPluginInstance(CaptionPlugin::PLUGIN_NAME);
						$dummyElement = new SimpleXMLElement('<dummy/>');
						$captionPlugin->contributeCaptionAssets($additionalAsset, $dummyElement);
						$dummyDom = dom_import_simplexml($dummyElement);
						$captionDom = $dummyDom->getElementsByTagName('subTitle');
						$captionDom = $this->doc->importNode($captionDom->item(0),true);
						$captionDom = $item->appendChild($captionDom);
						break;
					case AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT):
						/* @var $attachmentPlugin AttachmentPlugin */
						$attachmentPlugin = KalturaPluginManager::getPluginInstance(AttachmentPlugin::PLUGIN_NAME);
						$dummyElement = new SimpleXMLElement('<dummy/>');
						$attachmentPlugin->contributeAttachmentAssets($additionalAsset, $dummyElement);
						$dummyDom = dom_import_simplexml($dummyElement);
						$attachmentDom = $dummyDom->getElementsByTagName('attachment');
						$attachmentDom = $this->doc->importNode($attachmentDom->item(0),true);
						$attachmentDom = $item->appendChild($attachmentDom);
						break;
				}			
			}
		}
		return $item;
	}
	
	public function getAssetUrl(asset $asset)
	{
		$urlManager = DeliveryProfilePeer::getDeliveryProfile($asset->getEntryId());
		$urlManager->getFullAssetUrl($asset);
		$url = preg_replace('/^https?:\/\//', '', $url);
		return 'http://' . $url;
	}
	
	public function getXml()
	{
		return $this->doc->saveXML();
	}
}
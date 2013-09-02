<?php
/**
 * @package plugins.avnDistribution
 * @subpackage lib
 */
class AvnFeed
{
	/**
	 * @var int
	 */
	const NUMBER_OF_AVN_CATEGORIES = 5;
	
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
	 * @var AvnDistributionProfile
	 */
	protected $distributionProfile;
	
	/**
	 * @var array
	 */
	protected $avnCategories = array();
	
	/**
	 * @var array
	 */
	protected $avnCatagoriesIgnore = array('main menu', 'thank you'); // must be lowercased
	
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
		$this->xpath->registerNamespace('amg', 'http://rsp.activemediagroup.com/amg/1.0/');
		
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
	 * @param AvnDistributionProfile $profile
	 */
	public function setDistributionProfile(AvnDistributionProfile $profile)
	{
		$this->distributionProfile = $profile;
		
		kXml::setNodeValue($this->xpath,'/rss/channel/title', $profile->getFeedTitle());
		
		$this->loadAvnCategories($profile->getPartnerId());
	}
	
	/**
	 * @param int $partnerId
	 */
	public function loadAvnCategories($partnerId)
	{
		$criteria = new Criteria();
		$criteria->add(MetadataProfilePeer::PARTNER_ID, $partnerId);
		$metadataProfiles = MetadataProfilePeer::doSelect($criteria);
		foreach($metadataProfiles as $metadataProfile)
		{
			$key = $metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
			$xsd = kFileSyncUtils::file_get_contents($key, true, false);
			$this->avnCategories = array_merge($this->avnCategories, $this->getAvnCategoriesFromXsd($xsd));
		}
	} 
	
	public function addItemXml($xml)
	{
		$tempDoc = new DOMDocument('1.0', 'UTF-8');
		$tempDoc->loadXML($xml);
	
		$importedItem = $this->doc->importNode($tempDoc->firstChild, true);
		$channelNode = $this->xpath->query('/rss/channel')->item(0);
		$channelNode->appendChild($importedItem);
	}
	
	public function getItemXml(array $values, flavorAsset $flavorAssets = null, thumbAsset $thumbAssets = null)
	{
		$item = $this->getItem($values, $flavorAssets, $thumbAssets);
		return $this->doc->saveXML($item);
	}
	
	/**
	 * @param array $values
	 */
	public function getItem(array $values, flavorAsset $flavorAsset = null, thumbAsset $thumbAsset = null)
	{
		$item = $this->item->cloneNode(true);
		
		kXml::setNodeValue($this->xpath,'guid', $values[AvnDistributionField::GUID], $item);
		kXml::setNodeValue($this->xpath,'pubDate', $values[AvnDistributionField::PUB_DATE], $item);
		kXml::setNodeValue($this->xpath,'title', $values[AvnDistributionField::TITLE], $item);
		kXml::setNodeValue($this->xpath,'description', $values[AvnDistributionField::DESCRIPTION], $item);
		kXml::setNodeValue($this->xpath,'link', $values[AvnDistributionField::LINK], $item);
		kXml::setNodeValue($this->xpath,'category', $values[AvnDistributionField::CATEGORY], $item);
		
		kXml::setNodeValue($this->xpath,'amg:passthru', $this->getPassthruJsonObj($values), $item);
		
		if ($flavorAsset)
		{
			$url = $this->getAssetUrl($flavorAsset);
			$type = $this->getContentTypeFromUrl($url);
			
			kXml::setNodeValue($this->xpath,'media:content/@url', $url, $item);
			kXml::setNodeValue($this->xpath,'media:content/@type', $type, $item);
			if ($values[AvnDistributionField::ORDER_SUB] == '1')
				kXml::setNodeValue($this->xpath,'media:content/@isDefault', 'true', $item);
			else
				kXml::setNodeValue($this->xpath,'media:content/@isDefault', 'false', $item);
		}
		
		if ($thumbAsset)
		{
			kXml::setNodeValue($this->xpath,'media:thumbnail/@url', $this->getAssetUrl($thumbAsset), $item);
		}
		
		return $item;
	}
	
	/**
	 * @param asset $asset
	 * @return string
	 */
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
	
	/**
	 * @return string
	 */
	public function getXml()
	{
		return $this->doc->saveXML();
	}
	
	/**
	 * @param string $url
	 * @return string
	 */
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
	 * @param array $values
	 * @return string
	 */
	protected function getPassthruJsonObj($values)
	{
		$obj = new stdClass();
		if (strtolower($values[AvnDistributionField::CATEGORY]) != 'main menu' && strtolower($values[AvnDistributionField::CATEGORY]) != 'thank you')
			$obj->sectionTitle = $values[AvnDistributionField::CATEGORY];
		else 
			$obj->sectionTitle = '';
		$obj->isOnMainMenu = (strtolower($values[AvnDistributionField::IS_ON_MAIN]) == 'true') ? true : false;
		$obj->orderMainMenu = (int)$values[AvnDistributionField::ORDER_MAIN];
		$obj->orderSubMenu = (int)$values[AvnDistributionField::ORDER_SUB];
		$obj->headerCaption = $values[AvnDistributionField::HEADER];
		$obj->subHeaderCaption = $values[AvnDistributionField::SUB_HEADER];
		$obj->menu = $this->getAvnPassthruMenu($values);
		
		return json_encode($obj);
	}
	
	/**
	 * @param array $values
	 * @return array
	 */
	protected function getAvnPassthruMenu($values)
	{
		$menu = array("", "", "", "", ""); // make it compliant with the provided example
		
		// menu is only available the "main menu" item
		if (strtolower($values[AvnDistributionField::CATEGORY]) == 'main menu')
		{
			$avnCategories = $this->avnCategories;
			foreach($avnCategories as $index => $category)
			{
				if (in_array(strtolower($category), $this->avnCatagoriesIgnore))
				{
					unset($avnCategories[$index]);
				}
			}
			$avnCategories = array_values($avnCategories); // to reset the indexes
			for($i = 0; $i < self::NUMBER_OF_AVN_CATEGORIES; $i++)
				$menu[$i] = isset($avnCategories[$i]) ? $avnCategories[$i] : "";
		}
		return $menu;
	}
	
	/**
	 * @param string $xsd
	 * @return array
	 */
	protected function getAvnCategoriesFromXsd($xsd)
	{
		$categories = array();
		$doc = new KDOMDocument();
		$doc->loadXML($xsd);
		$xpath = new DOMXPath($doc);
		$xpath->registerNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
		$categoryNodes = $xpath->query("//xsd:element[@name='AVNCategory']/xsd:simpleType/xsd:restriction[@base='listType']/xsd:enumeration/@value");
		foreach($categoryNodes as $categoryNode)
		{
			$categories[] = $categoryNode->nodeValue;
		}
		return $categories;
	}
}
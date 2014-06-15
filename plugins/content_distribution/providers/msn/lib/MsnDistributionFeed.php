<?php
/**
 * @package plugins.msnDistribution
 * @subpackage lib
 */
class MsnDistributionFeed
{
	const TEMPLATE_XML = 'msn_template.xml';
	
	/**
	 * @var DOMDocument
	 */
	protected $_doc;
	
	/**
	 * @var DOMXPath
	 */
	protected $_xpath;
	
	/**
	 * @var KalturaDistributionJobData
	 */
	protected $_distributionJobData;
	
	/**
	 * @var KalturaMsnDistributionProfile
	 */
	protected $_distributionProfile;
	
	/**
	 * @var KalturaMsnDistributionJobProviderData
	 */
	protected $_providerData;
	
	/**
	 * @var array
	 */
	protected $_fieldValues;
	
	/**
	 * @param string $templateName
	 * @param KalturaMsnDistributionProfile $distributionProfile
	 * @param KalturaMsnDistributionJobProviderData $providerData
	 */
	public function __construct(KalturaDistributionJobData $distributionJobData, KalturaMsnDistributionJobProviderData $providerData)
	{
		$this->_distributionJobData = $distributionJobData;
		$this->_distributionProfile = $distributionJobData->distributionProfile;
		$this->_providerData = $providerData;
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml/' . self::TEMPLATE_XML;
		$this->_doc = new KDOMDocument();
		$this->_doc->load($xmlTemplate);
		$this->_xpath = new DOMXPath($this->_doc);
		$this->_xpath->registerNamespace('msn', 'urn:schemas-microsoft-com:msnvideo:catalog');
		
		$this->_fieldValues = unserialize($this->_providerData->fieldValues);
		if (!$this->_fieldValues) 
			$this->_fieldValues = array();
		
		$this->setNodeValueFieldConfigId('/msn:video/msn:providerId', KalturaMsnDistributionField::PROVIDER_ID);
		$this->setNodeValueFieldConfigId('/msn:video/msn:csId', KalturaMsnDistributionField::CSID);
		$this->setNodeValueFieldConfigId('/msn:video/msn:source', KalturaMsnDistributionField::SOURCE);
		$this->setNodeValueFieldConfigId('/msn:video/msn:source/@friendlyName', KalturaMsnDistributionField::SOURCE_FRIENDLY_NAME);
		$this->setNodeValueFieldConfigId('/msn:video/msn:pageGroup', KalturaMsnDistributionField::PAGE_GROUP);
		$this->setNodeValueFieldConfigId('/msn:video/msn:title', KalturaMsnDistributionField::TITLE);
		$this->setNodeValueFieldConfigId('/msn:video/msn:description', KalturaMsnDistributionField::DESCRIPTION);
		$this->setNodeValueDateFieldConfigIdOrRemove('/msn:video/msn:startDate', KalturaMsnDistributionField::START_DATE);
		$this->setNodeValueDateFieldConfigIdOrRemove('/msn:video/msn:activeEndDate', KalturaMsnDistributionField::ACTIVATE_END_DATE);
		$this->setNodeValueDateFieldConfigIdOrRemove('/msn:video/msn:searchableEndDate', KalturaMsnDistributionField::SEARCHABLE_END_DATE);
		$this->setNodeValueDateFieldConfigIdOrRemove('/msn:video/msn:archiveEndDate', KalturaMsnDistributionField::ARCHIVE_END_DATE);
		
		$this->addTagFieldConfig(KalturaMsnDistributionField::TAGS_MSNVIDEO_CAT, 'MSNVideo_Cat', 'us');
		$this->addTagFieldConfig(KalturaMsnDistributionField::TAGS_MSNVIDEO_TOP, 'MSNVideo_Top', 'us');
		$this->addTagFieldConfig(KalturaMsnDistributionField::TAGS_MSNVIDEO_TOP_CAT, 'MSNVideo_Top_Cat', 'us');
		
		$tags = explode(',', $this->_fieldValues[KalturaMsnDistributionField::TAGS_PUBLIC]);
		$this->addPublicTags($tags);
		
		// premium tags
		$dynamicPremiumTags = $this->getDynamicFieldValuesGrouped(
			KalturaMsnDistributionField::TAGS_PREMIUM_N_MARKET, 
			KalturaMsnDistributionField::TAGS_PREMIUM_N_NAMESPACE, 
			KalturaMsnDistributionField::TAGS_PREMIUM_N_VALUE);
		foreach($dynamicPremiumTags as $dynamicPremiumTag)
		{
			$this->addTag(
				$dynamicPremiumTag[KalturaMsnDistributionField::TAGS_PREMIUM_N_VALUE], 
				$dynamicPremiumTag[KalturaMsnDistributionField::TAGS_PREMIUM_N_NAMESPACE],
				$dynamicPremiumTag[KalturaMsnDistributionField::TAGS_PREMIUM_N_MARKET]);
		}
		
		// related links
		$relatedLinks = $this->getDynamicFieldValuesGrouped(
			KalturaMsnDistributionField::RELATED_LINK_N_URL, 
			KalturaMsnDistributionField::RELATED_LINK_N_TITLE);
			 
		foreach($relatedLinks as $relatedLink)
		{
			$this->addRelatedLink(
				$relatedLink[KalturaMsnDistributionField::RELATED_LINK_N_URL],
				$relatedLink[KalturaMsnDistributionField::RELATED_LINK_N_TITLE]); 
		}
	}
	
	public function setUUID($uuid)
	{
		kXml::setNodeValue($this->_xpath,'/msn:video/msn:uuid', $uuid);
	}
	
	public function addFlavorAssetsByMsnId(array $flavorAssetsByMsnId)
	{
		/*
		 * <videoFile formatCode="1003">
		 * 	 <uri>http://foo.edgefcs.net/foo.flv</uri>
		 * </videoFile>
		 */
		$videoFilesElement = $this->_xpath->query('/msn:video/msn:videoFiles')->item(0);
		foreach($flavorAssetsByMsnId as $msnId => $flavorAsset)
		{
			$videoFileElement = $this->_doc->createElement('videoFile');
			$videoFileElement->setAttribute('formatCode', $msnId);
			$uriElement = $this->_doc->createElement('uri', $this->getAssetUrl($flavorAsset));
			$videoFileElement->appendChild($uriElement);
			$videoFilesElement->appendChild($videoFileElement); 
		}
	}
	
	public function addThumbnailAssets(array $thumbnailAssets)
	{
		/*
		 * <file formatCode="2009">
		 *   <uri>http://foo.edgefcs.net/foo.jpg</uri>
		 * </file>
		 */
		
		$filesElement = $this->_xpath->query('/msn:video/msn:files')->item(0);
		foreach($thumbnailAssets as $thumbnailAsset)
		{
			$fileElement = $this->_doc->createElement('file');
			$fileElement->setAttribute('formatCode', '2009');
			$uriElement = $this->_doc->createElement('uri', $this->getAssetUrl($thumbnailAsset));
			$fileElement->appendChild($uriElement);
			$filesElement->appendChild($fileElement);
		}
	}
	
	protected function getDynamicFieldValuesGrouped(/* $args */)
	{
		$dynamicFields = func_get_args();
		$fieldConfigArray = $this->_distributionProfile->fieldConfigArray;
		$groupedValues = array();
		foreach($fieldConfigArray as $fieldConfig)
		{
			/* @var $fieldConfig KalturaDistributionFieldConfig */
			
			foreach($dynamicFields as $dynamicField)
			{
				$dynamicFieldRegex = '/'.str_replace('_N_', '_([0-9]+)_', $dynamicField).'/';
				if (preg_match($dynamicFieldRegex, $fieldConfig->fieldName, $matches))
				{
					$index = $matches[1];
					if (isset($this->_fieldValues[$fieldConfig->fieldName]))
					{ 
						if (!isset($groupedValues[$index]))
							$groupedValues[$index] = array();
							
						$groupedValues[$index][$dynamicField] = $this->_fieldValues[$fieldConfig->fieldName];
					}
						
					break;
				}
			}
		}
		
		return $groupedValues;
	}
	
	public function addPublicTags(array $tags)
	{
		foreach($tags as $tag)
		{
			$tag = trim($tag);
			if ($tag)
				$this->addTag($tag, 'Public', 'us');
		}
	}
	
	function addTagFieldConfig($fieldConfigId, $namespace, $market = null)
	{
		if (isset($this->_fieldValues[$fieldConfigId]) && $this->_fieldValues[$fieldConfigId])
			$this->addTag($this->_fieldValues[$fieldConfigId], $namespace, $market);
	}
	
	public function addTag($tag, $namespace, $market = null)
	{
		$tagElement = $this->_doc->createElement('tag', $tag);
		if ($market)
			$tagElement->setAttribute('market', 'us');
			
		$tagElement->setAttribute('namespace', $namespace);
		
		$tagsElement = $this->_xpath->query('/msn:video/msn:tags')->item(0);
		$tagsElement->appendChild($tagElement);
	}
	
	public function addRelatedLink($url, $title)
	{
		$linkElement = $this->_doc->createElement('link', $title);
		$linkElement->setAttribute('url', $url);
		
		$linksElement = $this->_xpath->query('/msn:video/msn:extendedXml/msn:relatedLinks')->item(0);
		$linksElement->appendChild($linkElement);
	}
	
	/**
	 * @param string $xpath
	 * @param string $fieldConfigId
	 */
	public function setNodeValueFieldConfigId($xpath, $fieldConfigId, DOMNode $contextnode = null)
	{
		if (isset($this->_fieldValues[$fieldConfigId]))
			kXml::setNodeValue($this->_xpath,$xpath, $this->_fieldValues[$fieldConfigId], $contextnode);
	}
	
	/**
	 * @param string $xpath
	 * @param string $fieldConfigId
	 */
	public function setNodeValueDateFieldConfigId($xpath, $fieldConfigId, DOMNode $contextnode = null)
	{
		if (isset($this->_fieldValues[$fieldConfigId]) && $this->_fieldValues[$fieldConfigId]) 
		{
			$this->setNodeValueDate($xpath, $this->_fieldValues[$fieldConfigId], $contextnode);
		}
	}
	
	/**
	 * @param string $xpath
	 * @param string $fieldConfigId
	 */
	public function setNodeValueDateFieldConfigIdOrRemove($xpath, $fieldConfigId, DOMNode $contextnode = null)
	{
		if (isset($this->_fieldValues[$fieldConfigId]) && $this->_fieldValues[$fieldConfigId]) 
		{
			$this->setNodeValueDate($xpath, $this->_fieldValues[$fieldConfigId], $contextnode);
		}
		else
		{
			$this->removeNode($xpath, $contextnode);
		}
	}
	
	/**
	 * @param string $xpath
	 * @param string $value
	 * @param DOMNode $contextnode
	 */
	public function setNodeValue($xpath, $value, DOMNode $contextnode = null)
	{
		if ($contextnode)
			$node = $this->_xpath->query($xpath, $contextnode)->item(0);
		else 
			$node = $this->_xpath->query($xpath)->item(0);
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
	 * @param DOMNode $contextnode
	 */
	public function setNodeValueDate($xpath, $value, DOMNode $contextnode = null)
	{
		$dateTime = new DateTime('@'.$value);
		// force time zone to GMT
		$dateTime->setTimezone(new DateTimeZone('GMT'));
		$date = $dateTime->format('c');
		$date = str_replace('+00:00', 'Z', $date);
		kXml::setNodeValue($this->_xpath,$xpath, $date, $contextnode);
	}
	
	/**
	 * @param string $xpath
	 * @param DOMNode $contextnode
	 */
	public function removeNode($xpath, DOMNode $contextnode = null)
	{
		if ($contextnode) 
		{
			$node = $this->_xpath->query($xpath, $contextnode)->item(0);
		}
		else 
		{
			$node = $this->_xpath->query($xpath)->item(0);
		}
		if (!is_null($node))
		{
			$node->parentNode->removeChild($node);
		}
	}
	
	/**
	 * @param string $xpath
	 * @param DOMNode $element
	 */
	public function appendElement($xpath, DOMNode $element)
	{
		$parentElement = $this->_xpath->query($xpath)->item(0);
		if ($parentElement && $parentElement instanceof DOMNode)
		{
			$parentElement->appendChild($element);
		}
	}
	
	/**
	 * @param string $xpath
	 */
	public function getNodeValue($xpath)
	{
		$node = $this->_xpath->query($xpath)->item(0);
		if (!is_null($node))
			return $node->nodeValue;
		else
			return null;
	}
	
	public function getXml()
	{
		$xml = $this->_doc->saveXML();
		return $xml;
	}
	
	public function getAssetUrl(asset $asset)
	{
		$urlManager = DeliveryProfilePeer::getDeliveryProfile($asset->getEntryId());
		if($asset instanceof flavorAsset)
			$urlManager->initDeliveryDynamicAttributes(null, $asset);
		$url = $urlManager->getFullAssetUrl($asset);
		$url = preg_replace('/^https?:\/\//', '', $url);
		$url = $url.'/'.$asset->getId().'.'.$asset->getFileExt();
		return 'http://' . $url;
	}
}
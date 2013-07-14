<?php
/**
 * @package plugins.yahooDistribution
 * @subpackage lib
 */
class YahooDistributionFeedHelper
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
	 * @var KalturaYahooDistributionProfile
	 */
	protected $distributionProfile;

	/**
	 * @var KalturaEntryDistribution
	 */
	protected $entryDistribution;
	/**
	 * 
	 * Provider data object
	 * @var KalturaYouTubeDistributionJobProviderData
	 */
	protected $providerData;
	
	protected $fieldValues;
	
	protected $flavorAssets;
	
	const VIDEO_TIME_ZONE = 'America/New_York';
	const VIDEO_TIME_ZONE_CODE = 'ET';
	const VIDEO_TIME_FORMAT = 'm/d/Y H:i:s';
		
	/**
	 * @param $templateName
	 * @param $distributionProfile
	 * @param KalturaYahooDistributionJobProviderData $providerData
	 * @param KalturaDistributionJobData $data
	 */
	public function __construct($templateName, KalturaYahooDistributionProfile $distributionProfile, KalturaYahooDistributionJobProviderData $providerData, $entryDistribution, $flavorAssets)
	{		
		$this->distributionProfile = $distributionProfile;
		$this->providerData = $providerData;
		$this->entryDistribution = $entryDistribution;
		$this->fieldValues = unserialize($providerData->fieldValues);
		if (!$this->fieldValues) {
		    $this->fieldValues = array();
		}		
		$this->flavorAssets = $flavorAssets;
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml_templates/' . $templateName;
		$this->doc = new KDOMDocument();
		$this->doc->load($xmlTemplate);		
		$this->xpath = new DOMXPath($this->doc);	
	}
	
	public function setFieldsForSubmit()
	{
		$startTime = $this->getValueForField(KalturaYahooDistributionField::VIDEO_VALID_TIME);
		if (is_null($startTime)) {
		    $startTime = time() - 24*60*60;  // yesterday, to make the video public by default
		}
		$this->setVideoValidTime($startTime);
		
		$endTime = $this->getValueForField(KalturaYahooDistributionField::VIDEO_EXPIRATION_TIME);
		if ($endTime && intval($endTime)) {
			$this->setVideoExpirationTime($endTime);        
		}
		//remove video exportaion time tag if empty
		else{
			$this->deleteVideoExpirtaionTimeTag();
		}		
		
		$this->setContactTelephone($this->getValueForField(KalturaYahooDistributionField::CONTACT_TELEPHONE));
		$this->setContactEmail($this->getValueForField(KalturaYahooDistributionField::CONTACT_EMAIL));					
		$this->setVideoModifiedDate($this->getValueForField(KalturaYahooDistributionField::VIDEO_MODIFIED_DATE));		
		$this->setVideoFeedItemId($this->getValueForField(KalturaYahooDistributionField::VIDEO_FEEDITEM_ID));
		$this->setVideoTitle($this->getValueForField(KalturaYahooDistributionField::VIDEO_TITLE));
		$this->setVideoDescription($this->getValueForField(KalturaYahooDistributionField::VIDEO_DESCRIPTION));
		$this->setVideoCategories($this->getValueForField(KalturaYahooDistributionField::VIDEO_ROUTING));
		$this->setVideoKeywords($this->getValueForField(KalturaYahooDistributionField::VIDEO_KEYWORDS));		
		$this->setLinkTitleAndUrl($this->getValueForField(KalturaYahooDistributionField::VIDEO_LINK_TITLE),
								  $this->getValueForField(KalturaYahooDistributionField::VIDEO_LINK_URL));		
		$this->setVideoDuration($this->getValueForField(KalturaYahooDistributionField::VIDEO_DURATION));		
	}
	
	public function setFieldsForDelete()
	{
		$this->setVideoFeedItemId($this->getValueForField(KalturaYahooDistributionField::VIDEO_FEEDITEM_ID));
		$this->setVideoTitle($this->getValueForField(KalturaYahooDistributionField::VIDEO_TITLE));
		//valid time
		$startTime = $this->getValueForField(KalturaYahooDistributionField::VIDEO_VALID_TIME);
		if (is_null($startTime)) {
		    $startTime = time() - 24*60*60;  // yesterday, to make the video public by default
		}
		$this->setVideoValidTime($startTime);
		
		//expiration time
		$newEndTime = time() - 24*60*60;  // yesterday, to make the video expired by default
		if ($newEndTime && intval($newEndTime)) {
			$this->setVideoExpirationTime($newEndTime);
		}		
	}
	
	private function getValueForField($fieldName)
	{
	    if (isset($this->fieldValues[$fieldName])) {
	        return $this->fieldValues[$fieldName];
	    }
	    return null;
	}	
	
	/**
	 * @param string $xpath
	 * @param string $value
	 */
	public function setNodeValue($xpath, $value, DOMNode $contextnode = null)
	{
		if ($contextnode) {
			$node = $this->xpath->query($xpath, $contextnode)->item(0);
		}
		else { 
			$node = $this->xpath->query($xpath)->item(0);
		}
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
	
	public function setContactTelephone($value)
	{
		kXml::setNodeValue($this->xpath,'/CMSFEED/CONTACT/@TELEPHONE', $value);
	}
	
	public function setContactEmail($value)
	{
		kXml::setNodeValue($this->xpath,'/CMSFEED/CONTACT/@EMAIL', $value);
	}
		
	public function setVideoType($value)
	{
		kXml::setNodeValue($this->xpath,'/CMSFEED/VIDEO/@TYPE', $value);
	}
	
	public function setVideoModifiedDate($value)
	{
		$dateTime = new DateTime('@'.$value);
		$dateTime->setTimezone(new DateTimeZone(self::VIDEO_TIME_ZONE));
		$date = $dateTime->format(self::VIDEO_TIME_FORMAT);
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/MODIFIEDDATE', $date);
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/MODIFIEDDATE/@TZ', self::VIDEO_TIME_ZONE_CODE);	
	}
		
	public function setVideoFeedItemId($value)
	{		
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/@ID', $value);
	}
	
	public function setVideoTitle($value)
	{
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/TITLE', $value);
	}
	
	public function setVideoDescription($value)
	{
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/DESCRIPTION', $value);
	}
	
	public function setVideoCategories($value)
	{
		$categories = explode(',', $value);
		if ($categories)
		{
			$node = $this->xpath->query('/CMSFEED/FEEDITEM/ROUTING')->item(0);
			$parentNode = $node->parentNode;		
			//$beforeNode = $this->xpath->query('/CMSFEED/FEEDITEM/KEYWORDS')->item(0);		
			foreach ($categories as $category)
			{
				if ($category){
					$routingContentNode = $node->cloneNode(true);									
					kXml::setNodeValue($this->xpath,'.', $category, $routingContentNode);					
					$parentNode->insertBefore($routingContentNode,$node);
				}
			}
			$parentNode->removeChild($node);
		}
	}
	
	public function setVideoKeywords($value)
	{
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/KEYWORDS', $value);
	}
	
	
	public function setLinkTitleAndUrl($titles, $urls)
	{
		$titleValues = explode(';', $titles);
		$urlValues = explode(';', $urls);		
		if ($titleValues)
		{
			$node = $this->xpath->query('/CMSFEED/FEEDITEM/LINK')->item(0);
			$parentNode = $node->parentNode;					
			foreach ($titleValues as $key =>$value)
			{
				$titleContentNode = $node->cloneNode(true);		
				//set attribute as title
				kXml::setNodeValue($this->xpath,'./@TITLE', $value, $titleContentNode);
				//set value as url			
				if (isset($urlValues[$key]))
				{			
					kXml::setNodeValue($this->xpath,'.', $urlValues[$key], $titleContentNode);
				}					
				$parentNode->insertBefore($titleContentNode,$node);
			}
			//check id more urls than titles
			if (count($urlValues) > count($titleValues))
			{
				for ($i=count($titleValues); $i<count($urlValues) ; $i++ )
				{
					$titleContentNode = $node->cloneNode(true);	
					kXml::setNodeValue($this->xpath,'.', $urlValues[$i], $titleContentNode);
					$parentNode->insertBefore($titleContentNode,$node);				
				}
			}
			$parentNode->removeChild($node);
		}	
	}
		
	public function setVideoValidTime($value)
	{
		$dateTime = new DateTime('@'.$value);
		$dateTime->setTimezone(new DateTimeZone(self::VIDEO_TIME_ZONE));
		$date = $dateTime->format(self::VIDEO_TIME_FORMAT);
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/VALIDTIME', $date);
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/VALIDTIME/@TZ', self::VIDEO_TIME_ZONE_CODE);	
	}
	
	public function setVideoExpirationTime($value)
	{		
		$dateTime = new DateTime('@'.$value);
		$dateTime->setTimezone(new DateTimeZone(self::VIDEO_TIME_ZONE));
		$date = $dateTime->format(self::VIDEO_TIME_FORMAT);
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/EXPIRATIONTIME', $date);
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/EXPIRATIONTIME/@TZ', self::VIDEO_TIME_ZONE_CODE);	
	}
		
	public function deleteVideoExpirtaionTimeTag()
	{
		$node = $this->xpath->query('/CMSFEED/FEEDITEM/EXPIRATIONTIME')->item(0);
		$node->parentNode->removeChild($node);		
	}
	
	public function setVideoDuration($value)
	{
		$value = gmdate('H:i:s', $value/1000);
		kXml::setNodeValue($this->xpath,'/CMSFEED/FEEDITEM/DURATION', $value);
	}

	public function getXmlString()
	{
		return $this->doc->saveXML();
	}

	/**
	 * 
	 * set streams in XML
	 * @param KalturaFlavorAsset $flavorAssets
	 */
	public function setStreams($flavorAssets, $currentTime)
	{		
		// if we have specific flavor assets for this distribution, grab the first one
		if(count($flavorAssets))
		{
			$node = $this->xpath->query('/CMSFEED/FEEDITEM/STREAM')->item(0);
			$streamParentNode = $node->parentNode;
			$streamParentNode->removeChild($node);
			$beforeNode = $this->xpath->query('/CMSFEED/FEEDITEM/KEYWORDS')->item(0);
						
			foreach ($flavorAssets as $flavorAsset)
			{
				$streamContentNode = $node->cloneNode(true);					
				$fileExt = $flavorAsset->fileExt;			
				$videoBitrate = $flavorAsset->bitrate;
				kXml::setNodeValue($this->xpath,'@FORMAT', strtoupper($fileExt), $streamContentNode);
				kXml::setNodeValue($this->xpath,'@BITRATE', $videoBitrate, $streamContentNode);				
								
				$uniqueName = $this->flavorAssetUniqueName($flavorAsset, $currentTime);
				kXml::setNodeValue($this->xpath,'.', $uniqueName, $streamContentNode);
				//kXml::setNodeValue($this->xpath,'.', providerData->flavorAsset[id]);						
				$streamParentNode->insertBefore($streamContentNode,$beforeNode);
			}		
		}
	}
	
	/**
	 * creates unique name for flavor asset
	 * @param KalturaFlavorAsset $flavorAsset
	 */
	public function flavorAssetUniqueName($flavorAsset, $currentTime)
	{
		$fileExt = $flavorAsset->fileExt;	
		$uniqueName = $currentTime.'_'.$this->entryDistribution->id.'_'.$flavorAsset->entryId.'_'.$flavorAsset->id.'.'.$fileExt;
		return $uniqueName;		
	}
	
	public function setThumbnailsPath($smallThumbPath, $largeThumbPath)
	{
		kXml::setNodeValue($this->xpath,"/CMSFEED/FEEDITEM/IMAGE[@USE='SMALLTHUMB']", $smallThumbPath);
		kXml::setNodeValue($this->xpath,"/CMSFEED/FEEDITEM/IMAGE[@USE='LARGETHUMB']", $largeThumbPath);		
	}
	
	
}
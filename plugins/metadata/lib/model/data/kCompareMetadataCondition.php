<?php
/**
 * @package plugins.metadata
 * @subpackage model.data
 */
class kCompareMetadataCondition extends kCompareCondition
{
	/**
	 * May contain the full xpath to the field in two formats
	 * 1. Slashed xPath, e.g. /metadata/myElementName
	 * 2. Using local-name function, e.g. /*[local-name()='metadata']/*[local-name()='myElementName']
	 * 3. Using only the field name, e.g. myElementName, it will be searched as //myElementName
	 * 
	 * @var string
	 */
	private $xPath;
	
	/**
	 * @var int
	 */
	private $profileId;
	
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(MetadataPlugin::getConditionTypeCoreValue(MetadataConditionType::METADATA_FIELD_COMPARE));
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::getFieldValue()
	 */
	public function getFieldValue(accessControl $accessControl)
	{
		$scope = $accessControl->getScope();
		$metadata = MetadataPeer::retrieveByObject($this->profileId, MetadataObjectType::ENTRY, $scope->getEntryId());
		if(!$metadata)
			return null;
			
			
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$source = kFileSyncUtils::file_get_contents($key, true, false);
		if(!$source)
			return null;
		
		$xml = new DOMDocument();
		$xml->loadXML($source);
		
		$xPathPattern = $this->xPath;
		if(preg_match('/^\w[\w\d]*$/', $xPathPattern))
			$xPathPattern = "//$xPathPattern";
		
		$matches = null;
		if(preg_match_all('/\/(\w[\w\d]*)/', $xPathPattern, $matches))
		{
			if(count($matches) == 2 && implode('', $matches[0]) == $xPathPattern)
			{
				$xPathPattern = '';
				foreach($matches[1] as $match)
					$xPathPattern .= "/*[local-name()='$match']";
			}
		}
		
		$xPath = new DOMXPath($xml);
		$elementsList = $xPath->query($xPathPattern);
		$values = array();
		foreach($elementsList as $element)
		{
			/* @var $element DOMNode */
			$values[] = intval($element->textContent);
		}

		return $values;
	}
	
	/**
	 * @return string $xPath
	 */
	public function getXPath() 
	{
		return $this->xPath;
	}

	/**
	 * @return int $profileId
	 */
	public function getProfileId()
	{
		return $this->profileId;
	}

	/**
	 * @param string $xPath
	 */
	public function setXPath($xPath) 
	{
		$this->xPath = $xPath;
	}

	/**
	 * @param int $profileId
	 */
	public function setProfileId($profileId) 
	{
		$this->profileId = $profileId;
	}
}

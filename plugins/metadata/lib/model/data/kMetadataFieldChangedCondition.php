<?php
/**
 * @package plugins.metadata
 * @subpackage model.data
 */
class kMetadataFieldChangedCondition extends kCondition
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
	
	/**
	 * @var string
	 */
	private $profileSystemName;
	
	/**
	 * @var string
	 */
	private $versionA;
	
	/**
	 * @var string
	 */
	private $versionB;
	
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(MetadataPlugin::getConditionTypeCoreValue(MetadataConditionType::METADATA_FIELD_CHANGED));
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::applyDynamicValues()
	 */
	protected function applyDynamicValues(kScope $scope)
	{
		parent::applyDynamicValues($scope);
		
		$dynamicValues = $scope->getDynamicValues('{', '}');
		
		if(is_array($dynamicValues) && count($dynamicValues))
		{
			$this->xPath = str_replace(array_keys($dynamicValues), $dynamicValues, $this->xPath);
			$this->versionA = str_replace(array_keys($dynamicValues), $dynamicValues, $this->versionA);
			$this->versionB = str_replace(array_keys($dynamicValues), $dynamicValues, $this->versionB);
			if($this->profileSystemName)
				$this->profileSystemName = str_replace(array_keys($dynamicValues), $dynamicValues, $this->profileSystemName);
		}
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		$profileId = $this->profileId;
		if(!$profileId)
		{
			if(!$this->profileSystemName)
				return null;
				
			$profile = MetadataProfilePeer::retrieveBySystemName($this->profileSystemName, kCurrentContext::getCurrentPartnerId());
			if(!$profile)
				return null;
				
			$profileId = $profile->getId();
		}
		
		$metadata = null;
		if($scope instanceof accessControlScope)
		{
			$metadata = MetadataPeer::retrieveByObject($profileId, MetadataObjectType::ENTRY, $scope->getEntryId());
		}
		elseif($scope instanceof kEventScope && $scope->getEvent() instanceof kApplicativeEvent)
		{
			$object = $scope->getEvent()->getObject();
			if($object instanceof IMetadataObject)
				$metadata = MetadataPeer::retrieveByObject($profileId, $object->getMetadataObjectType(), $object->getId());
		}
			
		if(!$metadata)
			return false;
			
		return kMetadataManager::parseMetadataValues($metadata, $this->xPath, $this->versionA) != kMetadataManager::parseMetadataValues($metadata, $this->xPath, $this->versionB);
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

	/**
	 * @return string
	 */
	public function getProfileSystemName() 
	{
		return $this->profileSystemName;
	}

	/**
	 * @param string $profileSystemName
	 */
	public function setProfileSystemName($profileSystemName) 
	{
		$this->profileSystemName = $profileSystemName;
	}

	/* (non-PHPdoc)
	 * @see kCondition::shouldFieldDisableCache()
	 */
	public function shouldFieldDisableCache($scope)
	{
		return false;
	}	
}

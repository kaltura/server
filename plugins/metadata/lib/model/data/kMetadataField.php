<?php
/**
 * @package plugins.metadata
 * @subpackage model.data
 */
class kMetadataField extends kStringField
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
	
	/* (non-PHPdoc)
	 * @see kIntegerField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null)
	{
		if(!$scope || (is_null($this->profileId) && is_null($this->profileSystemName)))
			return null;
		
		$profileId = $this->profileId;
		if(is_null($profileId))
		{
			$profile = MetadataProfilePeer::retrieveBySystemName($this->profileSystemName, array(kCurrentContext::getCurrentPartnerId(), PartnerPeer::GLOBAL_PARTNER));
			if($profile)
				$profileId = $profile->getId();
		}
		
		if(is_null($profileId))
		{
			KalturaLog::err("No metadata profile found matching input values of profileId [{$this->profileId}] systemName [{$this->profileSystemName}]");
			return null;
		}
		
		$metadata = null;
		if($scope instanceof accessControlScope || $scope instanceof kStorageProfileScope)
		{
			$metadata = MetadataPeer::retrieveByObject($profileId, MetadataObjectType::ENTRY, $scope->getEntryId());
		}
		elseif($scope instanceof kEventScope)
		{
			$object = $scope->getEvent()->getObject();
			if(kMetadataManager::isMetadataObject($object))
			{
				$objectType = kMetadataManager::getTypeNameFromObject($object);
				$metadata = MetadataPeer::retrieveByObject($profileId, $objectType, $object->getId());
			}
			elseif ($object instanceof Metadata)
			{
				$metadata = $object;
			}
			elseif ($scope->getEvent()->getObject() instanceof categoryEntry)
			{
				$profileObject = kMetadataManager::getObjectTypeName($profile->getObjectType());
				$getter = "get{$profileObject}Id";
				KalturaLog::info ("Using $getter in order to retrieve the metadata object ID");
				$categoryEntry = $scope->getEvent()->getObject();
				$objectId = $categoryEntry->$getter();
				$metadata = MetadataPeer::retrieveByObject($profileId, $profile->getObjectType(), $objectId);
			}
			elseif ($object instanceof asset)
			{
				$metadata = MetadataPeer::retrieveByObject($profileId, MetadataObjectType::ENTRY, $object->getEntryId());
			}
		}
		
		if($metadata)
		{
			$values = kMetadataManager::parseMetadataValues($metadata, $this->xPath);
			if($values && count($values))
			{
				return reset($values);
			}
		}
		
		KalturaLog::notice("Metadata object not found for scope [" . get_class($scope) . "]");
		return null;
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
}

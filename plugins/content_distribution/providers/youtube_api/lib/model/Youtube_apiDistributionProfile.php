<?php
/**
 * @package plugins.youtube_apiDistribution
 * @subpackage model
 */
class Youtube_apiDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USER = 'user';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';	


	const METADATA_FIELD_CATEGORY = 'Youtube_apiCategory';
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return Youtube_apiDistributionPlugin::getProvider();
	}
			
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		if(!class_exists('MetadataProfile'))
			return $validationErrors;
			
		$metadataProfileId = $this->getMetadataProfileId();
		if(!$metadataProfileId)
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_CATEGORY);
			return $validationErrors;
		}
		
		$metadataProfileCategoryField = MetadataProfileFieldPeer::retrieveByMetadataProfileAndKey($metadataProfileId, self::METADATA_FIELD_CATEGORY);
		if(!$metadataProfileCategoryField)
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_CATEGORY);
			return $validationErrors;
		}
		
		$metadatas = MetadataPeer::retrieveAllByObject(Metadata::TYPE_ENTRY, $entryDistribution->getEntryId());
		if(!count($metadatas))
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_CATEGORY);
			return $validationErrors;
		}
		
		$values = $this->findMetadataValue($metadatas, self::METADATA_FIELD_CATEGORY);
		
		if(!count($values))
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_CATEGORY);
			
		foreach($values as $value)
		{
			if(!strlen($value))
			{
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_CATEGORY);
				return $validationErrors;
			}
		}
		
		return $validationErrors;
	}
	
	public function getUser()					{return $this->getFromCustomData(self::CUSTOM_DATA_USER);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getMetadataProfileId()		{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	
	public function setUser($v)				{$this->putInCustomData(self::CUSTOM_DATA_USER, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setMetadataProfileId($v)	{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}
}
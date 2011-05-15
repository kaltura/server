<?php
/**
 * @package plugins.ideticDistribution
 * @subpackage model
 */
class IdeticDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_DOMAIN = 'domain';
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';
	
	const METADATA_FIELD_SHORTTITLE = 'ShortTitle';
	const METADATA_FIELD_STATSKEY = 'Statskeys';	

	const ENTRY_NAME_MINIMUM_LENGTH = 1;
	const ENTRY_NAME_MAXIMUM_LENGTH = 32;
	const ENTRY_DESCRIPTION_MINIMUM_LENGTH = 1;
	const ENTRY_DESCRIPTION_MAXIMUM_LENGTH = 300;
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return IdeticDistributionPlugin::getProvider();
	}

	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
		if(!$entry)
		{
			KalturaLog::err("Entry [" . $entryDistribution->getEntryId() . "] not found");
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'entry', 'entry not found');
			return $validationErrors;
		}
		
		// validate entry description minumum length of 1 character
		if(strlen($entry->getDescription()) < self::ENTRY_DESCRIPTION_MINIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::DESCRIPTION, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_DESCRIPTION_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		if(!class_exists('MetadataProfile'))
			return $validationErrors;
			
		$metadataFields = array(
			self::METADATA_FIELD_SHORTTITLE,
		);
		
		$metadataProfileId = $this->getMetadataProfileId();
		if(!$metadataProfileId)
		{
			foreach($metadataFields as $metadataField)
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $metadataField, "");
			return $validationErrors;
		}
		
		foreach($metadataFields as $index => $metadataField)
		{
			$metadataProfileCategoryField = MetadataProfileFieldPeer::retrieveByMetadataProfileAndKey($metadataProfileId, $metadataField);
			if(!$metadataProfileCategoryField)
			{
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $metadataField);
				unset($metadataFields[$index]);
				continue;
			}
		}
		
		$metadatas = MetadataPeer::retrieveAllByObject(Metadata::TYPE_ENTRY, $entryDistribution->getEntryId());
		if(!count($metadatas))
		{
			foreach($metadataFields as $metadataField)
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $metadataField, "");
			return $validationErrors;
		}
		
		foreach($metadataFields as $index => $metadataField)
		{
			$values = $this->findMetadataValue($metadatas, $metadataField);
			
			if(!count($values))
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $metadataField, "");
				
			foreach($values as $value)
			{
				if(!strlen($value))
				{
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $metadataField, "");
					$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
					$validationError->setMetadataProfileId($metadataProfileId);
					$validationErrors[] = $validationError;
					break;
				}
			}
		}
		
		return $validationErrors;
	}
	
	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getDomain()				{return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN);}
	public function getMetadataProfileId()			{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setDomain($v)				{$this->putInCustomData(self::CUSTOM_DATA_DOMAIN, $v);}
	public function setMetadataProfileId($v)		{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}	
}
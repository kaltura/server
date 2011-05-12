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
	const METADATA_FIELD_MEDIUMDESCRIPTION = 'MediumDescription';	

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
		
		// validate entry name minumum length of 1 character
		if(strlen($entry->getName()) < self::ENTRY_NAME_MINIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::NAME, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
/*		// validate entry name maximum length of 60 characters
		if(strlen($entry->getName()) > self::ENTRY_NAME_MAXIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::NAME, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MAXIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
	*/	
		// validate entry description minumum length of 1 character
		if(strlen($entry->getDescription()) < self::ENTRY_DESCRIPTION_MINIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::DESCRIPTION, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_DESCRIPTION_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
/*		// validate entry description maximum length of 60 characters
		if(strlen($entry->getDescription()) > self::ENTRY_DESCRIPTION_MAXIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::DESCRIPTION, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
			$validationError->setValidationErrorParam(self::ENTRY_DESCRIPTION_MAXIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
	*/	
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
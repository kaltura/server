<?php
/**
 * @package plugins.freewheelDistribution
 * @subpackage model
 */
class FreewheelDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_APIKEY = 'apikey';
	const CUSTOM_DATA_EMAIL = 'email';
	const CUSTOM_DATA_SFTP_LOGIN = 'sftpLogin';
	const CUSTOM_DATA_SFTP_PASS = 'sftpPass';
	
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';
	const ENTRY_NAME_MINIMUM_LENGTH = 1;
	const ENTRY_NAME_MAXIMUM_LENGTH = 120;
	const ENTRY_DESCRIPTION_MINIMUM_LENGTH = 1;
	const ENTRY_DESCRIPTION_MAXIMUM_LENGTH = 2000;
	const METADATA_FIELD_MEDIAKEY = 'MediaKey';
		
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return FreewheelDistributionPlugin::getProvider();
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
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::NAME, 'Name is too short');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		if(strlen($entry->getName()) > self::ENTRY_NAME_MAXIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::NAME, 'Name is too long');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MAXIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		$validationErrors = $this->validateMetadataForSubmission($entryDistribution, $action, $validationErrors);
		
		if(!strlen($entry->getDescription()))
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, entryPeer::DESCRIPTION, 'Description is empty');
		}
		elseif(strlen($entry->getDescription()) < self::ENTRY_DESCRIPTION_MINIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::DESCRIPTION, 'Description is too short');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_DESCRIPTION_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		elseif(strlen($entry->getDescription()) > self::ENTRY_DESCRIPTION_MAXIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::DESCRIPTION, 'Description is too long');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
			$validationError->setValidationErrorParam(self::ENTRY_DESCRIPTION_MAXIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
	
		return $validationErrors;
	}
	
	
	public function validateMetadataForSubmission(EntryDistribution $entryDistribution, $action, array $validationErrors) {
		if(!class_exists('MetadataProfile'))
			return $validationErrors;
			
		$metadataProfileId = $this->getMetadataProfileId();
		if(!$metadataProfileId)
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_MEDIAKEY, '');
			return $validationErrors;
		}
		$metadata = MetadataPeer::retrieveByObject($metadataProfileId, MetadataObjectType::ENTRY, $entryDistribution->getEntryId());
		if(!$metadata)
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_MEDIAKEY);
			return $validationErrors;
		}
		
		$values = $this->findMetadataValue(array($metadata), self::METADATA_FIELD_MEDIAKEY);
		
		if(!count($values)) {
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_MEDIAKEY, '');
		}
		return $validationErrors;
	}
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 * Shows how to report validation errors
	 */

	public function getApiKey()					{return $this->getFromCustomData(self::CUSTOM_DATA_APIKEY);}
	public function getEmail()					{return $this->getFromCustomData(self::CUSTOM_DATA_EMAIL);}
	public function getMetadataProfileId()		{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	public function getSftpPass()				{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PASS);}
	public function getSftpLogin()				{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_LOGIN);}
		
	public function setApiKey($v)				{$this->putInCustomData(self::CUSTOM_DATA_APIKEY, $v);}
	public function setEmail($v)				{$this->putInCustomData(self::CUSTOM_DATA_EMAIL, $v);}
	public function setMetadataProfileId($v)	{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}
	public function setSftpPass($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PASS, $v);}
	public function setSftpLogin($v)			{$this->putInCustomData(self::CUSTOM_DATA_SFTP_LOGIN, $v);}
}
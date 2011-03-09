<?php
/**
 * @package plugins.exampleDistribution
 * @subpackage model
 */
class ExampleDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_ACCOUNT_ID = 'accountId';
	
	const METADATA_FIELD_TEST_DATA = 'testData';
	const METADATA_FIELD_TEST_DATA_FORMAT = 'format-([0-9]{1,3})';
	const METADATA_FIELD_TEST_DATA_MINIMUM_FORMAT = 8;
	const METADATA_FIELD_TEST_DATA_MAXIMUM_FORMAT = 150;
	
	const ENTRY_NAME_MINIMUM_LENGTH = 3;
	const ENTRY_NAME_MAXIMUM_LENGTH = 10;
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return ExampleDistributionPlugin::getProvider();
	}

			
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 * Shows how to report validation errors
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		// get validation errors from parent class
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
	
		// get the entry object
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
		if(!$entry)
		{
			KalturaLog::err("Entry [" . $entryDistribution->getEntryId() . "] not found");
			return $validationErrors;
		}
		
		// validate entry name length
		if(strlen($entry->getName()) < self::ENTRY_NAME_MINIMUM_LENGTH)
		{
			$description = 'entry name length must be between ' . self::ENTRY_NAME_MINIMUM_LENGTH . ' and ' . self::ENTRY_NAME_MAXIMUM_LENGTH;
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_TEST_DATA, $description);
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		if(strlen($entry->getName()) > self::ENTRY_NAME_MAXIMUM_LENGTH)
		{
			$description = 'entry name length must be between ' . self::ENTRY_NAME_MINIMUM_LENGTH . ' and ' . self::ENTRY_NAME_MAXIMUM_LENGTH;
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_TEST_DATA, $description);
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MAXIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		// get all metadata objects that related to the entry
		$metadatas = MetadataPeer::retrieveAllByObject(Metadata::TYPE_ENTRY, $entryDistribution->getEntryId());
		if(!count($metadatas))
		{
			$description = 'field is missing because there is no metadata object defined';
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_TEST_DATA, $description);
		}
		
		// get all fields from all metadata profile for the testData key
		$metadataProfileFields = MetadataProfileFieldPeer::retrieveByPartnerAndKey($this->getPartnerId(), self::METADATA_FIELD_TEST_DATA);
		if(!count($metadataProfileFields))
		{
			$description = 'field is not defined in any of the metadata profiles';
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_TEST_DATA, $description);
		}
		elseif(count($metadatas))
		{
			foreach($metadataProfileFields as $metadataProfileField)
			{
				// get the values for the testData key from all metadata objects
				$values = $this->findMetadataValue($metadatas, self::METADATA_FIELD_TEST_DATA);
				if(!count($values))
				{
					$description = 'field is not defined in any of the metadata objects';
					$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_TEST_DATA, $description);
					continue;
				}	
			
				foreach($values as $value)
				{
					// validate that the field is not empty
					if(!strlen($value))
					{
						$description = 'field is empty';
						$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_TEST_DATA, $description);
						$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
						$validationErrors[] = $validationError;
						continue;
					}
				
					// match the value to the required format
					$matches = null;
					$isMatched = preg_match('/' . self::METADATA_FIELD_TEST_DATA_FORMAT . '/', $value, $matches);
					
					if(!$isMatched)
					{
						$description = 'test data must match the required format';
						$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_TEST_DATA, $description);
						$validationError->setValidationErrorType(DistributionValidationErrorType::INVALID_FORMAT);
						$validationError->setValidationErrorParam(self::METADATA_FIELD_TEST_DATA_FORMAT);
						$validationErrors[] = $validationError;
					}
					
					$formatNumber = (int) $matches[1];
					if($formatNumber < self::METADATA_FIELD_TEST_DATA_MINIMUM_FORMAT || $formatNumber > self::METADATA_FIELD_TEST_DATA_MAXIMUM_FORMAT)
					{
						$description = 'format number must be between ' . self::METADATA_FIELD_TEST_DATA_MINIMUM_FORMAT . ' and ' . self::METADATA_FIELD_TEST_DATA_MAXIMUM_FORMAT;
						$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_TEST_DATA, $description);
						$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
						$validationErrors[] = $validationError;
					}
				}
			}
		}
		
		return $validationErrors;
	}
	
	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getAccountId()				{return $this->getFromCustomData(self::CUSTOM_DATA_ACCOUNT_ID);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setAccountId($v)			{$this->putInCustomData(self::CUSTOM_DATA_ACCOUNT_ID, $v);}
}
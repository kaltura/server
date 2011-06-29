<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage model
 */
class DailymotionDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USER = 'user';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';	


	const METADATA_FIELD_CATEGORY = 'DailymotionCategory';
	const METADATA_FIELD_DESCRIPTION = 'DailymotionDescription';
	const METADATA_FIELD_TAGS = 'DailymotionKeywords';

	const ENTRY_NAME_MINIMUM_LENGTH = 1;
	const ENTRY_NAME_MAXIMUM_LENGTH = 60;
	const ENTRY_DESCRIPTION_MINIMUM_LENGTH = 1;
	const ENTRY_DESCRIPTION_MAXIMUM_LENGTH = 2000;
	const ENTRY_TAGS_MINIMUM_COUNT = 2;
	const ENTRY_TAGS_MAXIMUM_LENGTH = 250;
	const ENTRY_TAG_MINIMUM_LENGTH = 3;
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return DailymotionDistributionPlugin::getProvider();
	}
			
	/**
	 * @param EntryDistribution $entryDistribution
	 * @param int $action enum from DistributionAction
	 * @param array $validationErrors
	 * @param bool $validateDescription
	 * @param bool $validateTags
	 * @return array
	 */
	public function validateMetadataForSubmission(EntryDistribution $entryDistribution, $action, array $validationErrors, &$validateDescription, &$validateTags)
	{
		$validateDescription = true;
		$validateTags = true;
		
		if(!class_exists('MetadataProfile'))
			return $validationErrors;
			
		$metadataProfileId = $this->getMetadataProfileId();
		if(!$metadataProfileId)
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_CATEGORY, '');
			return $validationErrors;
		}
		
		$metadataProfileCategoryField = MetadataProfileFieldPeer::retrieveByMetadataProfileAndKey($metadataProfileId, self::METADATA_FIELD_CATEGORY);
		if(!$metadataProfileCategoryField)
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_CATEGORY, '');
			return $validationErrors;
		}
		
		$metadata = MetadataPeer::retrieveByObject($metadataProfileId, Metadata::TYPE_ENTRY, $entryDistribution->getEntryId());
		if(!$metadata)
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_CATEGORY);
			return $validationErrors;
		}
		
		$values = $this->findMetadataValue(array($metadata), self::METADATA_FIELD_CATEGORY);
		
		if(!count($values))
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_CATEGORY, '');
			
		foreach($values as $value)
		{
			if(!strlen($value))
			{
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_CATEGORY, '');
				return $validationErrors;
			}
		}
		
		$metadataProfileCategoryField = MetadataProfileFieldPeer::retrieveByMetadataProfileAndKey($metadataProfileId, self::METADATA_FIELD_DESCRIPTION);
		if($metadataProfileCategoryField)
		{
			$values = $this->findMetadataValue(array($metadata), self::METADATA_FIELD_DESCRIPTION);
			
			if(count($values))
			{	
				foreach($values as $value)
				{
					if(!strlen($value))
						continue;
				
					$validateDescription = false;
					
					// validate entry description minumum length of 1 character
					if(strlen($value) < self::ENTRY_DESCRIPTION_MINIMUM_LENGTH)
					{
						$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_DESCRIPTION, 'Dailymotion description is too short');
						$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
						$validationError->setValidationErrorParam(self::ENTRY_DESCRIPTION_MINIMUM_LENGTH);
						$validationErrors[] = $validationError;
					}
					
					// validate entry description minumum length of 1 character
					if(strlen($value) > self::ENTRY_DESCRIPTION_MAXIMUM_LENGTH)
					{
						$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_DESCRIPTION, 'Dailymotion description is too long');
						$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
						$validationError->setValidationErrorParam(self::ENTRY_DESCRIPTION_MAXIMUM_LENGTH);
						$validationErrors[] = $validationError;
					}
				}
			}
		}
		
		$metadataProfileCategoryField = MetadataProfileFieldPeer::retrieveByMetadataProfileAndKey($metadataProfileId, self::METADATA_FIELD_TAGS);
		if($metadataProfileCategoryField)
		{
			$values = $this->findMetadataValue(array($metadata), self::METADATA_FIELD_TAGS);
			
			if(count($values) && strlen(implode('', $values)))
			{	
				$tags = implode(',', $values);
				$tags = explode(',', $tags);
				foreach($tags as &$temptag)
					$temptag = trim($temptag);
				unset($temptag);
				
				$tagsStr = implode(' , ', $tags);
				$validateTags = false;
			
				if(strlen($tagsStr) > self::ENTRY_TAGS_MAXIMUM_LENGTH)
				{
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_TAGS, 'Dailymotion tags is too long');
					$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
					$validationError->setValidationErrorParam(self::ENTRY_TAGS_MAXIMUM_LENGTH);
					$validationErrors[] = $validationError;
				}
				if(count($tags) < self::ENTRY_TAGS_MINIMUM_COUNT)
				{
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_TAGS, 'Dailymotion tags must contain at least ' . self::ENTRY_TAGS_MINIMUM_COUNT . ' tags');
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam('Dailymotion tags must contain at least ' . self::ENTRY_TAGS_MINIMUM_COUNT . ' tags');
					$validationErrors[] = $validationError;
				}
				foreach($tags as $tag)
				{
					if(strlen($tag) < self::ENTRY_TAG_MINIMUM_LENGTH)
					{
						$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_TAGS, "Dailymotion tag [$tag] must contain at least " . self::ENTRY_TAG_MINIMUM_LENGTH . " characters");
						$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
						$validationError->setValidationErrorParam("Dailymotion tag [$tag] must contain at least " . self::ENTRY_TAG_MINIMUM_LENGTH . " characters");
						$validationErrors[] = $validationError;
					}
				}
			}
		}
		
		return $validationErrors;
	}
			
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
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
		
		$validateDescription = true;
		$validateTags = true;
		$validationErrors = $this->validateMetadataForSubmission($entryDistribution, $action, $validationErrors, $validateDescription, $validateTags);
		
		if($validateDescription)
		{
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
		}
	
		if($validateTags)
		{
			if(!strlen($entry->getTags()))
			{
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, entryPeer::TAGS, 'Tags is empty');
			}
			else
			{
				$tags = explode(',', $entry->getTags());
				foreach($tags as &$temptag)
					$temptag = trim($temptag);
				unset($temptag);
				$tagsStr = implode(' , ', $tags);
				
				if(strlen($tagsStr) > self::ENTRY_TAGS_MAXIMUM_LENGTH)
				{
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::TAGS, 'Entry tags is too long');
					$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
					$validationError->setValidationErrorParam(self::ENTRY_TAGS_MAXIMUM_LENGTH);
					$validationErrors[] = $validationError;
				}
				if(count($tags) < self::ENTRY_TAGS_MINIMUM_COUNT)
				{
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::TAGS, 'Entry tags must contain at least ' . self::ENTRY_TAGS_MINIMUM_COUNT . ' tags');
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam('Entry tags must contain at least ' . self::ENTRY_TAGS_MINIMUM_COUNT . ' tags');
					$validationErrors[] = $validationError;
				}
				foreach($tags as $tag)
				{
					if(strlen($tag) < self::ENTRY_TAG_MINIMUM_LENGTH)
					{
						$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::TAGS, "Entry tag [$tag] must contain at least " . self::ENTRY_TAG_MINIMUM_LENGTH . " characters");
						$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
						$validationError->setValidationErrorParam("Entry tag [$tag] must contain at least " . self::ENTRY_TAG_MINIMUM_LENGTH . " characters");
						$validationErrors[] = $validationError;
					}
				}
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
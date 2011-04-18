<?php
/**
 * @package plugins.comcastDistribution
 * @subpackage model
 */
class ComcastDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_EMAIL = 'email';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_ACCOUNT = 'account';
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';	
	const CUSTOM_DATA_KEYWORDS = 'keywords';
	const CUSTOM_DATA_AUTHOR = 'author';
	const CUSTOM_DATA_ALBUM = 'album';

	const METADATA_FIELD_COPYRIGHT = 'copyright';
	const METADATA_FIELD_LONG_TITLE = 'LongTitle';
	const METADATA_FIELD_SHORT_DESCRIPTION = 'ShortDescription';
	const METADATA_FIELD_RATING = 'ComcastRating';
	const METADATA_FIELD_CATEGORY = 'ComcastCategory';
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return ComcastDistributionPlugin::getProvider();
	}
			
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		if(!class_exists('MetadataProfile'))
			return $validationErrors;
			
		$metadataFields = array(
			self::METADATA_FIELD_COPYRIGHT,
			self::METADATA_FIELD_LONG_TITLE,
			self::METADATA_FIELD_SHORT_DESCRIPTION,
			self::METADATA_FIELD_RATING,
			self::METADATA_FIELD_CATEGORY,
		);
		
		$metadataProfileId = $this->getMetadataProfileId();
		if(!$metadataProfileId)
		{
			foreach($metadataFields as $metadataField)
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $metadataField);
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
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $metadataField);
			return $validationErrors;
		}
		
		foreach($metadataFields as $index => $metadataField)
		{
			$values = $this->findMetadataValue($metadatas, $metadataField);
			
			if(!count($values))
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $metadataField);
				
			foreach($values as $value)
			{
				if(!strlen($value))
				{
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $metadataField);
					$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
					$validationError->setMetadataProfileId($metadataProfileId);
					$validationErrors[] = $validationError;
					break;
				}
			}
		}
		
		return $validationErrors;
	}
	
	public function getEmail()					{return $this->getFromCustomData(self::CUSTOM_DATA_EMAIL);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getAccount()				{return $this->getFromCustomData(self::CUSTOM_DATA_ACCOUNT);}
	public function getMetadataProfileId()		{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	public function getKeywords()				{return $this->getFromCustomData(self::CUSTOM_DATA_KEYWORDS);}
	public function getAuthor()					{return $this->getFromCustomData(self::CUSTOM_DATA_AUTHOR);}
	public function getAlbum()					{return $this->getFromCustomData(self::CUSTOM_DATA_ALBUM);}
	
	public function setEmail($v)				{$this->putInCustomData(self::CUSTOM_DATA_EMAIL, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setAccount($v)				{$this->putInCustomData(self::CUSTOM_DATA_ACCOUNT, $v);}
	public function setMetadataProfileId($v)	{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}
	public function setKeywords($v)				{$this->putInCustomData(self::CUSTOM_DATA_KEYWORDS, $v);}
	public function setAuthor($v)				{$this->putInCustomData(self::CUSTOM_DATA_AUTHOR, $v);}
	public function setAlbum($v)				{$this->putInCustomData(self::CUSTOM_DATA_ALBUM, $v);}
}
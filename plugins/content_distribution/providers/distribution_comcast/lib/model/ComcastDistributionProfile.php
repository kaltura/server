<?php
class ComcastDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_EMAIL = 'email';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_ACCOUNT = 'account';
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';	
	const CUSTOM_DATA_KEYWORDS = 'keywords';
	const CUSTOM_DATA_AUTHOR = 'author';
	const CUSTOM_DATA_ALBUM = 'album';

	const METADATA_FIELD_CATEGORY = 'comcastCategory';
	
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
		
		$metadata = MetadataPeer::retrieveByObject($metadataProfileId, Metadata::TYPE_ENTRY, $entryDistribution->getEntryId());
		if(!$metadata)
		{
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, self::METADATA_FIELD_CATEGORY);
			return $validationErrors;
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
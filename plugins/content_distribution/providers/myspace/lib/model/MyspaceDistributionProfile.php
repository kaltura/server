<?php
/**
 * @package plugins.myspaceDistribution
 * @subpackage model
 */
class MyspaceDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_DOMAIN = 'domain';
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';
	const CUSTOM_DATA_MYSP_FLAVOR_PARAMS_ID = 'myspFlavorParamsId';
	const CUSTOM_DATA_FEED_TITLE = 'feedTitle';
	const CUSTOM_DATA_FEED_DESCRIPTION = 'feedDescription';
	const CUSTOM_DATA_FEED_CONTACT = 'feedContact';
	
	const CUSTOM_DATA_METADATA_LONG_TITLE = 'LongTitle';
	const CUSTOM_DATA_METADATA_LONG_DESCRIPTION = 'LongDescription';
	const CUSTOM_DATA_METADATA_KEYWORDS = 'Keywords';
	

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return MyspaceDistributionPlugin::getProvider();
	}

		/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		if(!class_exists('MetadataProfile'))
			return $validationErrors;
			
		$requiredFields = array(
			self::CUSTOM_DATA_METADATA_KEYWORDS,
			self::CUSTOM_DATA_METADATA_LONG_DESCRIPTION,
			self::CUSTOM_DATA_METADATA_LONG_TITLE,
		);
		
		$metadataProfileId = $this->getMetadataProfileId();
		if(!$metadataProfileId)
		{
			foreach($requiredFields as $field)
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field, "");
			return $validationErrors;
		}
	
		$metadatas = MetadataPeer::retrieveAllByObject(Metadata::TYPE_ENTRY, $entryDistribution->getEntryId());
		if(!count($metadatas))
		{
			foreach($requiredFields as $field)
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field, "");
			return $validationErrors;
		}
		
		foreach($requiredFields as $field)
		{
			$metadataProfileCategoryField = MetadataProfileFieldPeer::retrieveByMetadataProfileAndKey($metadataProfileId, $field, "");
			if(!$metadataProfileCategoryField)
			{
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field);
				continue;
			}
		
			$values = $this->findMetadataValue($metadatas, $field);
			if(!count($values))
			{
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field, "");
				continue;
			}
				
			foreach($values as $value)
			{
				if(!strlen($value))
				{
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $field, "");
					$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
					$validationError->setMetadataProfileId($metadataProfileId);
					$validationErrors[] = $validationError;
					return $validationErrors;
				}
			}
		}
		
		return $validationErrors;
	}
	
	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getDomain()				{return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN);}
	public function getMetadataProfileId()			{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	public function getMyspFlavorParamsId()			{return $this->getFromCustomData(self::CUSTOM_DATA_MYSP_FLAVOR_PARAMS_ID);}
	public function getFeedTitle()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TITLE);}
	public function getFeedDescription()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_DESCRIPTION);}
	public function getFeedContact()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_CONTACT);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setDomain($v)				{$this->putInCustomData(self::CUSTOM_DATA_DOMAIN, $v);}
	public function setMetadataProfileId($v)		{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}
	public function setMyspFlavorParamsId($v)			{$this->putInCustomData(self::CUSTOM_DATA_MYSP_FLAVOR_PARAMS_ID, $v);}
	public function setFeedTitle($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_TITLE, $v);}
	public function setFeedDescription($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_DESCRIPTION, $v);}
	public function setFeedContact($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_CONTACT, $v);}
	
}
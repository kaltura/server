<?php
/**
 * @package plugins.verizonDistribution
 * @subpackage model
 */
class VerizonDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_DOMAIN = 'domain';
	const CUSTOM_DATA_VRZ_FLAVOR_PARAMS_ID = 'vrzFlavorParamsId';
	const CUSTOM_DATA_PROVIDER_NAME = 'providerName';
	const CUSTOM_DATA_PROVIDER_ID = 'providerId';	
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';

	const CUSTOM_DATA_METADATA_SHORT_TITLE = 'ShortTitle';	
	const CUSTOM_DATA_METADATA_SHORT_DESCRIPTION = 'ShortDescription';	
	const CUSTOM_DATA_METADATA_CATEGORY = 'VerizonCategory';	
	
	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return VerizonDistributionPlugin::getProvider();
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
			self::CUSTOM_DATA_METADATA_CATEGORY,
			self::CUSTOM_DATA_METADATA_SHORT_DESCRIPTION,
			self::CUSTOM_DATA_METADATA_SHORT_TITLE,
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
			$metadataProfileCategoryField = MetadataProfileFieldPeer::retrieveByMetadataProfileAndKey($metadataProfileId, $field);
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
	public function getVrzFlavorParamsId()			{return $this->getFromCustomData(self::CUSTOM_DATA_VRZ_FLAVOR_PARAMS_ID);}
	public function getProviderName()			{return $this->getFromCustomData(self::CUSTOM_DATA_PROVIDER_NAME);}
	public function getProviderId()			{return $this->getFromCustomData(self::CUSTOM_DATA_PROVIDER_ID);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setDomain($v)				{$this->putInCustomData(self::CUSTOM_DATA_DOMAIN, $v);}
	public function setMetadataProfileId($v)		{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}	
	public function setVrzFlavorParamsId($v)			{$this->putInCustomData(self::CUSTOM_DATA_VRZ_FLAVOR_PARAMS_ID, $v);}
	public function setProviderName($v)		{$this->putInCustomData(self::CUSTOM_DATA_PROVIDER_NAME, $v);}	
	public function setProviderId($v)			{$this->putInCustomData(self::CUSTOM_DATA_PROVIDER_ID, $v);}
}
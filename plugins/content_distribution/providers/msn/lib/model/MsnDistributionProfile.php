<?php
class MsnDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_DOMAIN = 'domain';
	const CUSTOM_DATA_CS_ID = 'csId';
	const CUSTOM_DATA_SOURCE = 'source';
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';
	const CUSTOM_DATA_MOV_FLAVOR_PARAMS_ID = 'movFlavorParamsId';
	const CUSTOM_DATA_FLV_FLAVOR_PARAMS_ID = 'flvFlavorParamsId';
	const CUSTOM_DATA_WMV_FLAVOR_PARAMS_ID = 'wmvFlavorParamsId';

	const METADATA_FIELD_VIDEO_CAT = 'MSNVideoCat';
	const METADATA_FIELD_VIDEO_TOP = 'MSNVideoTop';
	const METADATA_FIELD_VIDEO_TOP_CAT = 'MSNVideoTopCat';
	const METADATA_FIELD_PUBLIC = 'MSNPublic';
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return MsnDistributionPlugin::getProvider();
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
			self::METADATA_FIELD_VIDEO_CAT,
			self::METADATA_FIELD_VIDEO_TOP,
			self::METADATA_FIELD_VIDEO_TOP_CAT,
			self::METADATA_FIELD_PUBLIC,
		);
		
		$metadataProfileId = $this->getMetadataProfileId();
		if(!$metadataProfileId)
		{
			foreach($requiredFields as $field)
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field);
			return $validationErrors;
		}
	
		$metadatas = MetadataPeer::retrieveAllByObject(Metadata::TYPE_ENTRY, $entryDistribution->getEntryId());
		if(!count($metadatas))
		{
			foreach($requiredFields as $field)
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field);
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
				$validationErrors[] = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $field);
				continue;
			}
				
			foreach($values as $value)
			{
				if(!strlen($value))
				{
					$validationErrors[] = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $field);
					return $validationErrors;
				}
			}
		}
		
		return $validationErrors;
	}

	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getDomain()					{return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN);}
	public function getCsId()					{return $this->getFromCustomData(self::CUSTOM_DATA_CS_ID);}
	public function getSource()					{return $this->getFromCustomData(self::CUSTOM_DATA_SOURCE);}
	public function getMetadataProfileId()		{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	public function getMovFlavorParamsId()		{return $this->getFromCustomData(self::CUSTOM_DATA_MOV_FLAVOR_PARAMS_ID);}
	public function getFlvFlavorParamsId()		{return $this->getFromCustomData(self::CUSTOM_DATA_FLV_FLAVOR_PARAMS_ID);}
	public function getWmvFlavorParamsId()		{return $this->getFromCustomData(self::CUSTOM_DATA_WMV_FLAVOR_PARAMS_ID);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setDomain($v)				{$this->putInCustomData(self::CUSTOM_DATA_DOMAIN, $v);}
	public function setCsId($v)					{$this->putInCustomData(self::CUSTOM_DATA_CS_ID, $v);}
	public function setSource($v)				{$this->putInCustomData(self::CUSTOM_DATA_SOURCE, $v);}
	public function setMetadataProfileId($v)	{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}
	public function setMovFlavorParamsId($v)	{$this->putInCustomData(self::CUSTOM_DATA_MOV_FLAVOR_PARAMS_ID, $v);}
	public function setFlvFlavorParamsId($v)	{$this->putInCustomData(self::CUSTOM_DATA_FLV_FLAVOR_PARAMS_ID, $v);}
	public function setWmvFlavorParamsId($v)	{$this->putInCustomData(self::CUSTOM_DATA_WMV_FLAVOR_PARAMS_ID, $v);}
	
}
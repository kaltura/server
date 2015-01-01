<?php
/**
 * @package plugins.unicornDistribution
 * @subpackage model
 */
class UnicornDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_DOMAIN_NAME = 'domainName';
	const CUSTOM_DATA_CHANNEL_GUID = 'channelGUID';
	const CUSTOM_DATA_API_HOST_URL = 'apiHostUrl';
	const CUSTOM_DATA_DOMAIN_GUID = 'domainGuid';
	const CUSTOM_DATA_AD_FREE_APPLICATION_GUID = 'adFreeApplicationGuid';
	const CUSTOM_DATA_REMOTE_ASSET_PARAMS_ID = 'remoteAssetParamsId';
	const CUSTOM_DATA_STORAGE_PROFILE_ID = 'storageProfileId';
	
	// validations
	const ITEM_TITLE_MAXIMUM_LENGTH = 50;
	
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		$maxLengthFields = array(UnicornDistributionField::TITLE => self::ITEM_TITLE_MAXIMUM_LENGTH);
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if(!$allFieldValues || !is_array($allFieldValues))
		{
			KalturaLog::err('Error getting field values from entry distribution id [' . $entryDistribution->getId() . '] profile id [' . $this->getId() . ']');
			return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($maxLengthFields, $allFieldValues, $action));
		
		return $validationErrors;
	}
	
	public function getApiHostUrl()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_API_HOST_URL);
	}
	
	public function getDomainGuid()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN_GUID);
	}
	
	public function getAdFreeApplicationGuid()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_AD_FREE_APPLICATION_GUID);
	}
	
	public function getRemoteAssetParamsId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_REMOTE_ASSET_PARAMS_ID);
	}
	
	public function getStorageProfileId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_STORAGE_PROFILE_ID);
	}
	
	public function getPassword()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);
	}
	
	public function getDomainName()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN_NAME);
	}
	
	public function getChannelGUID()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_GUID);
	}
	
	public function getUsername()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);
	}
	
	public function setApiHostUrl($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_API_HOST_URL, $v);
	}
	
	public function setDomainGuid($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DOMAIN_GUID, $v);
	}
	
	public function setAdFreeApplicationGuid($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_AD_FREE_APPLICATION_GUID, $v);
	}
	
	public function setRemoteAssetParamsId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_REMOTE_ASSET_PARAMS_ID, $v);
	}
	
	public function setStorageProfileId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_STORAGE_PROFILE_ID, $v);
	}
	
	public function setPassword($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);
	}
	
	public function setDomainName($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DOMAIN_NAME, $v);
	}
	
	public function setChannelGUID($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_GUID, $v);
	}
	
	public function setUsername($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);
	}
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return UnicornDistributionPlugin::getProvider();
	}
	
	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UnicornDistributionField::CATALOG_GUID);
		$fieldConfig->setUserFriendlyFieldName('catalog GUID');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/CatalogGUID" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UnicornDistributionField::TITLE);
		$fieldConfig->setUserFriendlyFieldName('title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		return $fieldConfigArray;
	}
	
	public function getOptionalAssetDistributionRules()
	{
		$ret = parent::getOptionalAssetDistributionRules();
		if(!class_exists('CaptionPlugin') || !CaptionPlugin::isAllowedPartner($this->getPartnerId()))
		{
			return $ret;
		}
		
		$isCaptionCondition = new kAssetDistributionPropertyCondition();
		$isCaptionCondition->setPropertyName(assetPeer::translateFieldName(assetPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME));
		$isCaptionCondition->setPropertyValue(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
		
		$captionDistributionRule = new kAssetDistributionRule();
		$captionDistributionRule->setAssetDistributionConditions(array($isCaptionCondition));
		$ret[] = $captionDistributionRule;
		
		return $ret;
	}
}
<?php
/**
 * @package plugins.contentDistribution
 * @subpackage model
 */
class GenericDistributionProfile extends DistributionProfile
{
	// TODO - force provider and provider action editable and required fields
	
	const CUSTOM_DATA_GENERIC_PROVIDER_ID = 'genericProviderId';	
	const CUSTOM_DATA_PROTOCOL = 'protocol';
	const CUSTOM_DATA_SERVERURL = 'serverUrl';
	const CUSTOM_DATA_SERVERPATH = 'serverPath';
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_FTP_PASSIVE_MODE = 'ftpPassiveMode';
	const CUSTOM_DATA_HTTP_FIELD_NAME = 'httpFieldName';
	const CUSTOM_DATA_HTTP_FILE_NAME = 'httpFileName';
	const CUSTOM_DATA_FIELD_UPDATE_REQUIRED_ENTRY_FIELDS = "updateRequiredEntryFields";
	const CUSTOM_DATA_FIELD_UPDATE_REQUIRED_METADATA_XPATHS = "updateRequiredMetadataXPaths";
	

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		$genericProviderId = $this->getGenericProviderId();
		if(!$genericProviderId)
			return null;
			
		return GenericDistributionProviderPeer::retrieveByPK($genericProviderId);
	}
	
	public function preSave(PropelPDO $con = null)
	{
		$provider = $this->getProvider();
		if($provider && $provider instanceof GenericDistributionProvider)
		{
			$requiredFlavorParams = $this->getRequiredFlavorParamsIdsArray();
			foreach($provider->getRequiredFlavorParamsIdsArray() as $flavorParamsId)
				if(!in_array($flavorParamsId, $requiredFlavorParams))
					$requiredFlavorParams[] = $flavorParamsId;
			$this->setRequiredFlavorParamsIdsArray($requiredFlavorParams);
			
			$requiredDimensions = $this->getRequiredThumbDimensionsObjects();
			$requiredDimensionsKeys = array();
			foreach($requiredDimensions as $requiredDimension)
				$requiredDimensionsKeys = $requiredDimension->getKey();
				
			foreach($provider->getRequiredThumbDimensionsObjects() as $requiredDimension)
				if(!in_array($requiredDimension->getKey(), $requiredDimensionsKeys))
					$requiredDimensions[] = $requiredDimension;
			$this->setRequiredThumbDimensionsObjects($requiredDimensions);
		}
    	
		return parent::preSave($con);
	}

	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::getSubmitEnabled()
	 */
	public function getSubmitEnabled()
	{
		$provider = $this->getProvider();
		if(!$provider)
			return DistributionProfileActionStatus::DISABLED;
			
		$action = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($provider->getId(), DistributionAction::SUBMIT);
		if(!$action)
			return DistributionProfileActionStatus::DISABLED;
		
		return parent::getSubmitEnabled();
	}

	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::getUpdateEnabled()
	 */
	public function getUpdateEnabled()
	{
		$provider = $this->getProvider();
		if(!$provider)
			return DistributionProfileActionStatus::DISABLED;
	
		$action = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($provider->getId(), DistributionAction::UPDATE);
		if(!$action)
			return DistributionProfileActionStatus::DISABLED;
		
		return parent::getUpdateEnabled();
	}

	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::getDeleteEnabled()
	 */
	public function getDeleteEnabled()
	{
		$provider = $this->getProvider();
		if(!$provider)
			return DistributionProfileActionStatus::DISABLED;
	
		$action = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($provider->getId(), DistributionAction::DELETE);
		if(!$action)
			return DistributionProfileActionStatus::DISABLED;
		
		return parent::getDeleteEnabled();
	}

	/* (non-PHPdoc)
	 * @see BaseDistributionProfile::getReportEnabled()
	 */
	public function getReportEnabled()
	{
		$provider = $this->getProvider();
		if(!$provider)
			return DistributionProfileActionStatus::DISABLED;
	
		$action = GenericDistributionProviderActionPeer::retrieveByProviderAndAction($provider->getId(), DistributionAction::FETCH_REPORT);
		if(!$action)
			return DistributionProfileActionStatus::DISABLED;
		
		return parent::getReportEnabled();
	}
	
	public function getGenericProviderId()			{return $this->getFromCustomData(self::CUSTOM_DATA_GENERIC_PROVIDER_ID);}
		
	public function getProtocol($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_PROTOCOL, $action);}
	public function getServerUrl($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_SERVERURL, $action);}
	public function getServerPath($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_SERVERPATH, $action);}
	public function getUsername($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME, $action);}
	public function getPassword($action)			{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD, $action);}
	public function getFtpPassiveMode($action)		{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PASSIVE_MODE, $action);}
	public function getHttpFieldName($action)		{return $this->getFromCustomData(self::CUSTOM_DATA_HTTP_FIELD_NAME, $action);}
	public function getHttpFileName($action)		{return $this->getFromCustomData(self::CUSTOM_DATA_HTTP_FILE_NAME, $action);}
	public function getUpdateRequiredEntryFields()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_UPDATE_REQUIRED_ENTRY_FIELDS);}
	public function getUpdateRequiredMetadataXPaths(){return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_UPDATE_REQUIRED_METADATA_XPATHS);}
	
	public function setGenericProviderId($v)		{$this->putInCustomData(self::CUSTOM_DATA_GENERIC_PROVIDER_ID, $v);}
		
	public function setProtocol($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_PROTOCOL, $v, $action);}
	public function setServerUrl($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_SERVERURL, $v, $action);}
	public function setServerPath($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_SERVERPATH, $v, $action);}
	public function setUsername($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v, $action);}
	public function setPassword($v, $action)		{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v, $action);}
	public function setFtpPassiveMode($v, $action)	{$this->putInCustomData(self::CUSTOM_DATA_FTP_PASSIVE_MODE, $v, $action);}
	public function setHttpFieldName($v, $action)	{$this->putInCustomData(self::CUSTOM_DATA_HTTP_FIELD_NAME, $v, $action);}
	public function setHttpFileName($v, $action)	{$this->putInCustomData(self::CUSTOM_DATA_HTTP_FILE_NAME, $v, $action);}
	public function setUpdateRequiredEntryFields($v){$this->putInCustomData(self::CUSTOM_DATA_FIELD_UPDATE_REQUIRED_ENTRY_FIELDS, $v);}
	public function setUpdateRequiredMetadataXpaths($v){$this->putInCustomData(self::CUSTOM_DATA_FIELD_UPDATE_REQUIRED_METADATA_XPATHS, $v);}
	
}
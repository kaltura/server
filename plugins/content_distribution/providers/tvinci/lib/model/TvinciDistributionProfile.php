<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage model
 */
class TvinciDistributionProfile extends ConfigurableDistributionProfile
{
 	const CUSTOM_DATA_INGEST_URL = 'ingestUrl';
 	const CUSTOM_DATA_USERNAME = 'username';
 	const CUSTOM_DATA_PASSWORD = 'password';
 	const CUSTOM_DATA_XSLT = 'xsltFile';
	const CUSTOM_ISM_FILENAME = 'ismFileName';
	const CUSTOM_ISM_PPV_MODULE = 'ismPpvModule';
	const CUSTOM_IPADNEW_FILENAME = 'ipadnewFileName';
	const CUSTOM_IPADNEW_PPV_MODULE = 'ipadnewPpvModule';
	const CUSTOM_IPHONENEW_FILENAME = 'iphonenewFileName';
	const CUSTOM_IPHONENEW_PPV_MODULE = 'iphonenewPpvModule';
	const CUSTOM_MBR_FILENAME = 'mbrFileName';
	const CUSTOM_MBR_PPV_MODULE = 'mbrPpvModule';
	const CUSTOM_DASH_FILENAME = 'dashFileName';
	const CUSTOM_DASH_PPV_MODULE = 'dashPpvModule';
	const CUSTOM_WIDEVINE_FILENAME = 'widevineFileName';
	const CUSTOM_WIDEVINE_PPV_MODULE = 'widevinePpvModule';
	const CUSTOM_WIDEVINE_MBR_FILENAME = 'widevineMbrFileName';
	const CUSTOM_WIDEVINE_MBR_PPV_MODULE = 'widevineMbrPpvModule';

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return TvinciDistributionPlugin::getProvider();
	}

	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TvinciDistributionField::CUSTOM);
		$fieldConfig->setUserFriendlyFieldName('Custom Data:');
		$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setIsDefault(true);
		$fieldConfig->setUpdateParams( array( entryPeer::CUSTOM_DATA, entryPeer::DESCRIPTION, entryPeer::NAME));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		return $fieldConfigArray;

	}

	public function getUpdateRequiredMetadataXPaths()
	{
		$metadataConfigArray = parent::getUpdateRequiredMetadataXPaths();
		/* we want any change to the metadata to create an update possibility */
		$metadataConfigArray[] = TvinciDistributionField::META;

		return $metadataConfigArray;

	}



	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);

		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
		$this->validateReferenceId($entryDistribution, $action, $validationErrors);

		return $validationErrors;
	}

	/**
	 * @param EntryDistribution $entryDistribution
	 * @param $action
	 * @param array $validationErrors
	 * since entry distribution and entry are validated in the parent of validateForSubmission we will not add an error for them
	 */
	private function validateReferenceId(EntryDistribution $entryDistribution, $action, array &$validationErrors)
	{

		if ($entryDistribution && $entryDistribution->getEntryId() )
		{
			$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
			if ($entry && (!$entry->getReferenceID()))
			{
				$validationError = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, "Reference ID" , "is a mandatory field");
				$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
				$validationError->setValidationErrorParam("Reference ID is a mandatory field");
				$validationErrors[] = $validationError;
			}
		}
	}
	public function getXsltFile()				{return $this->getFromCustomData(self::CUSTOM_DATA_XSLT);}
	public function setXsltFile($v)				{$this->putInCustomData(self::CUSTOM_DATA_XSLT, $v);}

	public function getIngestUrl()				{return $this->getFromCustomData(self::CUSTOM_DATA_INGEST_URL);}
	public function setIngestUrl($v)			{$this->putInCustomData(self::CUSTOM_DATA_INGEST_URL, $v);}

	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}

	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}

	public function getPublisher()				{return $this->getFromCustomData(self::CUSTOM_DATA_PUBLISHER);}
	public function setPublisher($v)			{$this->putInCustomData(self::CUSTOM_DATA_PUBLISHER, $v);}

	public function getIsmFileName()			{return $this->getFromCustomData(self::CUSTOM_ISM_FILENAME);}
	public function setIsmFileName($v)			{$this->putInCustomData(self::CUSTOM_ISM_FILENAME, $v);}
	public function getIsmPpvModule()			{return $this->getFromCustomData(self::CUSTOM_ISM_PPV_MODULE);}
	public function setIsmPpvModule($v)			{$this->putInCustomData(self::CUSTOM_ISM_PPV_MODULE, $v);}

	public function getMbrFileName()			{return $this->getFromCustomData(self::CUSTOM_MBR_FILENAME);}
	public function setMbrFileName($v)			{$this->putInCustomData(self::CUSTOM_MBR_FILENAME, $v);}
	public function getMbrPpvModule()			{return $this->getFromCustomData(self::CUSTOM_MBR_PPV_MODULE);}
	public function setMbrPpvModule($v)			{$this->putInCustomData(self::CUSTOM_MBR_PPV_MODULE, $v);}

	public function getDashFileName()			{return $this->getFromCustomData(self::CUSTOM_DASH_FILENAME);}
	public function setDashFileName($v)			{$this->putInCustomData(self::CUSTOM_DASH_FILENAME, $v);}
	public function getDashPpvModule()			{return $this->getFromCustomData(self::CUSTOM_DASH_PPV_MODULE);}
	public function setDashPpvModule($v)			{$this->putInCustomData(self::CUSTOM_DASH_PPV_MODULE, $v);}

	public function getIphonenewFileName()		{return $this->getFromCustomData(self::CUSTOM_IPHONENEW_FILENAME);}
	public function setIphonenewFileName($v)	{$this->putInCustomData(self::CUSTOM_IPHONENEW_FILENAME, $v);}
	public function getIphonenewPpvModule()		{return $this->getFromCustomData(self::CUSTOM_IPHONENEW_PPV_MODULE);}
	public function setIphonenewPpvModule($v)	{$this->putInCustomData(self::CUSTOM_IPHONENEW_PPV_MODULE, $v);}

	public function getIpadnewFileName()		{return $this->getFromCustomData(self::CUSTOM_IPADNEW_FILENAME);}
	public function setIpadnewFileName($v)		{$this->putInCustomData(self::CUSTOM_IPADNEW_FILENAME, $v);}
	public function getIpadnewPpvModule()		{return $this->getFromCustomData(self::CUSTOM_IPADNEW_PPV_MODULE);}
	public function setIpadnewPpvModule($v)		{$this->putInCustomData(self::CUSTOM_IPADNEW_PPV_MODULE, $v);}

	public function getWidevineFileName()		{return $this->getFromCustomData(self::CUSTOM_WIDEVINE_FILENAME);}
	public function setWidevineFileName($v)		{$this->putInCustomData(self::CUSTOM_WIDEVINE_FILENAME, $v);}
	public function getWidevinePpvModule()		{return $this->getFromCustomData(self::CUSTOM_WIDEVINE_PPV_MODULE);}
	public function setWidevinePpvModule($v)	{$this->putInCustomData(self::CUSTOM_WIDEVINE_PPV_MODULE, $v);}

	public function getWidevineMbrFileName()	{return $this->getFromCustomData(self::CUSTOM_WIDEVINE_MBR_FILENAME);}
	public function setWidevineMbrFileName($v)	{$this->putInCustomData(self::CUSTOM_WIDEVINE_MBR_FILENAME, $v);}
	public function getWidevineMbrPpvModule()	{return $this->getFromCustomData(self::CUSTOM_WIDEVINE_MBR_PPV_MODULE);}
	public function setWidevineMbrPpvModule($v)	{$this->putInCustomData(self::CUSTOM_WIDEVINE_MBR_PPV_MODULE, $v);}
}

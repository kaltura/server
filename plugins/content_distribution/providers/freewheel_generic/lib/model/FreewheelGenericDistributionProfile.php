<?php
/**
 * @package plugins.freewheelGenericDistribution
 * @subpackage model
 */
class FreewheelGenericDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_APIKEY = 'apikey';
	const CUSTOM_DATA_EMAIL = 'email';
	const CUSTOM_DATA_SFTP_LOGIN = 'sftpLogin';
	const CUSTOM_DATA_SFTP_PASS = 'sftpPass';
	const CUSTOM_DATA_CONTENT_OWNER = 'contentOwner';
	const CUSTOM_DATA_UPSTREAM_VIDEO_ID = 'upstreamVideoId';
	const CUSTOM_DATA_UPSTREAM_NETWORK_NAME = 'upstreamNetworkName';
	const CUSTOM_DATA_UPSTREAM_NETWORK_ID = 'upstreamNetworkId';
	const CUSTOM_DATA_CATEGORY_ID = 'categoryId';
	const CUSTOM_DATA_REPLACE_GROUP = 'fwReplaceGroup';
	const CUSTOM_DATA_REPLACE_AIR_DATES = 'fwReplaceAirDates';
	
	protected $maxLengthValidation= array (
		FreewheelGenericDistributionField::FWTITLES_EPISODE_TITLE1 => 255,
		FreewheelGenericDistributionField::FWTITLES_EPISODE_TITLE2 => 255,
		FreewheelGenericDistributionField::FWDESCRIPTIONS_SERIES => 1024,
		FreewheelGenericDistributionField::FWDESCRIPTIONS_EPISODE => 2048,
	);
	
	protected $inListOrNullValidation = array (
	);
	
	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::VIDEO_ID);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_EPISODE_TITLE1);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(name)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_EPISODE_TITLE2);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_SERIES);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_SEASON);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP1);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP2);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP3);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP4);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP5);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP6);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP7);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP8);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP9);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWTITLES_GROUP10);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWDESCRIPTIONS_SERIES);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWDESCRIPTIONS_EPISODE);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::GENRE);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::RATING);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::DATE_AVAILABLE_START);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::DATE_AVAILABLE_END);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::DATE_ISSUED);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::DATE_LAST_AIRED);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::DURATION);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="floor(sum(media/duration) div 1000)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		// placeholder of metadata values
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FreewheelGenericDistributionField::FWMETADATA);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		return $fieldConfigArray;
	}

	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) 
		{
			KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
			return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($this->maxLengthValidation, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($this->inListOrNullValidation, $allFieldValues, $action));

		return $validationErrors;
	}
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return FreewheelGenericDistributionPlugin::getProvider();
	}

	public function getApiKey()					{return $this->getFromCustomData(self::CUSTOM_DATA_APIKEY);}
	public function getEmail()					{return $this->getFromCustomData(self::CUSTOM_DATA_EMAIL);}
	public function getSftpPass()				{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PASS);}
	public function getSftpLogin()				{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_LOGIN);}
	public function getReplaceGroup()			{return $this->getFromCustomData(self::CUSTOM_DATA_REPLACE_GROUP);}
	public function getReplaceAirDates()		{return $this->getFromCustomData(self::CUSTOM_DATA_REPLACE_AIR_DATES);}
	public function getContentOwner()			{return $this->getFromCustomData(self::CUSTOM_DATA_CONTENT_OWNER);}
	public function getUpstreamVideoId()		{return $this->getFromCustomData(self::CUSTOM_DATA_UPSTREAM_VIDEO_ID);}
	public function getUpstreamNetworkName()	{return $this->getFromCustomData(self::CUSTOM_DATA_UPSTREAM_NETWORK_NAME);}
	public function getUpstreamNetworkId()		{return $this->getFromCustomData(self::CUSTOM_DATA_UPSTREAM_NETWORK_ID);}
	public function getCategoryId()				{return $this->getFromCustomData(self::CUSTOM_DATA_CATEGORY_ID);}
		
	public function setApiKey($v)				{$this->putInCustomData(self::CUSTOM_DATA_APIKEY, $v);}
	public function setEmail($v)				{$this->putInCustomData(self::CUSTOM_DATA_EMAIL, $v);}
	public function setSftpPass($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PASS, $v);}
	public function setSftpLogin($v)			{$this->putInCustomData(self::CUSTOM_DATA_SFTP_LOGIN, $v);}
	public function setReplaceGroup($v)			{$this->putInCustomData(self::CUSTOM_DATA_REPLACE_GROUP, $v);}
	public function setReplaceAirDates($v)		{$this->putInCustomData(self::CUSTOM_DATA_REPLACE_AIR_DATES, $v);}
	public function setContentOwner($v)			{$this->putInCustomData(self::CUSTOM_DATA_CONTENT_OWNER, $v);}
	public function setUpstreamVideoId($v)		{$this->putInCustomData(self::CUSTOM_DATA_UPSTREAM_VIDEO_ID, $v);}
	public function setUpstreamNetworkName($v)	{$this->putInCustomData(self::CUSTOM_DATA_UPSTREAM_NETWORK_NAME, $v);}
	public function setUpstreamNetworkId($v)	{$this->putInCustomData(self::CUSTOM_DATA_UPSTREAM_NETWORK_ID, $v);}
	public function setCategoryId($v)			{$this->putInCustomData(self::CUSTOM_DATA_CATEGORY_ID, $v);}
}
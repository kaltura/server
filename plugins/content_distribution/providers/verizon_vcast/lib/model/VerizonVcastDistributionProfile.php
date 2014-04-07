<?php
/**
 * @package plugins.verizonVcastDistribution
 * @subpackage model
 */
class VerizonVcastDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_FTP_HOST 				= 'ftpHost';
	const CUSTOM_DATA_FTP_LOGIN 			= 'ftpLogin';
	const CUSTOM_DATA_FTP_PASS 				= 'ftpPass';
	const CUSTOM_DATA_PROVIDER_NAME			= 'providerName';
	const CUSTOM_DATA_PROVIDER_ID 			= 'providerId';
	const CUSTOM_DATA_ENTITLEMENT 			= 'entitlement';
	const CUSTOM_DATA_PRIORITY 				= 'priority';
	const CUSTOM_DATA_ALLOW_STREAMING 		= 'allow_streaming';
	const CUSTOM_DATA_STREAMING_PRICE_CODE 	= 'streaming_price_code';
	const CUSTOM_DATA_ALLOW_DOWNLOAD 		= 'allow_download';
	const CUSTOM_DATA_DOWNLOAD_PRICE_CODE 	= 'download_price_code';
	
	protected $maxLengthValidation= array (
		VerizonVcastDistributionField::TITLE 				=> 128,
		VerizonVcastDistributionField::EXTERNAL_ID 			=> 64,
		VerizonVcastDistributionField::SHORT_DESCRIPTION 	=> 128,
		VerizonVcastDistributionField::DESCRIPTION 			=> 1024,
		VerizonVcastDistributionField::KEYWORDS 			=> 256,
	);
	
	protected $inListOrNullValidation = array (
		VerizonVcastDistributionField::ENTITLEMENT 		=> array('BASIC', 'PREMIUM', 'SUBSCRIPTION'),
		VerizonVcastDistributionField::ALLOW_STREAMING 	=> array('Y', 'N'),
		VerizonVcastDistributionField::ALLOW_DOWNLOAD 	=> array('Y', 'N'),
	);
	
	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::EXTERNAL_ID);
		$fieldConfig->setUserFriendlyFieldName('Entry id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::SHORT_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::KEYWORDS);
		$fieldConfig->setUserFriendlyFieldName('Entry Tags');
		$fieldConfig->setEntryMrssXslt(
					'<xsl:for-each select="tags/tag">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::TAGS));
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::PUB_DATE);
		$fieldConfig->setUserFriendlyFieldName('Entry created at');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(createdAt)" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::CATEGORY);
		$fieldConfig->setUserFriendlyFieldName('Category');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::GENRE);
		$fieldConfig->setUserFriendlyFieldName('Genre');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::RATING);
		$fieldConfig->setUserFriendlyFieldName('Rating');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::COPYRIGHT);
		$fieldConfig->setUserFriendlyFieldName('Copyright');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::ENTITLEMENT);
		$fieldConfig->setUserFriendlyFieldName('Entitlement');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/Entitlement" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::LIVE_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution start date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::END_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution end date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::PRIORITY);
		$fieldConfig->setUserFriendlyFieldName('Priority');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/Priority" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::ALLOW_STREAMING);
		$fieldConfig->setUserFriendlyFieldName('Allow streaming');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/AllowStreaming" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::STREAMING_PRICE_CODE);
		$fieldConfig->setUserFriendlyFieldName('Streaming price code');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/StreamingPriceCode" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::ALLOW_DOWNLOAD);
		$fieldConfig->setUserFriendlyFieldName('Allow download');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/AllowDownload" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::DOWNLOAD_PRICE_CODE);
		$fieldConfig->setUserFriendlyFieldName('Download price code');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/DownloadPriceCode" />');
		//$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER); vcast said that this value should be empty 
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::PROVIDER);
		$fieldConfig->setUserFriendlyFieldName('Provider');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/ProviderName" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VerizonVcastDistributionField::PROVIDER_ID);
		$fieldConfig->setUserFriendlyFieldName('Provider id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/ProviderId" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig(); 
		$fieldConfig->setFieldName(VerizonVcastDistributionField::ALERT_CODE);
		$fieldConfig->setUserFriendlyFieldName('Alert code');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
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
		return VerizonVcastDistributionPlugin::getProvider();
	}

	public function getFtpHost()					{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_HOST);}
	public function getFtpLogin()					{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_LOGIN);}
	public function getFtpPass()					{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PASS);}
	public function getProviderName()				{return $this->getFromCustomData(self::CUSTOM_DATA_PROVIDER_NAME);}
	public function getProviderId()					{return $this->getFromCustomData(self::CUSTOM_DATA_PROVIDER_ID);}
	public function getEntitlement()				{return $this->getFromCustomData(self::CUSTOM_DATA_ENTITLEMENT);}
	public function getPriority()					{return $this->getFromCustomData(self::CUSTOM_DATA_PRIORITY);}
	public function getAllowStreaming()				{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_STREAMING);}
	public function getStreamingPriceCode()			{return $this->getFromCustomData(self::CUSTOM_DATA_STREAMING_PRICE_CODE);}
	public function getAllowDownload()				{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_DOWNLOAD);}
	public function getDownloadPriceCode()			{return $this->getFromCustomData(self::CUSTOM_DATA_DOWNLOAD_PRICE_CODE);}
	
	public function setFtpHost($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_HOST, $v);}
	public function setFtpLogin($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_LOGIN, $v);}
	public function setFtpPass($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_PASS, $v);}
	public function setProviderName($v)				{$this->putInCustomData(self::CUSTOM_DATA_PROVIDER_NAME, $v);}
	public function setProviderId($v)				{$this->putInCustomData(self::CUSTOM_DATA_PROVIDER_ID, $v);}
	public function setEntitlement($v)				{$this->putInCustomData(self::CUSTOM_DATA_ENTITLEMENT, $v);}
	public function setPriority($v)					{$this->putInCustomData(self::CUSTOM_DATA_PRIORITY, $v);}
	public function setAllowStreaming($v)			{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_STREAMING, $v);}
	public function setStreamingPriceCode($v)		{$this->putInCustomData(self::CUSTOM_DATA_STREAMING_PRICE_CODE, $v);}
	public function setAllowDownload($v)			{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_DOWNLOAD, $v);}
	public function setDownloadPriceCode($v)		{$this->putInCustomData(self::CUSTOM_DATA_DOWNLOAD_PRICE_CODE, $v);}
}
<?php
/**
 * @package plugins.quickPlayDistribution
 * @subpackage model
 */
class QuickPlayDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_SFTP_HOST 				= 'sftpHost';
	const CUSTOM_DATA_SFTP_LOGIN 				= 'sftpLogin';
	const CUSTOM_DATA_SFTP_PASS 				= 'sftpPass';
	const CUSTOM_DATA_SFTP_BASE_PATH			= 'sftpBasePath';
	const CUSTOM_DATA_CHANNEL_TITLE 			= 'channelTitle';
	const CUSTOM_DATA_CHANNEL_LINK 				= 'channelLink';
	const CUSTOM_DATA_CHANNEL_DESCRIPTION 		= 'channelDescription';
	const CUSTOM_DATA_CHANNEL_MANAGING_EDITOR 	= 'channelManagingEditor';
	const CUSTOM_DATA_CHANNEL_LANGUAGE 			= 'channelLanguage';
	const CUSTOM_DATA_CHANNEL_IMAGE_TITLE 		= 'channelImageTitle';
	const CUSTOM_DATA_CHANNEL_IMAGE_WIDTH 		= 'channelImageWidth';
	const CUSTOM_DATA_CHANNEL_IMAGE_HEIGHT 		= 'channelImageHeight';
	const CUSTOM_DATA_CHANNEL_IMAGE_LINK 		= 'channelImageLink';
	const CUSTOM_DATA_CHANNEL_IMAGE_URL 		= 'channelImageUrl';
	const CUSTOM_DATA_CHANNEL_COPYRIGHT 		= 'channelCopyright';
	const CUSTOM_DATA_CHANNEL_GENERATOR 		= 'channelGenerator';
	const CUSTOM_DATA_CHANNEL_RATING 			= 'channelRating';
	
	
	protected $maxLengthValidation= array (
		QuickPlayDistributionField::TITLE 				=> 64,
		QuickPlayDistributionField::DESCRIPTION			=> 255,
		QuickPlayDistributionField::GUID				=> 32,
		QuickPlayDistributionField::QPM_KEYWORDS		=> 2048,
		QuickPlayDistributionField::QPM_COPYRIGHT		=> 64,
		QuickPlayDistributionField::QPM_ARTIST			=> 256,
		QuickPlayDistributionField::QPM_DIRECTOR		=> 128,
		QuickPlayDistributionField::QPM_PRODUCER		=> 128,
	);
	
	protected $inListOrNullValidation = array (
		QuickPlayDistributionField::QPM_GENRE 	=> array(
			'Kids', 'Movie', 'Sports', 'News', 'Special', 'Lifestyle', 
			'Action', 'Comedy', 'Drama', 'Reality', 'Horror', 'International', 
			'Music', 'SciFi', 'Suspense'),
		
	);
	
	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::GUID);
		$fieldConfig->setUserFriendlyFieldName('Entry id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::CATEGORY);
		$fieldConfig->setUserFriendlyFieldName('Entry categories');
		$fieldConfig->setEntryMrssXslt(
					'<xsl:for-each select="category">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::PUB_DATE);
		$fieldConfig->setUserFriendlyFieldName('Sunrise');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_KEYWORDS);
		$fieldConfig->setUserFriendlyFieldName('Entry tags');
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
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_PRICE_ID);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay price id');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_UPDATE_DATE);
		$fieldConfig->setUserFriendlyFieldName('Entry updated at');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(updatedAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_EXPIRY_DATE);
		$fieldConfig->setUserFriendlyFieldName('Sunset');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_SORT_ORDER);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay sort order');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_GENRE);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay genre');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_COPYRIGHT);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay copyright');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_ARTIST);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay artist');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_DIRECTOR);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay director');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_PRODUCER);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay producer');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_EXP_DATE_PADDING);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay expiration date padding');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_ON_DEVICE_EXPIRATION_PADDING);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay on device expiration padding');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_ON_DEVICE_EXPIRATION);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay on device expiration');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_GROUP_CATEGORY);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay group category');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_NOTES);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay notes');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_RATING);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay rating');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(QuickPlayDistributionField::QPM_RATING_SCHEMA);
		$fieldConfig->setUserFriendlyFieldName('QuickPlay rating schema');
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
		return QuickPlayDistributionPlugin::getProvider();
	}

	public function getSftpHost()					{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_HOST);}
	public function getSftpLogin()					{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_LOGIN);}
	public function getSftpPass()					{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PASS);}
	public function getSftpBasePath()				{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_BASE_PATH);}
	public function getChannelTitle()				{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_TITLE);}
	public function getChannelLink()				{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_LINK);}
	public function getChannelDescription()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_DESCRIPTION);}
	public function getChannelManagingEditor()		{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_MANAGING_EDITOR);}
	public function getChannelLanguage()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_LANGUAGE);}
	public function getChannelImageTitle()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_TITLE);}
	public function getChannelImageWidth()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_WIDTH);}
	public function getChannelImageHeight()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_HEIGHT);}
	public function getChannelImageLink()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_LINK);}
	public function getChannelImageUrl()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_URL);}
	public function getChannelCopyright()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_COPYRIGHT);}
	public function getChannelGenerator()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_GENERATOR);}
	public function getChannelRating()				{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_RATING);}
	
	public function setSftpHost($v)					{$this->putInCustomData(self::CUSTOM_DATA_SFTP_HOST, $v);}
	public function setSftpLogin($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_LOGIN, $v);}
	public function setSftpPass($v)					{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PASS, $v);}
	public function setSftpBasePath($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_BASE_PATH, $v);}
	public function setChannelTitle($v)				{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_TITLE, $v);}
	public function setChannelLink($v)				{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_LINK, $v);}
	public function setChannelDescription($v)		{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_DESCRIPTION, $v);}
	public function setChannelManagingEditor($v)	{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_MANAGING_EDITOR, $v);}
	public function setChannelLanguage($v)			{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_LANGUAGE, $v);}
	public function setChannelImageTitle($v)		{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_TITLE, $v);}
	public function setChannelImageWidth($v)		{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_WIDTH, $v);}
	public function setChannelImageHeight($v)		{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_HEIGHT, $v);}
	public function setChannelImageLink($v)			{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_LINK, $v);}
	public function setChannelImageUrl($v)			{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_URL, $v);}
	public function setChannelCopyright($v)			{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_COPYRIGHT, $v);}
	public function setChannelGenerator($v)			{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_GENERATOR, $v);}
	public function setChannelRating($v)			{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_RATING, $v);}
}
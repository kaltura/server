<?php
/**
 * @package plugins.uverseDistribution
 * @subpackage model
 */
class UverseDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	
	const CUSTOM_DATA_CHANNEL_TITLE = 'channelTitle';
	const CUSTOM_DATA_CHANNEL_LINK = 'channelLink';
	const CUSTOM_DATA_CHANNEL_DESCRIPTION = 'channelDescription';
	const CUSTOM_DATA_CHANNEL_LANGUAGE = 'channelLanguage';
	const CUSTOM_DATA_CHANNEL_COPYRIGHT = 'channelCopyright';
	const CUSTOM_DATA_CHANNEL_IMAGE_TITLE = 'channelImageTitle';
	const CUSTOM_DATA_CHANNEL_IMAGE_URL = 'channelImageUrl';
	const CUSTOM_DATA_CHANNEL_IMAGE_LINK = 'channelImageLink';
	const CUSTOM_DATA_FTP_HOST = 'channelFtpHost';
	const CUSTOM_DATA_FTP_LOGIN = 'channelFtpLogin';
	const CUSTOM_DATA_FTP_PASSWORD = 'channelFtpPassword';
	
	protected $maxLengthValidation= array (
		UverseDistributionField::ITEM_TITLE => 500,
		UverseDistributionField::ITEM_DESCRIPTION => 1000,
		UverseDistributionField::ITEM_MEDIA_CATEGORY => 250,	
		UverseDistributionField::ITEM_MEDIA_TITLE => 500,
		UverseDistributionField::ITEM_MEDIA_DESCRIPTION => 1000,	
		UverseDistributionField::ITEM_MEDIA_KEYWORDS => 1000,
	);	
	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return UverseDistributionPlugin::getProvider();
	}
	
	public function preSave(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$this->setUniqueHashForFeedUrl(md5(time().rand(0, time())));
		}		
		return parent::preSave($con);
	}
	
	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_GUID);
		$fieldConfig->setUserFriendlyFieldName('Entry id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_LINK);
		$fieldConfig->setUserFriendlyFieldName('Link');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
				
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_MEDIA_RATING);
		$fieldConfig->setUserFriendlyFieldName('Media rating');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_MEDIA_CATEGORY);
		$fieldConfig->setUserFriendlyFieldName('Entry categories');
		$fieldConfig->setEntryMrssXslt(
					'<xsl:for-each select="category">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_PUB_DATE);
		$fieldConfig->setUserFriendlyFieldName('Entry created at');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(createdAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_EXPIRATION_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution end date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_MEDIA_KEYWORDS);
		$fieldConfig->setUserFriendlyFieldName('Entry tags');
		$fieldConfig->setEntryMrssXslt(
					'<xsl:for-each select="tags/tag">
						<xsl:if test="position() &gt; 1">
							<xsl:value-of select="\',\'" />
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_LIVE_ORIGINAL_RELEASE_DATE);
		$fieldConfig->setUserFriendlyFieldName('Live original release date');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_MEDIA_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_MEDIA_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_MEDIA_COPYRIGHT);
		$fieldConfig->setUserFriendlyFieldName('Media copyright');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_MEDIA_COPYRIGHT_URL);
		$fieldConfig->setUserFriendlyFieldName('Media copyright url');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		//attributes
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_THUMBNAIL_CREDIT);
		$fieldConfig->setUserFriendlyFieldName('Thumbnail credit');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseDistributionField::ITEM_CONTENT_LANG);
		$fieldConfig->setUserFriendlyFieldName('Content lang');
		$fieldConfig->setEntryMrssXslt('<xsl:text>en</xsl:text>');
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
		
		return $validationErrors;
	}
	
	public function getFeedUrl()
	{
		$urlParams = array(
			'service' => 'uversedistribution_uverse',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $this->getId(),
			'hash' => $this->getUniqueHashForFeedUrl(),
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');
	}
	
	public function getUniqueHashForFeedUrl()			{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}
	public function getChannelTitle()	 				{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_TITLE);}
	public function getChannelLink()	 				{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_LINK);}
	public function getChannelDescription()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_DESCRIPTION);}
	public function getChannelLanguage()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_LANGUAGE);}
	public function getChannelCopyright()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_COPYRIGHT);}
	public function getChannelImageTitle()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_TITLE);}
	public function getChannelImageUrl()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_URL);}
	public function getChannelImageLink()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_LINK);}
	public function getFtpHost() 				{ return $this->getFromCustomData(self::CUSTOM_DATA_FTP_HOST); }
	public function getFtpLogin() 				{ return $this->getFromCustomData(self::CUSTOM_DATA_FTP_LOGIN); }
	public function getFtpPassword() 			{ return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PASSWORD); }
	
	//custom data setters
	public function setUniqueHashForFeedUrl($v)			{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
	public function setChannelTitle($v)			 		{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_TITLE, $v);}
	public function setChannelLink($v)			 		{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_LINK, $v);}
	public function setChannelDescription($v)			{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_DESCRIPTION, $v);}
	public function setChannelLanguage($v)			 	{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_LANGUAGE, $v);}
	public function setChannelCopyright($v)			 	{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_COPYRIGHT, $v);}
	public function setChannelImageTitle($v)			{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_TITLE, $v);}
	public function setChannelImageUrl($v)			 	{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_URL, $v);}
	public function setChannelImageLink($v)			 	{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_LINK, $v);}
	public function setFtpHost ($v) 			{ $this->putInCustomData(self::CUSTOM_DATA_FTP_HOST, $v); }
	public function setFtpLogin ($v) 			{ $this->putInCustomData(self::CUSTOM_DATA_FTP_LOGIN, $v); }
	public function setFtpPassword ($v) 		{ $this->putInCustomData(self::CUSTOM_DATA_FTP_PASSWORD, $v); }
	
}
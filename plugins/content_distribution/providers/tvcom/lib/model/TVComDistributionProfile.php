<?php
/**
 * @package plugins.tvComDistribution
 * @subpackage model
 */
class TVComDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';
	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	const CUSTOM_DATA_FEED_TITLE = 'feedTitle';
	const CUSTOM_DATA_FEED_LINK = 'feedLink';
	const CUSTOM_DATA_FEED_DESCRIPTION = 'feedDescription';
	const CUSTOM_DATA_FEED_LANGUAGE = 'feedLanguage';
	const CUSTOM_DATA_FEED_COPYRIGHT = 'feedCopyright';
	const CUSTOM_DATA_FEED_IMAGE_TITLE = 'feedImageTitle';
	const CUSTOM_DATA_FEED_IMAGE_URL = 'feedImageUrl';
	const CUSTOM_DATA_FEED_IMAGE_LINK = 'feedImageLink';
	const CUSTOM_DATA_FEED_IMAGE_WIDTH = 'feedImageWidth';
	const CUSTOM_DATA_FEED_IMAGE_HEIGHT = 'feedImageHeight';
	
	const METADATA_FIELD_KEYWORDS = 'TVComKeywords';
	const METADATA_FIELD_DESCRIPTION = 'TVComDescription';

	protected $maxLengthValidation= array (
		TVComDistributionField::MEDIA_TITLE => 128,
		TVComDistributionField::MEDIA_DESCRIPTION => 255,
		TVComDistributionField::ITEM_LINK=> 255,
		TVComDistributionField::MEDIA_RESTRICTION_COUNTRIES => 10,
	);
	
	protected $inListOrNullValidation = array (
		TVComDistributionField::MEDIA_CATEGORY_EPISODE_TYPE => array('clip', 'full', 'interview'),
		TVComDistributionField::MEDIA_RESTRICTION_TYPE => array('allow', 'deny'),
		TVComDistributionField::MEDIA_RATING => array('adult', 'nonadult'),
	);
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return TVComDistributionPlugin::getProvider();

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
		$fieldConfig->setFieldName(TVComDistributionField::GUID_ID);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_KEYWORDS);
		$fieldConfig->setUserFriendlyFieldName('Entry tags');
		$tagsXslt = '<xsl:for-each select="tags/tag"><xsl:if test="position() &gt; 1"><xsl:value-of select="\',\'" /></xsl:if><xsl:value-of select="." /></xsl:for-each>';
		$fieldConfig->setEntryMrssXslt($tagsXslt);
		
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::ITEM_PUB_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution start date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::ITEM_EXP_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution end date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::ITEM_LINK);
		$fieldConfig->setUserFriendlyFieldName('TVComLink');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComLink" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_COPYRIGHT);
		$fieldConfig->setUserFriendlyFieldName('TVComCopyright');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComCopyright" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_RATING);
		$fieldConfig->setUserFriendlyFieldName('TVComRating');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComRating" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_RESTRICTION_TYPE);
		$fieldConfig->setUserFriendlyFieldName('TVComRestrictionType');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComRestrictionType" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_RESTRICTION_COUNTRIES);
		$fieldConfig->setUserFriendlyFieldName('TVComRestrictionCountries');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComRestrictionCountries" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_CATEGORY_SHOW_TMSID);
		$fieldConfig->setUserFriendlyFieldName('TVComShowTMSID');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComShowTMSID" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_CATEGORY_SHOW_TMSID_LABEL);
		$fieldConfig->setUserFriendlyFieldName('TVComShowTMSIDLabel');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComShowTMSIDLabel" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_CATEGORY_EPISODE_TMSID);
		$fieldConfig->setUserFriendlyFieldName('TVComEpisodeTMSID');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComEpisodeTMSID" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_CATEGORY_EPISODE_TMSID_LABEL);
		$fieldConfig->setUserFriendlyFieldName('TVComEpisodeTMSIDLabel');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComEpisodeTMSIDLabel" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_CATEGORY_EPISODE_TYPE);
		$fieldConfig->setUserFriendlyFieldName('TVComEpisodeType');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComEpisodeType" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_CATEGORY_ORIGINAL_AIR_DATE);
		$fieldConfig->setUserFriendlyFieldName('TVComOriginalAirDate');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComOriginalAirDate" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_CATEGORY_VIDEO_FORMAT);
		$fieldConfig->setUserFriendlyFieldName('TVComVideoFormat');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComVideoFormat" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_CATEGORY_SEASON_NUMBER);
		$fieldConfig->setUserFriendlyFieldName('TVComSeasonNumber');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComSeasonNumber" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TVComDistributionField::MEDIA_CATEGORY_EPISODE_NUMBER);
		$fieldConfig->setUserFriendlyFieldName('TVComEpisodeNumber');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TVComEpisodeNumber" />');
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
	
	public function getFeedUrl()
	{
		$urlParams = array(
			'service' => 'tvcomdistribution_tvcom',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $this->getId(),
			'hash' => $this->getUniqueHashForFeedUrl(),
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');
	}

	public function getMetadataProfileId()		{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	public function setMetadataProfileId($v)	{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}	
	
	public function getUniqueHashForFeedUrl()	{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}
	public function setUniqueHashForFeedUrl($v)	{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
	
	public function getFeedTitle()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TITLE);}
	public function setFeedTitle($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_TITLE, $v);}
	
	public function getFeedLink()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_LINK);}
	public function setFeedLink($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_LINK, $v);}
	
	public function getFeedDescription()		{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_DESCRIPTION);}
	public function setFeedDescription($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_DESCRIPTION, $v);}
	
	public function getFeedLanguage()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_LANGUAGE);}
	public function setFeedLanguage($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_LANGUAGE, $v);}
	
	public function getFeedCopyright()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_COPYRIGHT);}
	public function setFeedCopyright($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_COPYRIGHT, $v);}
	
	public function getFeedImageTitle()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_IMAGE_TITLE);}
	public function setFeedImageTitle($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_IMAGE_TITLE, $v);}
	
	public function getFeedImageUrl()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_IMAGE_URL);}
	public function setFeedImageUrl($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_IMAGE_URL, $v);}
	
	public function getFeedImageLink()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_IMAGE_LINK);}
	public function setFeedImageLink($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_IMAGE_LINK, $v);}
	
	public function getFeedImageWidth()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_IMAGE_WIDTH);}
	public function setFeedImageWidth($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_IMAGE_WIDTH, $v);}
	
	public function getFeedImageHeight()		{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_IMAGE_HEIGHT);}
	public function setFeedImageHeight($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_IMAGE_HEIGHT, $v);}
}
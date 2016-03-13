<?php
/**
 * @package plugins.comcastMrssDistribution
 * @subpackage model
 */
class ComcastMrssDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';
	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	const CUSTOM_DATA_FEED_TITLE = 'feedTitle';
	const CUSTOM_DATA_FEED_LINK = 'feedLink';
	const CUSTOM_DATA_FEED_DESCRIPTION = 'feedDescription';
	const CUSTOM_DATA_FEED_LAST_BUILD_DATE = 'feedLastBuildDate';
	const CUSTOM_DATA_ITEM_LINK = 'itemLink';
	const CUSTOM_DATA_C_PLATFORM_TV_SERIES = 'cPlatformTVSeries';
	const CUSTOM_DATA_C_PLATFORM_TV_SERIES_FIELD = 'cPlatformTVSeriesField';
	const SHOULD_INCLUDE_CUE_POINTS = 'shouldIncludeCuePoints';
	const SHOULD_INCLUDE_CAPTIONS = 'shouldIncludeCaptions';
	const SHOULD_ADD_THUMB_EXTENSION = 'shouldAddThumbExtension';
	
	protected $maxLengthValidation= array (
		ComcastMrssDistributionField::TITLE => 38,
		ComcastMrssDistributionField::DESCRIPTION => 80,
		ComcastMrssDistributionField::LINK => 128,
	);
	
	protected $inListOrNullValidation = array (
		ComcastMrssDistributionField::MEDIA_RATING => array(
			'PG', 
			'PG-13', 
			'R', 
			'TV-14', 
			'TV-AO', 
			'TV-G', 
			'TV-MA', 
			'TV-PG', 
			'TV-Y', 
			'TV-Y7', 
			'TV-Y7-FV', 
			'X',
		),
		ComcastMrssDistributionField::MEDIA_CATEGORY => array(
			'TV Preview',
			'TV Interview',
			'TV News',
			'TV Featurette',
			'TV Sneak Peek',
			'TV Full Episode',
			'MOVIE Trailer',
			'MOVIE Interview',
			'MOVIE News',
			'MOVIE Featurette',
			'MOVIE Sneak Peek',
			'MOVIE Full Feature',
		),
	);
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return ComcastMrssDistributionPlugin::getProvider();

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
		$fieldConfig->setFieldName(ComcastMrssDistributionField::GUID_ID);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::LINK);
		$fieldConfig->setUserFriendlyFieldName('ComcastLink');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/ComcastLink" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::PUB_DATE);
		$fieldConfig->setUserFriendlyFieldName('Entry created at');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(createdAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::LAST_BUILD_DATE);
		$fieldConfig->setUserFriendlyFieldName('Entry updated at');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(updatedAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::MEDIA_RATING);
		$fieldConfig->setUserFriendlyFieldName('ComcastRating');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/ComcastRating" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::MEDIA_KEYWORDS);
		$fieldConfig->setUserFriendlyFieldName('Entry tags');
		$tagsXslt = '<xsl:for-each select="tags/tag"><xsl:if test="position() &gt; 1"><xsl:value-of select="\',\'" /></xsl:if><xsl:value-of select="." /></xsl:for-each>';
		$fieldConfig->setEntryMrssXslt($tagsXslt);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::MEDIA_CATEGORY);
		$fieldConfig->setUserFriendlyFieldName('ComcastCategory');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/ComcastCategory" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::COMCAST_TV_SERIES);
		$fieldConfig->setUserFriendlyFieldName('ComcastTVSeries');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/ComcastTVSeries" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::COMCAST_LINK);
		$fieldConfig->setUserFriendlyFieldName('ComcastLink');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/ComcastLink" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::COMCAST_BRAND);
		$fieldConfig->setUserFriendlyFieldName('ComcastBrand');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/ComcastBrand" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::COMCAST_VIDEO_CONTENT_TYPE);
		$fieldConfig->setUserFriendlyFieldName('ComcastVideoContentType');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::START_TIME);
		$fieldConfig->setUserFriendlyFieldName('Distribution start date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(ComcastMrssDistributionField::END_TIME);
		$fieldConfig->setUserFriendlyFieldName('Distribution end date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
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
			'service' => 'comcastmrssdistribution_comcastmrss',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $this->getId(),
			'hash' => $this->getUniqueHashForFeedUrl(),
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');
	}

	public function getMetadataProfileId()			{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	public function setMetadataProfileId($v)		{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}	
	
	public function getUniqueHashForFeedUrl()		{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}
	public function setUniqueHashForFeedUrl($v)		{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
	
	public function getFeedTitle()					{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TITLE);}
	public function setFeedTitle($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_TITLE, $v);}
	
	public function getFeedLink()					{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_LINK);}
	public function setFeedLink($v)					{$this->putInCustomData(self::CUSTOM_DATA_FEED_LINK, $v);}
	
	public function getFeedDescription()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_DESCRIPTION);}
	public function setFeedDescription($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_DESCRIPTION, $v);}
	
	public function getFeedLastBuildDate()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_LAST_BUILD_DATE);}
	public function setFeedLastBuildDate($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_LAST_BUILD_DATE, $v);}
	
	public function getCPlatformTvSeries()			{return $this->getFromCustomData(self::CUSTOM_DATA_C_PLATFORM_TV_SERIES);}
	public function setCPlatformTvSeries($v)		{$this->putInCustomData(self::CUSTOM_DATA_C_PLATFORM_TV_SERIES, $v);}
	
	public function getCPlatformTvSeriesField()		{return $this->getFromCustomData(self::CUSTOM_DATA_C_PLATFORM_TV_SERIES_FIELD);}
	public function setCPlatformTvSeriesField($v)	{$this->putInCustomData(self::CUSTOM_DATA_C_PLATFORM_TV_SERIES_FIELD, $v);}
	
	public function getItemLink()					{return $this->getFromCustomData(self::CUSTOM_DATA_ITEM_LINK);}
	public function setItemLink($v)					{$this->putInCustomData(self::CUSTOM_DATA_ITEM_LINK, $v);}
	
	public function getShouldIncludeCuePoints ()	{return $this->getFromCustomData(self::SHOULD_INCLUDE_CUE_POINTS);}
	public function setShouldIncludeCuePoints ($v)	{$this->putInCustomData(self::SHOULD_INCLUDE_CUE_POINTS, $v);}
	
	public function getShouldIncludeCaptions ()	{return $this->getFromCustomData(self::SHOULD_INCLUDE_CAPTIONS);}
	public function setShouldIncludeCaptions ($v)	{$this->putInCustomData(self::SHOULD_INCLUDE_CAPTIONS, $v);}
	
	public function getShouldAddThumbExtension ()	{return $this->getFromCustomData(self::SHOULD_ADD_THUMB_EXTENSION);}
 	public function setShouldAddThumbExtension ($v)	{$this->putInCustomData(self::SHOULD_ADD_THUMB_EXTENSION, $v);}
}
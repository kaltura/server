<?php
/**
 * @package plugins.doubleClickDistribution
 * @subpackage model
 */
class DoubleClickDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	const CUSTOM_DATA_CHANNEL_TITLE = 'channelTitle';
	const CUSTOM_DATA_CHANNEL_DESCRIPTION = 'channelDescription';
	const CUSTOM_DATA_CHANNEL_LINK = 'channelLink';
	const CUSTOM_DATA_CUE_POINTS_PROVIDER = 'cuePointsProvider';
	const CUSTOM_DATA_ITEMS_PER_PAGE = 'itemsPerPage';
	const CUSTOM_DATA_IGNORE_SCHEDULING_IN_FEED = 'ignoreSchedulingInFeed';

	protected $maxLengthValidation= array (
	);
	
	protected $inListOrNullValidation = array (
	);
	
	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::GUID);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::PUB_DATE);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="createdAt" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::TITLE);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(name)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::DESCRIPTION);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::LINK);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::AUTHOR);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(userId)" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::KEYWORDS);
		$fieldConfig->setEntryMrssXslt(
					'<xsl:for-each select="tags/tag">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::CATEGORIES);
		$fieldConfig->setEntryMrssXslt(
					'<xsl:for-each select="category">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::MONETIZABLE);
		$fieldConfig->setEntryMrssXslt('<xsl:text>false</xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::TOTAL_VIEW_COUNT);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::PREVIOUS_DAY_VIEW_COUNT);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::PREVIOUS_WEEK_VIEW_COUNT);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::FAVORITE_COUNT);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::LIKE_COUNT);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::DISLIKE_COUNT);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		// placeholder of metadata values
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::DFP_METADATA);
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::LAST_MODIFIED_DATE);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="updatedAt" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::LAST_MEDIA_MODIFIED_DATE);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="updatedAt" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::STATUS);
		$fieldConfig->setEntryMrssXslt('<xsl:text>blocked</xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DoubleClickDistributionField::FW_CAID);
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
		return DoubleClickDistributionPlugin::getProvider();
	}
	
	public function preSave(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$this->setUniqueHashForFeedUrl(md5(time().rand(0, time())));
		}
		
		return parent::preSave($con);
	}

	public function getFeedUrl()
	{
		$urlParams = array(
			'service' => 'doubleclickdistribution_doubleclick',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $this->getId(),
			'hash' => $this->getUniqueHashForFeedUrl(),
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');
	}
	
	public function getUniqueHashForFeedUrl()		{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}
	public function setUniqueHashForFeedUrl($v)		{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
	
	public function getChannelTitle()				{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_TITLE);}
	public function setChannelTitle($v)				{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_TITLE, $v);}
	
	public function getChannelLink()				{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_LINK);}
	public function setChannelLink($v)				{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_LINK, $v);}
	
	public function getChannelDescription()			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_DESCRIPTION);}
	public function setChannelDescription($v)		{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_DESCRIPTION, $v);}
	
	public function getCuePointsProvider()			{return $this->getFromCustomData(self::CUSTOM_DATA_CUE_POINTS_PROVIDER);}
	public function setCuePointsProvider($v)		{$this->putInCustomData(self::CUSTOM_DATA_CUE_POINTS_PROVIDER, $v);}
	
	public function getItemsPerPage()				{return $this->getFromCustomData(self::CUSTOM_DATA_ITEMS_PER_PAGE);}
	public function setItemsPerPage($v)				{$this->putInCustomData(self::CUSTOM_DATA_ITEMS_PER_PAGE, $v);}

	public function getIgnoreSchedulingInFeed()		{return (bool)$this->getFromCustomData(self::CUSTOM_DATA_IGNORE_SCHEDULING_IN_FEED);}
	public function setIgnoreSchedulingInFeed($v)	{$this->putInCustomData(self::CUSTOM_DATA_IGNORE_SCHEDULING_IN_FEED, (bool)$v);}
}
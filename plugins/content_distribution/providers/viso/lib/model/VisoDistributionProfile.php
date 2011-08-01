<?php
/**
 * @package plugins.visoDistribution
 * @subpackage model
 */
class VisoDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	const CUSTOM_DATA_FEED_TITLE = 'feedTitle';
	const CUSTOM_DATA_FEED_LINK = 'feedLink';
	const CUSTOM_DATA_FEED_DESCRIPTION = 'feedDescription';
	
	protected $maxLengthValidation= array (
	);
	
	protected $inListOrNullValidation = array (
		VisoDistributionField::MEDIA_RATING => array(
			'TV-Y', 
			'TV-Y7', 
			'TV-Y7-FV', 
			'TV-G', 
			'TV-PG', 
			'TV-14', 
			'TV-MA',
		),
	);
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return VisoDistributionPlugin::getProvider();

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
		$fieldConfigArray = array();
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VisoDistributionField::GUID_ID);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VisoDistributionField::ITEM_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VisoDistributionField::ITEM_LINK);
		$fieldConfig->setUserFriendlyFieldName('VisoLink');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/VisoLink" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VisoDistributionField::MEDIA_PLAYER);
		$fieldConfig->setUserFriendlyFieldName('VisoPlayer');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/VisoPlayer" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VisoDistributionField::MEDIA_RATING);
		$fieldConfig->setUserFriendlyFieldName('VisoRating');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/VisoRating" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VisoDistributionField::MEDIA_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(VisoDistributionField::MEDIA_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
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
			'service' => 'visodistribution_viso',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $this->getId(),
			'hash' => $this->getUniqueHashForFeedUrl(),
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');
	}

	public function getUniqueHashForFeedUrl()	{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}
	public function setUniqueHashForFeedUrl($v)	{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
	
	public function getFeedTitle()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TITLE);}
	public function setFeedTitle($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_TITLE, $v);}
	
	public function getFeedLink()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_LINK);}
	public function setFeedLink($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_LINK, $v);}
	
	public function getFeedDescription()		{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_DESCRIPTION);}
	public function setFeedDescription($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_DESCRIPTION, $v);}
}
<?php
/**
 * @package plugins.avnDistribution
 * @subpackage model
 */
class AvnDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	const CUSTOM_DATA_FEED_TITLE = 'feedTitle';
	const SPECIAL_THUMBNAIL_WIDTH = 640;
	const SPECIAL_THUMBNAIL_HEIGHT = 480;
	
	protected $maxLengthValidation= array (
		AvnDistributionField::TITLE => 30,
		AvnDistributionField::DESCRIPTION => 160,
		AvnDistributionField::HEADER => 36,
		AvnDistributionField::SUB_HEADER => 30,
	);
	
	protected $inListOrNullValidation = array (
	);
	
	protected $overrideRequiredThumbs;
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return AvnDistributionPlugin::getProvider();

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
		$fieldConfig->setFieldName(AvnDistributionField::GUID);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::PUB_DATE);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(updatedAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::TITLE);
		$fieldConfig->setUserFriendlyFieldName('AVN Title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AVNTitle" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('AVN Description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AVNDescription" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::LINK);
		$fieldConfig->setUserFriendlyFieldName('AVN Link');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AVNLink" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::CATEGORY);
		$fieldConfig->setUserFriendlyFieldName('AVN Category');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AVNCategory" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::IS_ON_MAIN);
		$fieldConfig->setUserFriendlyFieldName('AVN Is On Main');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AVNIsOnMain" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::ORDER_MAIN);
		$fieldConfig->setUserFriendlyFieldName('AVN Order Main');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AVNOrderMain" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::ORDER_SUB);
		$fieldConfig->setUserFriendlyFieldName('AVN Order Sub');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AVNOrderSub" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::HEADER);
		$fieldConfig->setUserFriendlyFieldName('AVN Header');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AVNHeader" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AvnDistributionField::SUB_HEADER);
		$fieldConfig->setUserFriendlyFieldName('AVN Sub Header');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AVNSubHeader" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		return $fieldConfigArray;
	}

	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		
		// check if this entry is main menu or thank you screen and override the thumbnail validation
		if (isset($allFieldValues[AvnDistributionField::CATEGORY]))
		{ 
			if (in_array(strtolower($allFieldValues[AvnDistributionField::CATEGORY]), array('main menu', 'thank you')))
			{
				$this->overrideRequiredThumbs = new kDistributionThumbDimensions();
				$this->overrideRequiredThumbs->setWidth(self::SPECIAL_THUMBNAIL_WIDTH);
				$this->overrideRequiredThumbs->setHeight(self::SPECIAL_THUMBNAIL_HEIGHT);
			}
		}
		
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		if (!$allFieldValues || !is_array($allFieldValues)) 
		{
			KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
			return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($this->maxLengthValidation, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($this->inListOrNullValidation, $allFieldValues, $action));

		
		
		return $validationErrors;
	}
	
	/**
	 * @return array<kDistributionThumbDimensions>
	 */
	public function getRequiredThumbDimensionsObjects()
	{
		$requiredThumbs = parent::getRequiredThumbDimensionsObjects();
		
		if (!is_null($this->overrideRequiredThumbs))
		{
			$requiredThumbs = array();
			$requiredThumbs[] = $this->overrideRequiredThumbs;
		}
	
		return $requiredThumbs;
	}
	
	public function getFeedUrl()
	{
		$urlParams = array(
			'service' => 'avndistribution_avn',
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
}
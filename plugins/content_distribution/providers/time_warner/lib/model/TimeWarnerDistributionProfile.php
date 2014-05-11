<?php
/**
 * @package plugins.timeWarnerDistribution
 * @subpackage model
 */
class TimeWarnerDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	
	protected $maxLengthValidation= array (
		TimeWarnerDistributionField::TITLE => 100,
		TimeWarnerDistributionField::DESCRIPTION => 600,
		TimeWarnerDistributionField::MEDIA_KEYWORDS => 1000,
		TimeWarnerDistributionField::CABLE_SHORT_DESCRIPTION => 250,
		TimeWarnerDistributionField::CABLE_SHORT_TITLE => 60,
	);
	
	protected $inListOrNullValidation = array (
		TimeWarnerDistributionField::MEDIA_RATING => array(
			'TV-Y',
			'TV-G',
			'TV-PG',
			'TV-14',
			'TV-MA',
		),
	/*	
		TimeWarnerDistributionField::MEDIA_CATEGORY_CT => array(
			'Clip',
			'Movie',
			'Music',
			'News',
			'TV',
		),
		TimeWarnerDistributionField::MEDIA_CATEGORY_TX => array(
			'Clips',
			'Episode',
			'Fulllength',
			'Miniseries',
			'Promo',
		),	    
	);
	protected $multipleInListOrNull = array (
		TimeWarnerDistributionField::MEDIA_CATEGORY_GE => array(
				'Action',
				'Animation',
				'Comedy',
				'Crime',
				'Documentary',
				'Drama',
				'Family', 
				'Food',
				'Health',
				'History',
				'Home',
				'Horror',
				'Reality',
				'Sports',
				'Strange'
			),
	*/
		);
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return TimeWarnerDistributionPlugin::getProvider();

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
		$fieldConfig->setFieldName(TimeWarnerDistributionField::GUID);
		$fieldConfig->setUserFriendlyFieldName('Entry id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::AUTHOR);
		$fieldConfig->setUserFriendlyFieldName('TwAuthor');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwAuthor" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::PUB_DATE);
		$fieldConfig->setUserFriendlyFieldName('Entry updated at');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(updatedAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::START_TIME);
		$fieldConfig->setUserFriendlyFieldName('Distribution start date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);	
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::END_TIME);
		$fieldConfig->setUserFriendlyFieldName('Distribution end date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::MEDIA_COPYRIGHT);
		$fieldConfig->setUserFriendlyFieldName('TwCopyright');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwCopyright" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::MEDIA_KEYWORDS);
		$fieldConfig->setUserFriendlyFieldName('Entry tags');
		$tagsXslt = '<xsl:for-each select="tags/tag"><xsl:if test="position() &gt; 1"><xsl:value-of select="\',\'" /></xsl:if><xsl:value-of select="." /></xsl:for-each>';
		$fieldConfig->setEntryMrssXslt($tagsXslt);
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::MEDIA_RATING);
		$fieldConfig->setUserFriendlyFieldName('TwContentRating');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwContentRating" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::MEDIA_CATEGORY_CT);
		$fieldConfig->setUserFriendlyFieldName('TwCtCategory');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwCtCategory" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::MEDIA_CATEGORY_TX);
		$fieldConfig->setUserFriendlyFieldName('TwTxCategory');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwTxCategory" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::MEDIA_CATEGORY_GE);
		$fieldConfig->setUserFriendlyFieldName('TwGeCategory');		
		$tagsXslt = '<xsl:for-each select="customData/metadata/TwGeCategory"><xsl:if test="position() &gt; 1"><xsl:value-of select="\',\'" /></xsl:if><xsl:value-of select="." /></xsl:for-each>';
		$fieldConfig->setEntryMrssXslt($tagsXslt);		
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::MEDIA_CATEGORY_GR);
		$fieldConfig->setUserFriendlyFieldName('TwGrCategory');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwGrCategory" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::PLMEDIA_APPROVED);
		$fieldConfig->setUserFriendlyFieldName('TwPlMediaApproved');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwPlMediaApproved" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::CABLE_EPISODE_NUMBER);
		$fieldConfig->setUserFriendlyFieldName('TwCableEpisodeNumber');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwCableEpisodeNumber" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::CABLE_EXTERNAL_ID);
		$fieldConfig->setUserFriendlyFieldName('External Id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::CABLE_PRODUCTION_DATE);
		$fieldConfig->setUserFriendlyFieldName('Entry created at');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(createdAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::CABLE_NETWORK);
		$fieldConfig->setUserFriendlyFieldName('TwCableNetwork');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwCableNetwork" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::CABLE_PROVIDER);
		$fieldConfig->setUserFriendlyFieldName('TwCableProvider');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwCableProvider" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::CABLE_SHORT_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('TwCableDescription');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwCableDescription" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::CABLE_SHORT_TITLE);
		$fieldConfig->setUserFriendlyFieldName('TwCableTitle');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwCableTitle" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TimeWarnerDistributionField::CABLE_SHOW_NAME);
		$fieldConfig->setUserFriendlyFieldName('TwCableShowName');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TwCableShowName" />');
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
		//$validationErrors = array_merge($validationErrors, $this->validateMultipleValuesInListOrNull($this->multipleInListOrNull, $allFieldValues, $action));
		
		return $validationErrors;
	}
	
	public function getFeedUrl()
	{
		$urlParams = array(
			'service' => 'timewarnerdistribution_timewarner',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $this->getId(),
			'hash' => $this->getUniqueHashForFeedUrl(),
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');
	}
	
	/**
	 * 
	 * Validate that all the values are from list or they are null
	 * @param unknown_type $fieldArray
	 * @param unknown_type $allFieldValues
	 * @param unknown_type $action
	 */
	/*
	protected function validateMultipleValuesInListOrNull($fieldArray, $allFieldValues, $action)
	{
	    $validationErrors = array();
	    foreach ($fieldArray as $fieldName => $validValues){
		    $value = isset($allFieldValues[$fieldName]) ? $allFieldValues[$fieldName] : null;
		    if (!empty($value)){
		    	$valuesArray = explode(',',$value);
		    	if (!is_array($valuesArray))
				{		        
				    $validationErrors = array_merge($validationErrors, $this->validateInListOrNull($this->multipleInListOrNull, $allFieldValues, $action));
				}
				//if there are severl values: check that each one is valid
				else {					
					foreach($valuesArray as $singleValue){
						if (!empty($singleValue) && !in_array($singleValue, $validValues))
					    {
					        $validValuesStr = implode(',',$validValues);
					        $errorMsg = $this->getUserFriendlyFieldName($fieldName).' value must be in ['.$validValuesStr.']';
			    		    $validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
			    			$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
			    			$validationError->setValidationErrorParam($errorMsg);
			    			$validationError->setDescription($errorMsg);
			    			$validationErrors[] = $validationError;
					    }
					}
				}
		    }
		}
	    return $validationErrors;
	}
	*/
	
	public function getUniqueHashForFeedUrl()		{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}
	public function setUniqueHashForFeedUrl($v)		{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
}
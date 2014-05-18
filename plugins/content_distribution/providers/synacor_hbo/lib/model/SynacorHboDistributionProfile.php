<?php
/**
 * @package plugins.synacorHboDistribution
 * @subpackage model
 */
class SynacorHboDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	const CUSTOM_DATA_FEED_TITLE = 'feedTitle';
	const CUSTOM_DATA_FEED_SUBTITLE = 'feedSubtitle';
	const CUSTOM_DATA_FEED_LINK = 'feedLink';
	const CUSTOM_DATA_FEED_AUTHOR_NAME = 'feedAuthorName';
	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return SynacorHboDistributionPlugin::getProvider();

	}
	
	public function preSave(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$this->setUniqueHashForFeedUrl(md5(time().rand(0, time())));
		}
		
		return parent::preSave($con);
	}
	
	
    public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{	    
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);
	    
	    $profile = DistributionProfilePeer::retrieveByPK($entryDistribution->getDistributionProfileId());
	    if (!$profile)
	    {
	        KalturaLog::err("Distribution  profile [" . $entryDistribution->getDistributionProfileId() . "] not found");
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'profile', 'distribution profile not found');
			return $validationErrors;
	    }
	    
	    if (strlen($profile->getFeedTitle()) <= 0)
	    {
	        $newError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'Feed title');
            $newError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
	        $validationErrors[] = $newError;        
	    }
	    
	    if (strlen($profile->getFeedLink()) <= 0)
	    {
	        $newError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'Feed link');
            $newError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
	        $validationErrors[] = $newError;	
	    }
		
		return $validationErrors;
	}
	

	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_SUMMARY);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_UPDATED);
		$fieldConfig->setUserFriendlyFieldName('Entry Updated At');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(updatedAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_AUTHOR_NAME);
		$fieldConfig->setUserFriendlyFieldName('Feed Author Name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/feed_author_name" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;		
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_ASSET_ID);
		$fieldConfig->setUserFriendlyFieldName('Entry ID');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_OFFERING_START);
		$fieldConfig->setUserFriendlyFieldName('Distribution start date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);	
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_OFFERING_END);
		$fieldConfig->setUserFriendlyFieldName('Distribution end date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_CATEGORY_TERM);
	    $fieldConfig->setUserFriendlyFieldName('Synacor HBO Category');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/SynacorHboCategory" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_GENRE_TERM);
	    $fieldConfig->setUserFriendlyFieldName('Synacor HBO Genre');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/SynacorHboGenre" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_ASSET_TYPE);
	    $fieldConfig->setUserFriendlyFieldName('Synacor HBO Asset Type');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/SynacorHboAssetType" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_RATING);
	    $fieldConfig->setUserFriendlyFieldName('Synacor HBO Rating');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/SynacorHboRating" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_SERIES_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Synacor HBO Series Title');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/SynacorHboSeriesTitle" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(SynacorHboDistributionField::ENTRY_BRAND);
	    $fieldConfig->setUserFriendlyFieldName('Synacor HBO Brand');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/SynacorHboBrand" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    		
		return $fieldConfigArray;
	}

	
	public function getFeedUrl()
	{
		$urlParams = array(
			'service' => 'synacorhbodistribution_synacorhbo',
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
	
	public function getUniqueHashForFeedUrl()		{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}
	public function setUniqueHashForFeedUrl($v)		{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
    
	public function getFeedTitle()		{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TITLE);}
	public function setFeedTitle($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_TITLE, $v);}
	
	public function getFeedSubtitle()		{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_SUBTITLE);}
	public function setFeedSubtitle($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_SUBTITLE, $v);}
	
	public function getFeedLink()		{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_LINK);}
	public function setFeedLink($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_LINK, $v);}

	public function getFeedAuthorName()		{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_AUTHOR_NAME);}
	public function setFeedAuthorName($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_AUTHOR_NAME, $v);}
	
}
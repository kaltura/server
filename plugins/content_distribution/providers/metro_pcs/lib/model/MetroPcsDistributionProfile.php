<?php
/**
 * @package plugins.MetroPcsDistribution
 * @subpackage model
 */
class MetroPcsDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_FTP_HOST 				= 'ftpHost';
	const CUSTOM_DATA_FTP_LOGIN 			= 'ftpLogin';
	const CUSTOM_DATA_FTP_PASS 				= 'ftpPass';
	const CUSTOM_DATA_FTP_PATH				= 'ftpPath';
	//const CUSTOM_DATA_PROVIDER_NAME			= 'providerName';
	const CUSTOM_DATA_PROVIDER_ID 			= 'providerId';
	const CUSTOM_DATA_COPYRIGHT 			= 'copyright';
	const CUSTOM_DATA_ENTITLEMENTS 			= 'entitlements';
	const CUSTOM_DATA_RATING 				= 'rating';
	const CUSTOM_DATA_ITEM_TYPE 			= 'itemType';
	
	//max length validation fields
	const TITLE_MAXIMUM_LENGTH = 128;
	const EXTERNAL_ID_MAXIMUM_LENGTH = 64;
	const SHORT_DESCRIPTION_MAXIMUM_LENGTH = 128;
	const DESCRIPTION_MAXIMUM_LENGTH = 1024;
	const KEYWORDS_MAXIMUM_LENGTH = 256;
	
	//is list or null validation fields
	//const ENTITLEMENTS_VALID_VALUES = array('BASIC', 'PREMIUM', 'SUBSCRIPTION');
	
	protected $maxLengthValidation= array (
		MetroPcsDistributionField::TITLE => self::TITLE_MAXIMUM_LENGTH,
		MetroPcsDistributionField::EXTERNAL_ID => self::EXTERNAL_ID_MAXIMUM_LENGTH,
		MetroPcsDistributionField::SHORT_DESCRIPTION => self::SHORT_DESCRIPTION_MAXIMUM_LENGTH,
		MetroPcsDistributionField::DESCRIPTION => self::DESCRIPTION_MAXIMUM_LENGTH,
		MetroPcsDistributionField::KEYWORDS => self::KEYWORDS_MAXIMUM_LENGTH,			
	);
	
	protected $inListOrNullValidation = array (
		//MetroPcsDistributionField::ENTITLEMENTS => self::ENTITLEMENTS_VALID_VALUES		
	);
	
	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::LINK);
		$fieldConfig->setUserFriendlyFieldName('Link');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsLink" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::EXTERNAL_ID);
		$fieldConfig->setUserFriendlyFieldName('Entry id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::PROVIDER_ID);
		$fieldConfig->setUserFriendlyFieldName('Provider id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/provider_id" />');		
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::SHORT_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::LANGUAGE);
		$fieldConfig->setUserFriendlyFieldName('Language');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsLanguage" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::MANAGING_EDITOR);
		$fieldConfig->setUserFriendlyFieldName('Managing Editor');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsManagingEditor" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::PUB_DATE);
		$fieldConfig->setUserFriendlyFieldName('Created Date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(createdAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::CATEGORY);
		$fieldConfig->setUserFriendlyFieldName('Category');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsCategory" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::UPC);
		$fieldConfig->setUserFriendlyFieldName('Upc');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsUpc" />');			
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::ISRC);
		$fieldConfig->setUserFriendlyFieldName('Isrc');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsIsrc" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::PROGRAM);
		$fieldConfig->setUserFriendlyFieldName('Program');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsProgram" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::SEASON_ID);
		$fieldConfig->setUserFriendlyFieldName('Season id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsSeasonId" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::EPISODIC_ID);
		$fieldConfig->setUserFriendlyFieldName('Episodic id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsEpisodicId" />');		
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::CHAPTER_ID);
		$fieldConfig->setUserFriendlyFieldName('Chapter id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsChapterId" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::ARTIST);
		$fieldConfig->setUserFriendlyFieldName('Artist');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsArtist" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::PERFORMER);
		$fieldConfig->setUserFriendlyFieldName('Performer');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsPerformer" />');		
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::DIRECTOR);
		$fieldConfig->setUserFriendlyFieldName('Director');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsDirector" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::STUDIO);
		$fieldConfig->setUserFriendlyFieldName('Studio');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsStudio" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::ORIGINAL_RELEASE);
		$fieldConfig->setUserFriendlyFieldName('Original release');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsOriginalRelease" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::TOP_STORY);
		$fieldConfig->setUserFriendlyFieldName('Top story');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsTopStory" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::SORT_ORDER);
		$fieldConfig->setUserFriendlyFieldName('Sort order');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsSortOrder" />');		
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
				
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::SORT_NAME);
		$fieldConfig->setUserFriendlyFieldName('Sort name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsSortName" />');		
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::GENRE);
		$fieldConfig->setUserFriendlyFieldName('Genre');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsGenre" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::KEYWORDS);
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
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::LOCAL_CODE);
		$fieldConfig->setUserFriendlyFieldName('Local code');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsLocalCode" />');		
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::ENTITLEMENTS);
		$fieldConfig->setUserFriendlyFieldName('Entitlements');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/entitlements" />');		
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::START_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution start date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::END_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution end date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::RATING);
		$fieldConfig->setUserFriendlyFieldName('Rating');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/rating" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::ITEM_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Item title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsItemTitle" />');	
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
				
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::ITEM_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Item description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/MetroPcsItemDescription" />');			
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(MetroPcsDistributionField::ITEM_TYPE);
		$fieldConfig->setUserFriendlyFieldName('Item type');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/item_type" />');		
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
		$validationErrors = array_merge($validationErrors, $this->validateEndDateNotPrecedesStartDatet($allFieldValues, $entryDistribution, $action));
		
		return $validationErrors;
	}
	
	/**
	 * Validate two thumbnails exist
	 * @param $entryDistribution
	 * @param $action
	 */
	private function validateEndDateNotPrecedesStartDatet($allFieldValues, $entryDistribution, $action)
	{
		$validationErrors = array();
		$startDate = $allFieldValues[MetroPcsDistributionField::START_DATE];
		$endDate = $allFieldValues[MetroPcsDistributionField::END_DATE];
		
		if ($startDate && $endDate)
		{
			if ($endDate < $startDate)
			{
				KalturaLog::debug('End Date precedes start date');
				$errorMsg = 'End Date precedes start date';			
	    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);    		
	    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
	    		$validationError->setValidationErrorParam($errorMsg);
	    		$validationError->setDescription($errorMsg);
	    		$validationErrors[] = $validationError;
			}
		}
		return $validationErrors;			
	}	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return MetroPcsDistributionPlugin::getProvider();
	}

	public function getFtpHost()					{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_HOST);}
	public function getFtpLogin()					{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_LOGIN);}
	public function getFtpPass()					{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PASS);}
	public function getFtpPath()					{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PATH);}
	//public function getProviderName()				{return $this->getFromCustomData(self::CUSTOM_DATA_PROVIDER_NAME);}
	public function getProviderId()					{return $this->getFromCustomData(self::CUSTOM_DATA_PROVIDER_ID);}
	public function getCopyright()					{return $this->getFromCustomData(self::CUSTOM_DATA_COPYRIGHT);}
	public function getEntitlements()				{return $this->getFromCustomData(self::CUSTOM_DATA_ENTITLEMENTS);}
	public function getRating()						{return $this->getFromCustomData(self::CUSTOM_DATA_RATING);}
	public function getItemType()					{return $this->getFromCustomData(self::CUSTOM_DATA_ITEM_TYPE);}
		
	public function setFtpHost($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_HOST, $v);}
	public function setFtpLogin($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_LOGIN, $v);}
	public function setFtpPass($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_PASS, $v);}
	public function setFtpPath($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_PATH, $v);}
	//public function setProviderName($v)				{$this->putInCustomData(self::CUSTOM_DATA_PROVIDER_NAME, $v);}
	public function setProviderId($v)				{$this->putInCustomData(self::CUSTOM_DATA_PROVIDER_ID, $v);}
	public function setCopyright($v)				{$this->putInCustomData(self::CUSTOM_DATA_COPYRIGHT, $v);}
	public function setEntitlements($v)				{$this->putInCustomData(self::CUSTOM_DATA_ENTITLEMENTS, $v);}
	public function setRating($v)					{$this->putInCustomData(self::CUSTOM_DATA_RATING, $v);}
	public function setItemType($v)					{$this->putInCustomData(self::CUSTOM_DATA_ITEM_TYPE, $v);}
	
}
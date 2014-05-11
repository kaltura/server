<?php
/**
 * @package plugins.ndnDistribution
 * @subpackage model
 */
class NdnDistributionProfile extends ConfigurableDistributionProfile
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
	const CUSTOM_DATA_ITEM_MEDIA_RATING = 'itemMediaRating';
			
	protected $maxLengthValidation= array (
		NdnDistributionField::ITEM_TITLE => 500,
		NdnDistributionField::ITEM_DESCRIPTION => 1000,
		NdnDistributionField::ITEM_MEDIA_CATEGORY => 250,	
		NdnDistributionField::ITEM_MEDIA_TITLE => 500,
		NdnDistributionField::ITEM_MEDIA_DESCRIPTION => 1000,	
		NdnDistributionField::ITEM_MEDIA_KEYWORDS => 1000,
	);	

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return NdnDistributionPlugin::getProvider();
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
		/*
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Channel title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/channel_title" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_LINK);
		$fieldConfig->setUserFriendlyFieldName('Channel link');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/channel_link" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Channel description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/channel_description" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_LANGUAGE);
		$fieldConfig->setUserFriendlyFieldName('Channel language');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/channel_language" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_COPYRIGHT);
		$fieldConfig->setUserFriendlyFieldName('Channel copyright');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/channel_copyright" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_IMAGE_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Channel image title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/channel_image_title" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_IMAGE_URL);
		$fieldConfig->setUserFriendlyFieldName('Channel image url');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/channel_image_url" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_IMAGE_LINK);
		$fieldConfig->setUserFriendlyFieldName('Channel image link');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/channel_image_link" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_PUB_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution createdAt');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/createdAt" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::CHANNEL_LAST_BUILD_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution updatedAt');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/updatedAt" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		*/
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_GUID);
		$fieldConfig->setUserFriendlyFieldName('Entry id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_LINK);
		$fieldConfig->setUserFriendlyFieldName('Ndn item link');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/NdnLink" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
				
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		//default value is taken from entry distribution and not from custom data
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_MEDIA_RATING);
		$fieldConfig->setUserFriendlyFieldName('Item media rating');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/item_media_rating" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_MEDIA_CATEGORY);
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
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_PUB_DATE);
		$fieldConfig->setUserFriendlyFieldName('Entry created at');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(createdAt)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_EXPIRATION_DATE);
		$fieldConfig->setUserFriendlyFieldName('Distribution end date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_MEDIA_KEYWORDS);
		$fieldConfig->setUserFriendlyFieldName('Entry tags');
		$tagsXslt = '<xsl:for-each select="tags/tag"><xsl:if test="position() &gt; 1"><xsl:value-of select="\',\'" /></xsl:if><xsl:value-of select="." /></xsl:for-each>';
		$fieldConfig->setEntryMrssXslt($tagsXslt);
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_LIVE_ORIGINAL_RELEASE_DATE);
		$fieldConfig->setUserFriendlyFieldName('Ndn item original release date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/NdnOriginalReleaseDate" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_MEDIA_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Media title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_MEDIA_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Media description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_MEDIA_COPYRIGHT);
		$fieldConfig->setUserFriendlyFieldName('Ndn media copyright');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/NdnMediaCopyright" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_MEDIA_COPYRIGHT_URL);
		$fieldConfig->setUserFriendlyFieldName('Ndn media copyright url');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/NdnMediaCopyrightUrl" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		//attributes
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_THUMBNAIL_CREDIT);
		$fieldConfig->setUserFriendlyFieldName('Thumbnail credit');
		$fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(NdnDistributionField::ITEM_CONTENT_LANG);
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
		
		//validate required channel fields
		$channelTitleValue = $this->getChannelTitle();
    	if (empty($channelTitleValue)){   		
    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA,'Channel title profile configuration');
    		$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
    		$validationErrors[] = $validationError;
    	}
		$channelLinkValue = $this->getChannelLink();
    	if (empty($channelLinkValue)){   		
    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA,'Channel link profile configuration');
    		$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
    		$validationErrors[] = $validationError;
    	}
		$channelDescriptionValue = $this->getChannelDescription();
    	if (empty($channelDescriptionValue)){   		
    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA,'Channel description profile configuration');
    		$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
    		$validationErrors[] = $validationError;
    	}
    	
    	//verify channel image title exist if channel image url exists and vice versa
		$channelImageTitleValue = $this->getChannelImageTitle();
		$channelImageUrlValue = $this->getChannelImageUrl();
    	if (!empty($channelImageTitleValue) && empty($channelImageUrlValue)){      			
    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA,'Channel image url profile configuration');
    		$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
    		$validationErrors[] = $validationError;
    	}
		if (!empty($channelImageUrlValue) && empty($channelImageTitleValue)){      			
    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA,'Channel image title profile configuration');
    		$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_EMPTY);
    		$validationErrors[] = $validationError;
    	}    	
    	
    	// validate thumbnail assets
    	$requiredThumbAssetDim = $this->getRequiredThumbDimensions();
		if (empty($requiredThumbAssetDim)) {
    		$errorMsg = 'no thumbnail was configured';
    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);
    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
    		$validationError->setValidationErrorParam($errorMsg);
    		$validationError->setDescription($errorMsg);
    		$validationErrors[] = $validationError;	
    	}
    	
		return $validationErrors;
	}
	
	public function getFeedUrl()
	{
		$urlParams = array(
			'service' => 'ndndistribution_ndn',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $this->getId(),
			'hash' => $this->getUniqueHashForFeedUrl(),
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');
	}	
		
	//custom data getters
	public function getUniqueHashForFeedUrl()			{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}
	public function getChannelTitle()	 				{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_TITLE);}
	public function getChannelLink()	 				{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_LINK);}
	public function getChannelDescription()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_DESCRIPTION);}
	public function getChannelLanguage()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_LANGUAGE);}
	public function getChannelCopyright()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_COPYRIGHT);}
	public function getChannelImageTitle()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_TITLE);}
	public function getChannelImageUrl()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_URL);}
	public function getChannelImageLink()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_IMAGE_LINK);}
	public function getItemMediaRating()	 			{return $this->getFromCustomData(self::CUSTOM_DATA_ITEM_MEDIA_RATING);}
	
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
	public function setItemMediaRating($v)			 	{$this->putInCustomData(self::CUSTOM_DATA_ITEM_MEDIA_RATING, $v);}
	

}
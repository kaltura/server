<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage model
 */
class AttUverseDistributionProfile extends ConfigurableDistributionProfile
{

	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	const CUSTOM_DATA_FTP_HOST = 'ftpHost';
	const CUSTOM_DATA_FTP_USERNAME = 'ftpUsername';
	const CUSTOM_DATA_FTP_PASSWORD = 'ftpPassword';
	const CUSTOM_DATA_FTP_PATH = 'ftpPath';
	const CUSTOM_DATA_CHANNEL_TITLE = 'channelTitle';

			
	// validations
	const ITEM_TITLE_MAXIMUM_LENGTH = 50;
	const ITEM_METADATA_TUNEIN_MAXIMUM_LENGTH = 150;
	const ITEM_DESCRIPTION_MAXIMUM_LENGTH = 1000;
	const ITEM_METADATA_LEGAL_DISCLAIMER_MAXIMUM_LENGTH = 1000;

		
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{	    	    
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);		
		$maxLengthFields = array (
			AttUverseDistributionField::ITEM_TITLE => self::ITEM_TITLE_MAXIMUM_LENGTH,
			AttUverseDistributionField::ITEM_METADATA_TUNEIN => self::ITEM_METADATA_TUNEIN_MAXIMUM_LENGTH,
			AttUverseDistributionField::ITEM_DESCRIPTION => self::ITEM_DESCRIPTION_MAXIMUM_LENGTH,
			AttUverseDistributionField::ITEM_METADATA_LEGAL_DISCLAIMER => self::ITEM_METADATA_LEGAL_DISCLAIMER_MAXIMUM_LENGTH,				    		    
		);
		    		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($maxLengthFields, $allFieldValues, $action));
		//$validationErrors = array_merge($validationErrors, $this->validateThumbnailExist($entryDistribution, $action));
									
		return $validationErrors;
	}
	
	/**
	 * Validate at least one thumbnail exists
	 * @param $entryDistribution
	 * @param $action
	 */
	private function validateThumbnailExist($entryDistribution, $action)
	{
		$validationErrors = array();		
		//Validating thumbnails
		$c = new Criteria();
		$c->addAnd(assetPeer::ID, explode(',',$entryDistribution->getThumbAssetIds()), Criteria::IN);
		$c->addAscendingOrderByColumn(assetPeer::ID);
		$thumbAssets = assetPeer::doSelect($c);		
		if (!count($thumbAssets))
		{
			KalturaLog::debug('Thumbnail is required');
			$errorMsg = 'thumbnail is required';			
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
			'service' => 'attuversedistribution_attuverse',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $this->getId(),
			'hash' => $this->getUniqueHashForFeedUrl(),
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');
	}
	
	public function getUniqueHashForFeedUrl()		{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}	
	public function getFtpPath()					{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PATH);}
	public function getFtpUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_USERNAME);}
	public function getFtpPassword()			 	{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PASSWORD);}
	public function getFtpHost()			 		{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_HOST);}
	public function getChannelTitle()		 		{return $this->getFromCustomData(self::CUSTOM_DATA_CHANNEL_TITLE);}
	
	public function setUniqueHashForFeedUrl($v)		{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
	public function setFtpPath($v)			 		{$this->putInCustomData(self::CUSTOM_DATA_FTP_PATH, $v);}
	public function setFtpUsername($v)			 	{$this->putInCustomData(self::CUSTOM_DATA_FTP_USERNAME, $v);}
	public function setFtpPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_FTP_PASSWORD, $v);}	
	public function setFtpHost($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_HOST, $v);}
	public function setChannelTitle($v)				{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_TITLE, $v);}
	
	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return AttUverseDistributionPlugin::getProvider();
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
	    $fieldConfig->setFieldName(AttUverseDistributionField::CHANNEL_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Channel title');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/channel_title" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;	    
			    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_ENTRY_ID);
	    $fieldConfig->setUserFriendlyFieldName('item entry id');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_CREATED_AT);
	    $fieldConfig->setUserFriendlyFieldName('item created at');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(createdAt)" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	        
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_UPDATED_AT);
	    $fieldConfig->setUserFriendlyFieldName('item updated at');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(updatedAt)" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_START_DATE);
	    $fieldConfig->setUserFriendlyFieldName('entry valid time');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');	    
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_END_DATE);
	    $fieldConfig->setUserFriendlyFieldName('entry expiration time');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');	    
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	        
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('item title');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_DESCRIPTION);
	    $fieldConfig->setUserFriendlyFieldName('item description');
	   	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    //tags
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_TAGS);
	    $fieldConfig->setUserFriendlyFieldName('Entry tags');
	    $fieldConfig->setEntryMrssXslt(
	    			'<xsl:for-each select="tags/tag">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::TAGS));
	 	$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    //categories
	    $fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(AttUverseDistributionField::ITEM_CATEGORIES);
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
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_METADATA_SHORT_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('metadata short title');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AttUverseShortTitle" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_METADATA_TUNEIN);
	    $fieldConfig->setUserFriendlyFieldName('metadata tunein');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AttUverseTunein" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_METADATA_CONTENT_RATING);
	    $fieldConfig->setUserFriendlyFieldName('metadata content rating');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AttUverseContentRating" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_METADATA_LEGAL_DISCLAIMER);
	    $fieldConfig->setUserFriendlyFieldName('metadata legal disclaimer');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AttUverseLegalDisclaimer" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(AttUverseDistributionField::ITEM_METADATA_GENRE);
	    $fieldConfig->setUserFriendlyFieldName('metadata genre');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/AttUverseGenre" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    return $fieldConfigArray;
	}
	
	
}
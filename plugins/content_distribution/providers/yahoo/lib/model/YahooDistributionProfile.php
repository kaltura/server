<?php
/**
 * @package plugins.yahooDistribution
 * @subpackage model
 */
class YahooDistributionProfile extends ConfigurableDistributionProfile
{
	const SMALL_THUMB_WIDTH = '168';
	const SMALL_THUMB_HEIGHT = '94';
	const LARGE_THUMB_WIDTH = '550';
	const LARGE_THUMB_HEIGHT = '309';
	private $FLAVOR_ASSET_DIMENSIONS = array (
		  array('width' => 800, 'height' => 640),
		  array('width' => 400, 'height' => 300),
		  array('width' => 320, 'height' => 240),
		  array('width' => 240, 'height' => 180),	
	); 
	
	
	const CUSTOM_DATA_FTP_PATH = 'ftpPath';
	const CUSTOM_DATA_FTP_USERNAME = 'ftpUsername';
	const CUSTOM_DATA_FTP_PASSWORD = 'ftpPassword';
	const CUSTOM_DATA_FTP_HOST = 'ftpHost';
	const CUSTOM_DATA_CONTACT_TELEPHONE = 'contactTelephone';
	const CUSTOM_DATA_CONTACT_EMAIL = 'contactEmail';
	const CUSTOM_DATA_PROCESS_FEED_ACTION_STATUS = 'processFeed';
			
	// validations
	const VIDEO_DESCRIPTION_MAXIMUM_LENGTH = 255;
	const FEED_ITEM_ID_MAXIMUM_LENGTH = 50;
	
	const DATE_TIME_ZONE_VALID_VALUES = 'CST,MST,EST,ST,CDT,MDT,EDT,PDT,CT,ET,MT,PT';
	const VIDEO_STREAM_BITRATE_VALID_VALUES = '8,14,16,20,28,32,48,56,64,80,96,100,115,128,150,250,300,319,350,500,600,700,1000,1200,1500,1800,2000';
	const VIDEO_STREAM_FORMAT_VALID_VALUES = 'MP4,MOV';
	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return YahooDistributionPlugin::getProvider();
	}
	
		
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{	    	    
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);		
		$maxLengthFields = array (
			YahooDistributionField::VIDEO_DESCRIPTION => self::VIDEO_DESCRIPTION_MAXIMUM_LENGTH,
			YahooDistributionField::VIDEO_FEEDITEM_ID => self::FEED_ITEM_ID_MAXIMUM_LENGTH,		    		    
		);
		    		
		$inListOrNullFields = array ();		

		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($maxLengthFields, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($inListOrNullFields, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateTwoThumbnailsExist($entryDistribution, $action));
		//$validationErrors = array_merge($validationErrors, $this->validateVideoStreamFormatAndBitrate($entryDistribution, $action));
		//$validationErrors = array_merge($validationErrors, $this->validateThumbnailsDimensions($entryDistribution, $action));
		//TODO: validate only video stream formats and remove bitrate and thumb dimensions	
		
	    $emailField = YahooDistributionField::CONTACT_EMAIL;
		$emailValue = $allFieldValues[$emailField];
		if ($emailValue && !kString::isEmailString($emailValue))
		{
		    $validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($emailField));
			$validationError->setValidationErrorType(DistributionValidationErrorType::INVALID_FORMAT);
			$validationError->setValidationErrorParam('email');
			$validationError->setDescription('Not an email string');
			$validationErrors[] = $validationError;
		}
		//TODO: validate that if contact_email is given, contact_telephone is given	
		$telephoneField = YahooDistributionField::CONTACT_TELEPHONE;
		$telephoneValue = $allFieldValues[$telephoneField];
		//if Email is given than telephone must be given too
		if ($emailValue && !$telephoneValue)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::MISSING_METADATA, $this->getUserFriendlyFieldName($telephoneField));
			$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
			$validationError->setValidationErrorParam('Telephone is missing');
			$validationError->setDescription('Telephone is required');
			$validationErrors[] = $validationError;
		}
							
		return $validationErrors;
	}
	
	/**
	 * Validate two thumbnails exist
	 * @param $entryDistribution
	 * @param $action
	 */
	private function validateTwoThumbnailsExist($entryDistribution, $action)
	{
		$validationErrors = array();		
		//Validating thumbnails
		$c = new Criteria();
		$c->addAnd(assetPeer::ID, explode(',',$entryDistribution->getThumbAssetIds()), Criteria::IN);
		$c->addAscendingOrderByColumn(assetPeer::ID);
		$thumbAssets = assetPeer::doSelect($c);		
		if (!count($thumbAssets)|| count($thumbAssets)<2)
		{
			KalturaLog::debug('Two thumbnails are required');
			$errorMsg = 'two thumbnails are required';			
    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);    		
    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
    		$validationError->setValidationErrorParam($errorMsg);
    		$validationError->setDescription($errorMsg);
    		$validationErrors[] = $validationError;
		}
		return $validationErrors;			
	}	
			

	
	/**
	 * Validate video format and video bitrate
	 * @param $entryDistribution
	 * @param $action
	 */
	private function validateVideoStreamFormatAndBitrate($entryDistribution, $action)
	{		
		$validationErrors = array();		
		//validation of stream format
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		// if we have specific flavor assets for this distribution, grab the first one
		if(count($flavorAssets))
		{
			foreach ($flavorAssets as $flavorAsset)
			{
				/* @var $flavorAsset flavorAsset */				
				$fileExt = strtoupper($flavorAsset->getFileExt());				
				$allowedExts = explode(',', self::VIDEO_STREAM_FORMAT_VALID_VALUES);
				//validate file format
				if (!in_array($fileExt, $allowedExts))
				{
					KalturaLog::debug('flavor asset id ['.$flavorAsset->getId().'] does not have a valid extension ['.$fileExt.']');
					$errorMsg = 'flavor format must be one of ['.self::VIDEO_STREAM_FORMAT_VALID_VALUES.']';
		    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);
		    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
		    		$validationError->setValidationErrorParam($errorMsg);
		    		$validationError->setDescription($errorMsg);
		    		$validationErrors[] = $validationError;
				}
				//format is valid -> check bitrate validation
				/*
				$videoBitrate = $flavorAsset->getBitrate();
				$allowedBitrates = explode(',', self::VIDEO_STREAM_BITRATE_VALID_VALUES);
				if (!in_array($videoBitrate, $allowedBitrates))
				{
					KalturaLog::debug('flavor asset id ['.$flavorAsset->getId().'] does not have a valid bitrate ['.$videoBitrate.']');
					$errorMsg = 'video bitrate must be one of ['.self::VIDEO_STREAM_BITRATE_VALID_VALUES.']';
		    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);
		    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
		    		$validationError->setValidationErrorParam($errorMsg);
		    		$validationError->setDescription($errorMsg);
		    		$validationErrors[] = $validationError;
				}
				//if bitrate & fileExt had been validated					
				//check formats width and height
				$fileWidth = $flavorAsset->getWidth();
				$fileHeight = $flavorAsset->getHeight();
				$flavorDimension =  array('width' => $fileWidth, 'height' => $fileHeight);
				if (!in_array($flavorDimension, $this->FLAVOR_ASSET_DIMENSIONS))
				{
					KalturaLog::debug('flavor asset id ['.$flavorAsset->getId().'] does not have a valid dimensions. width ['.$fileWidth.']. height ['.$fileHeight.']');
					$errorMsg = 'video dimensions must be one of ['.$this->FLAVOR_ASSET_DIMENSIONS.']';
		    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);
		    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
		    		$validationError->setValidationErrorParam($errorMsg);
		    		$validationError->setDescription($errorMsg);
		    		$validationErrors[] = $validationError;
				}	
				*/															
			}				
		}
		return $validationErrors;
	}
	
	/**
	 * Validate thumbnails dimensions
	 * @param $entryDistribution
	 * @param $action
	 */
	private function validateThumbnailsDimensions ($entryDistribution, $action)
	{
		$validationErrors = array();		
		//Validating thumbnails
		$c = new Criteria();
		$c->addAnd(assetPeer::ID, explode(',',$entryDistribution->getThumbAssetIds()), Criteria::IN);
		$c->addAscendingOrderByColumn(assetPeer::ID);
		$thumbAssets = assetPeer::doSelect($c);		
		$smallThumbFound = false;
		$largeThumbFound = false;
		if (count($thumbAssets))
		{
			//if the thumbnails dimensions were not found
			if (!$smallThumbFound || !$largeThumbFound)
			{
				foreach ($thumbAssets as $thumbAsset) {
					/* @var $thumbAsset KalturaThumbAsset */
					if (empty($thumbAsset)) {
						continue;
					}
					$width = $thumbAsset->width;
					$height = $thumbAsset->height;
					if ($width == self::SMALL_THUMB_WIDTH  && $height == self::SMALL_THUMB_HEIGHT){
						$smallThumbFound = true;
					}
					if ($width == self::LARGE_THUMB_WIDTH && $height == self::LARGE_THUMB_HEIGHT){
						$largeThumbFound = true;
					}
					//other dimensions were choosen
					else{
						KalturaLog::debug('Thumb id ['.$thumbAsset->id.'] does not have a valid dimensions. width ['.$width.']. height ['.$height.']');
						$errorMsg = 'thumbnail dimension must be ['.self::SMALL_THUMB_WIDTH.'] on ['.self::SMALL_THUMB_HEIGHT.'] , or ['.self::LARGE_THUMB_WIDTH.'] on ['.self::LARGE_THUMB_HEIGHT.']';
			    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);
			    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
			    		$validationError->setValidationErrorParam($errorMsg);
			    		$validationError->setDescription($errorMsg);
			    		$validationErrors[] = $validationError;
					}
				}
			}
		}
		//if there are no thumbnails- fail!
		else{
			KalturaLog::debug('No thumbnails were supplied');
			$errorMsg = 'no thumbnails were supplied';			
    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);    		
    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
    		$validationError->setValidationErrorParam($errorMsg);
    		$validationError->setDescription($errorMsg);
    		$validationErrors[] = $validationError;
		}
		return $validationErrors;			
	}
		
	
	public function getFtpPath()					{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PATH);}
	public function getFtpUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_USERNAME);}
	public function getFtpPassword()			 	{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PASSWORD);}
	public function getFtpHost()			 		{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_HOST);}
	public function getContactEmail()		 		{return $this->getFromCustomData(self::CUSTOM_DATA_CONTACT_EMAIL);}
	public function getContactTelephone()	 		{return $this->getFromCustomData(self::CUSTOM_DATA_CONTACT_TELEPHONE);}
	public function getProcessFeedActionStatus()	{return $this->getFromCustomData(self::CUSTOM_DATA_PROCESS_FEED_ACTION_STATUS);}	
	
	public function setFtpPath($v)			 		{$this->putInCustomData(self::CUSTOM_DATA_FTP_PATH, $v);}
	public function setFtpUsername($v)			 	{$this->putInCustomData(self::CUSTOM_DATA_FTP_USERNAME, $v);}
	public function setFtpPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_FTP_PASSWORD, $v);}	
	public function setFtpHost($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_HOST, $v);}
	public function setContactEmail($v)				{$this->putInCustomData(self::CUSTOM_DATA_CONTACT_EMAIL, $v);}
	public function setContactTelephone($v)			{$this->putInCustomData(self::CUSTOM_DATA_CONTACT_TELEPHONE, $v);}	   
	public function setProcessFeedActionStatus($v)	{$this->putInCustomData(self::CUSTOM_DATA_PROCESS_FEED_ACTION_STATUS, $v);}	   
	
	
	protected function getDefaultFieldConfigArray()
	{	    
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();
		
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::CONTACT_TELEPHONE);
	    $fieldConfig->setUserFriendlyFieldName('Contact telephone');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/contact_telephone" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
		$fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::CONTACT_EMAIL);
	    $fieldConfig->setUserFriendlyFieldName('Contact email');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/contact_email" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
		$fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_MODIFIED_DATE);
	    $fieldConfig->setUserFriendlyFieldName('Video modified date');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(updatedAt)" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	        
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_FEEDITEM_ID);
	    $fieldConfig->setUserFriendlyFieldName('feed item id');
	    //TODO: insert /, Category, /, and then entryId, for example: ID="/nat/041405flagirl
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_DESCRIPTION);
	    $fieldConfig->setUserFriendlyFieldName('Entry description');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(YahooDistributionField::VIDEO_ROUTING);
		$fieldConfig->setUserFriendlyFieldName('Entry categories');
		$fieldConfig->setEntryMrssXslt(
					'<xsl:for-each select="category">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
		$fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::CATEGORIES));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_KEYWORDS);
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
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_VALID_TIME);
	    $fieldConfig->setUserFriendlyFieldName('entry valid time');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');	    
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_EXPIRATION_TIME);
	    $fieldConfig->setUserFriendlyFieldName('entry expiration time');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');	    
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    //link title
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_LINK_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('video link title');
	    $fieldConfig->setEntryMrssXslt(
	    			'<xsl:for-each select="customData/metadata/YahooLinkTitle">
						<xsl:if test="position() &gt; 1">
							<xsl:text>;</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	     //link url
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_LINK_URL);
	    $fieldConfig->setUserFriendlyFieldName('video link url');
	    $fieldConfig->setEntryMrssXslt(
	    			'<xsl:for-each select="customData/metadata/YahooLinkUrl">
						<xsl:if test="position() &gt; 1">
							<xsl:text>;</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YahooDistributionField::VIDEO_DURATION);
	    $fieldConfig->setUserFriendlyFieldName('video duration');
	   	$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(media/duration)" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;	    
	    
	    return $fieldConfigArray;
	}
	
	
}
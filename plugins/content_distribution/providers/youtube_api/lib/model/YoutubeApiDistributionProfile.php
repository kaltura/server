<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage model
 */
class YoutubeApiDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_DEFAULT_CATEGORY = 'defaultCategory';
	const CUSTOM_DATA_ALLOW_COMMENTS = 'allowComments';
	const CUSTOM_DATA_ALLOW_EMBEDDING = 'allowEmbedding';
	const CUSTOM_DATA_ALLOW_RATINGS = 'allowRatings';
	const CUSTOM_DATA_ALLOW_RESPONSES = 'allowResponses';
	const CUSTOM_DATA_ASSUME_SUCCESS = 'assumeSuccess';
	const CUSTOM_DATA_PRIVACY_STATUS = 'privacyStatus';

	const METADATA_FIELD_DESCRIPTION = 'YoutubeDescription';
	const METADATA_FIELD_CATEGORY = 'YoutubeCategory';
	const METADATA_FIELD_TAGS = 'YoutubeKeywords';
	const METADATA_FIELD_PLAYLIST_IDS = 'YoutubePlaylistIds';
	const METADATA_FIELD_PRIVACY_STATUS = 'YoutubePrivacyStatus';
	
	const MEDIA_TITLE_MAXIMUM_LENGTH = 100;
	const MEDIA_DESCRIPTION_MAXIMUM_LENGTH = 5000;
	const MEDIA_KEYWORDS_MAXIMUM_LENGTH = 500;
	const MEDIA_EACH_KEYWORD_MINIMUM_LENGTH = 2;
	const MEDIA_EACH_KEYWORD_MAXIMUM_LENGTH = 30;
	const MEDIA_KEYWORDS_FORBIDDEN_CHARS = '"';
	
	const ALLOW_COMMENTS_VALID_VALUES = 'allowed,denied,moderated';
	const ALLOW_RESPONSES_VALID_VALUES = 'allowed,denied,moderated';
	const ALLOW_EMBEDDING_VALID_VALUES = 'allowed,denied';
	const ALLOW_RATINGS_VALID_VALUES = 'allowed,denied';
	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return YoutubeApiDistributionPlugin::getProvider();
	}
		
	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);

		$maxLengthFields = array (
		    YouTubeApiDistributionField::MEDIA_TITLE => self::MEDIA_TITLE_MAXIMUM_LENGTH,
		    YouTubeApiDistributionField::MEDIA_DESCRIPTION => self::MEDIA_DESCRIPTION_MAXIMUM_LENGTH,
		    YouTubeApiDistributionField::MEDIA_KEYWORDS => self::MEDIA_KEYWORDS_MAXIMUM_LENGTH
		);
		
		$inListOrNullFields = array (
		    YouTubeApiDistributionField::ALLOW_COMMENTS => explode(',', self::ALLOW_COMMENTS_VALID_VALUES),
		    YouTubeApiDistributionField::ALLOW_EMBEDDING => explode(',', self::ALLOW_EMBEDDING_VALID_VALUES),
		    YouTubeApiDistributionField::ALLOW_RATINGS => explode(',', self::ALLOW_RATINGS_VALID_VALUES),
		    YouTubeApiDistributionField::ALLOW_RESPONSES => explode(',', self::ALLOW_RESPONSES_VALID_VALUES),
		);
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($maxLengthFields, $allFieldValues, $action));
	    $validationErrors = array_merge($validationErrors, $this->validateInListOrNull($inListOrNullFields, $allFieldValues, $action));
				
		$videoTagsValue = isset($allFieldValues[YouTubeApiDistributionField::MEDIA_KEYWORDS]) ? $allFieldValues[YouTubeApiDistributionField::MEDIA_KEYWORDS] : null;
		$validationErrors = array_merge($validationErrors, $this->validateTags($videoTagsValue, $action));
	
		return $validationErrors;
	}
	
	
    protected function validateTags($tagsStr, $action)
	{
	    $validationErrors = array();
	    if (!empty($tagsStr) && mb_strlen($tagsStr, 'UTF-8') > 0)
		{
		    $userFriendlyTagsFieldName = $this->getUserFriendlyFieldName(YouTubeApiDistributionField::MEDIA_KEYWORDS);
		    $tagsArray = array_map('trim', explode(',', $tagsStr));
            
		    if(mb_strlen($tagsStr, 'UTF-8') > self::MEDIA_KEYWORDS_MAXIMUM_LENGTH)
			{
			    $validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $userFriendlyTagsFieldName);
    			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
    			$validationError->setValidationErrorParam(self::MEDIA_KEYWORDS_MAXIMUM_LENGTH);
    			$validationErrors[] = $validationError;
			}
			
		    $forbiddenChars = str_split(self::MEDIA_KEYWORDS_FORBIDDEN_CHARS);
			foreach($forbiddenChars as $forbiddenChar)
			{
				if(strpos($tagsStr, $forbiddenChar) !== false)
				{
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, self::METADATA_FIELD_TAGS, "$userFriendlyTagsFieldName contain invalid char [$forbiddenChar]");
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam("$userFriendlyTagsFieldName contain invalid char [$forbiddenChar]");
					$validationErrors[] = $validationError;
				}
			}
			
			
		    foreach($tagsArray as $tag)
			{
				if(mb_strlen($tag, 'UTF-8') < self::MEDIA_EACH_KEYWORD_MINIMUM_LENGTH)
				{
				    $errorDescription = $userFriendlyTagsFieldName.' ['.$tag.'] must contain at least '.self::MEDIA_EACH_KEYWORD_MINIMUM_LENGTH.' characters';
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $userFriendlyTagsFieldName, $errorDescription);
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam($errorDescription);
					$validationErrors[] = $validationError;
				}
			    if(mb_strlen($tag, 'UTF-8') > self::MEDIA_EACH_KEYWORD_MAXIMUM_LENGTH)
				{
				    $errorDescription = $userFriendlyTagsFieldName.' ['.$tag.'] must contain less than '.self::MEDIA_EACH_KEYWORD_MAXIMUM_LENGTH.' characters';
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $userFriendlyTagsFieldName, $errorDescription);
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam($errorDescription);
					$validationErrors[] = $validationError;
				}
			}
		    
		}
		return $validationErrors;
	}
	

	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getDefaultCategory()		{return $this->getFromCustomData(self::CUSTOM_DATA_DEFAULT_CATEGORY);}
	public function getAllowComments()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_COMMENTS);}
	public function getAllowEmbedding()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_EMBEDDING);}
	public function getAllowRatings()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_RATINGS);}
	public function getAllowResponses()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_RESPONSES);}
	public function getAssumeSuccess()			{return (bool) $this->getFromCustomData(self::CUSTOM_DATA_ASSUME_SUCCESS);}
	public function getPrivacyStatus()			{return $this->getFromCustomData(self::CUSTOM_DATA_PRIVACY_STATUS, null, 'public');}

	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setDefaultCategory($v)		{$this->putInCustomData(self::CUSTOM_DATA_DEFAULT_CATEGORY, $v);}
	public function setAllowComments($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_COMMENTS, $v);}
	public function setAllowEmbedding($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_EMBEDDING, $v);}
	public function setAllowRatings($v)			{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_RATINGS, $v);}
	public function setAllowResponses($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_RESPONSES, $v);}
	public function setAssumeSuccess($v)		{$this->putInCustomData(self::CUSTOM_DATA_ASSUME_SUCCESS, $v);}
	public function setPrivacyStatus($v)		{$this->putInCustomData(self::CUSTOM_DATA_PRIVACY_STATUS, $v);}

	
	protected function getDefaultFieldConfigArray()
	{	    
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::MEDIA_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::MEDIA_DESCRIPTION);
	    $fieldConfig->setUserFriendlyFieldName(self::METADATA_FIELD_DESCRIPTION.' / Entry description');
	    $fieldConfig->setEntryMrssXslt('
        			<xsl:choose>
                    	<xsl:when test="customData/metadata/'.self::METADATA_FIELD_DESCRIPTION.' != \'\'">
                    		<xsl:value-of select="customData/metadata/'.self::METADATA_FIELD_DESCRIPTION.'" />
                    	</xsl:when>
                    	<xsl:otherwise>
                    		<xsl:value-of select="string(description)" />
                    	</xsl:otherwise>
                    </xsl:choose>');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION,"/*[local-name()='metadata']/*[local-name()='".self::METADATA_FIELD_DESCRIPTION."']"));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::MEDIA_KEYWORDS);
	    $fieldConfig->setUserFriendlyFieldName(self::METADATA_FIELD_TAGS.' / Entry tags');
	    $fieldConfig->setEntryMrssXslt(
	                '<xsl:choose>
                    	<xsl:when test="customData/metadata/'.self::METADATA_FIELD_TAGS.' != \'\'">
                    		<xsl:value-of select="normalize-space(customData/metadata/'.self::METADATA_FIELD_TAGS.')" />
                    	</xsl:when>
                    	<xsl:otherwise>
                    		<xsl:for-each select="tags/tag">
                    			<xsl:if test="position() &gt; 1">
                    				<xsl:text>,</xsl:text>
                    			</xsl:if>
                    			<xsl:value-of select="normalize-space(.)" />
                    		</xsl:for-each>
                    	</xsl:otherwise>
                    </xsl:choose>');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::TAGS,"/*[local-name()='metadata']/*[local-name()='".self::METADATA_FIELD_TAGS."']"));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::MEDIA_CATEGORY);
	    $fieldConfig->setUserFriendlyFieldName(self::METADATA_FIELD_CATEGORY);
	    $fieldConfig->setEntryMrssXslt('
        			<xsl:choose>
                    	<xsl:when test="customData/metadata/'.self::METADATA_FIELD_CATEGORY.' != \'\'">
                    		<xsl:value-of select="customData/metadata/'.self::METADATA_FIELD_CATEGORY.'" />
                    	</xsl:when>
                    	<xsl:otherwise>
                    		<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/default_category" />
                    	</xsl:otherwise>
                    </xsl:choose>');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array("/*[local-name()='metadata']/*[local-name()='default_category']","/*[local-name()='metadata']/*[local-name()='".self::METADATA_FIELD_CATEGORY."']"));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;


	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::MEDIA_PLAYLIST_IDS);
	    $fieldConfig->setUserFriendlyFieldName(self::METADATA_FIELD_PLAYLIST_IDS);
	    $fieldConfig->setEntryMrssXslt(
					'<xsl:if test="customData/metadata/'.self::METADATA_FIELD_PLAYLIST_IDS.' != \'\'">'
						.'<xsl:value-of select="normalize-space(customData/metadata/'.self::METADATA_FIELD_PLAYLIST_IDS.')" />'
					.'</xsl:if>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::START_DATE);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunrise');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::END_DATE);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunset');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::ALLOW_COMMENTS);
	    $fieldConfig->setUserFriendlyFieldName('Allow comments');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_comments" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::ALLOW_RESPONSES);
	    $fieldConfig->setUserFriendlyFieldName('Allow responses');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_responses" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::ALLOW_RATINGS);
	    $fieldConfig->setUserFriendlyFieldName('Allow ratings');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_ratings" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::ALLOW_EMBEDDING);
	    $fieldConfig->setUserFriendlyFieldName('Allow embedding');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_embedding" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    // The following allows defining an alternative slug header for an uploaded video, which
	    // affects the "raw file" field of a YouTube video entry.
	    // A typical MRSS XSLT value would be the entry's title (similar to MEDIA_TITLE)
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeApiDistributionField::ALT_RAW_FILENAME);
	    $fieldConfig->setUserFriendlyFieldName('Alternative Raw File Name ');
	    $fieldConfig->setEntryMrssXslt(''); // Empty by default to indicate that the feature is switched off.
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(YouTubeApiDistributionField::ENTRY_PRIVACY_STATUS);
		$fieldConfig->setUserFriendlyFieldName('Entry Privacy Status');
		$fieldConfig->setEntryMrssXslt(
					'<xsl:choose>
                    	<xsl:when test="customData/metadata/'.self::METADATA_FIELD_PRIVACY_STATUS.' != \'\'">
                    		<xsl:value-of select="customData/metadata/'.self::METADATA_FIELD_PRIVACY_STATUS.'" />
                    	</xsl:when>
                    	<xsl:otherwise>
                    		<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/privacy_status" />
                    	</xsl:otherwise>
                    </xsl:choose>'
		);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		
	    return $fieldConfigArray;
	}
	
	protected function getGoogleOAuth2ObjectIdentifier()
	{
		return md5(get_class($this) . $this->getUsername());
	}
	
	public function getGoogleOAuth2Data()
	{
		$appId = YoutubeApiDistributionPlugin::GOOGLE_APP_ID;
		$subId = $this->getGoogleOAuth2ObjectIdentifier();
		
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		if($partner)
			return $partner->getGoogleOAuth2($appId, $subId);
			
		return null;
	}
	
	public function getApiAuthorizeUrl()
	{
		$appId = YoutubeApiDistributionPlugin::GOOGLE_APP_ID;
		$subId = $this->getGoogleOAuth2ObjectIdentifier();
					
		$url = kConf::get('apphome_url');
		$url .= "/index.php/extservices/googleoauth2/ytid/$appId/subid/$subId";
		$url .= "?partnerId=".$this->getPartnerId();
		return $url;
	}
    
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		$ret = parent::getOptionalAssetDistributionRules();
		if(!class_exists('CaptionPlugin') || !CaptionPlugin::isAllowedPartner($this->getPartnerId()))
		{
			return $ret;
		}
		$isCaptionCondition = new kAssetDistributionPropertyCondition();
		$isCaptionCondition->setPropertyName(assetPeer::translateFieldName(assetPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME));
		$isCaptionCondition->setPropertyValue(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));

		$captionDistributionRule = new kAssetDistributionRule();
		$captionDistributionRule->setAssetDistributionConditions(array($isCaptionCondition));
		$ret[] = $captionDistributionRule;
		$this->setOptionalAssetDistributionRules($ret);
		$this->save();
	}
}

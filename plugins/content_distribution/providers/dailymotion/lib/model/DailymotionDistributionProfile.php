<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage model
 */
class DailymotionDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_USER = 'user';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_GEO_BLOCKING_MAPPING = 'geoBlockingMapping';

	const METADATA_FIELD_CATEGORY = 'DailymotionCategory';
	const METADATA_FIELD_DESCRIPTION = 'DailymotionDescription';
	const METADATA_FIELD_TAGS = 'DailymotionKeywords';
	const METADATA_FIELD_TYPE = 'DailymotionType';

	const VIDEO_TITLE_MAXIMUM_LENGTH = 255;
	const VIDEO_DESCRIPTION_MAXIMUM_LENGTH = 2000;
	const VIDEO_TAGS_MINIMUM_COUNT = 2;
	const VIDEO_TAGS_MAXIMUM_LENGTH = 250;
	const VIDEO_TAG_MINIMUM_LENGTH = 3;
	const VIDEO_TYPE_ALLOWED_VALUES = 'ugc,creative,official';
	const VIDEO_GEO_BLOCKING_OPERATION_ALLOWED_VALUES = 'allow,deny';
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return DailymotionDistributionPlugin::getProvider();
	}
	
	public function getUser()			{return $this->getFromCustomData(self::CUSTOM_DATA_USER);}
	public function getPassword()		{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getGeoBlockingMapping(){return $this->getFromCustomData(self::CUSTOM_DATA_GEO_BLOCKING_MAPPING);}

	public function setUser($v)			{$this->putInCustomData(self::CUSTOM_DATA_USER, $v);}
	public function setPassword($v)		{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setGeoBlockingMapping($v){$this->putInCustomData(self::CUSTOM_DATA_GEO_BLOCKING_MAPPING, $v);}

	
			
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);

		$maxLengthFields = array (
		    DailymotionDistributionField::VIDEO_TITLE => self::VIDEO_TITLE_MAXIMUM_LENGTH,
		    DailymotionDistributionField::VIDEO_DESCRIPTION => self::VIDEO_DESCRIPTION_MAXIMUM_LENGTH,
		);
		
		$inListOrNullFields = array (
		    DailymotionDistributionField::VIDEO_TYPE => explode(',', self::VIDEO_TYPE_ALLOWED_VALUES),
			DailymotionDistributionField::VIDEO_GEO_BLOCKING_OPERATION => explode(',', self::VIDEO_GEO_BLOCKING_OPERATION_ALLOWED_VALUES),
	    );
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}	
				
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($maxLengthFields, $allFieldValues, $action));
	    $validationErrors = array_merge($validationErrors, $this->validateInListOrNull($inListOrNullFields, $allFieldValues, $action));
				
		$videoTagsValue = isset($allFieldValues[DailymotionDistributionField::VIDEO_TAGS]) ? $allFieldValues[DailymotionDistributionField::VIDEO_TAGS] : null;
		$validationErrors = array_merge($validationErrors, $this->validateTags($videoTagsValue, $action));
	
		return $validationErrors;
	}
	
	
	protected function validateTags($videoTagsValue, $action)
	{
	    $validationErrors = array();
	    if (!empty($videoTagsValue) && strlen($videoTagsValue) > 0)
		{
		    $userFriendlyTagsFieldName = $this->getUserFriendlyFieldName(DailymotionDistributionField::VIDEO_TAGS);
		    $tagsArray = array_map('trim', explode(',', $videoTagsValue));
            $tagsStr = implode(' , ', $tagsArray);
            
		    if(strlen($tagsStr) > self::VIDEO_TAGS_MAXIMUM_LENGTH)
			{
			    $validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $userFriendlyTagsFieldName);
    			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
    			$validationError->setValidationErrorParam(self::VIDEO_TAGS_MAXIMUM_LENGTH);
    			$validationErrors[] = $validationError;
			}
			if(count($tagsArray) < self::VIDEO_TAGS_MINIMUM_COUNT)
			{
			    $errorDescription = $userFriendlyTagsFieldName.' must contain at least ' . self::VIDEO_TAGS_MINIMUM_COUNT . ' tags';
				$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $userFriendlyTagsFieldName, $errorDescription);
				$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
				$validationError->setValidationErrorParam($errorDescription);
				$validationErrors[] = $validationError;
			}
			
		    foreach($tagsArray as $tag)
			{
				if(strlen($tag) < self::VIDEO_TAG_MINIMUM_LENGTH)
				{
				    $errorDescription = $userFriendlyTagsFieldName.' ['.$tag.'] must contain at least '.self::VIDEO_TAG_MINIMUM_LENGTH.' characters';
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $userFriendlyTagsFieldName, $errorDescription);
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam($errorDescription);
					$validationErrors[] = $validationError;
				}
			}
		    
		}
		return $validationErrors;
	}

	
	protected function getDefaultFieldConfigArray()
	{	    
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();
	      
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(DailymotionDistributionField::VIDEO_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(DailymotionDistributionField::VIDEO_DESCRIPTION);
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
	    $fieldConfig->setFieldName(DailymotionDistributionField::VIDEO_TAGS);
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
	    $fieldConfig->setFieldName(DailymotionDistributionField::VIDEO_CHANNEL);
	    $fieldConfig->setUserFriendlyFieldName(self::METADATA_FIELD_CATEGORY);
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/'.self::METADATA_FIELD_CATEGORY.'" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array("/*[local-name()='metadata']/*[local-name()='".self::METADATA_FIELD_CATEGORY."']"));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(DailymotionDistributionField::VIDEO_LANGUAGE);
	    $fieldConfig->setUserFriendlyFieldName('Video language');
	    $fieldConfig->setEntryMrssXslt('<xsl:text>en</xsl:text>');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(DailymotionDistributionField::VIDEO_TYPE);
	    $fieldConfig->setUserFriendlyFieldName(self::METADATA_FIELD_TYPE);
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/'.self::METADATA_FIELD_TYPE.'" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array("/*[local-name()='metadata']/*[local-name()='".self::METADATA_FIELD_TYPE."']"));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DailymotionDistributionField::VIDEO_GEO_BLOCKING_OPERATION);
		$fieldConfig->setUserFriendlyFieldName('Geo blocking allow / deny');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(DailymotionDistributionField::VIDEO_GEO_BLOCKING_COUNTRY_LIST);
		$fieldConfig->setUserFriendlyFieldName('Geo blocking country list');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

	    return $fieldConfigArray;
	}
}
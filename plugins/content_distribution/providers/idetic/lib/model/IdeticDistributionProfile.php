<?php
/**
 * @package plugins.ideticDistribution
 * @subpackage model
 */
class IdeticDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_FTP_PATH = 'ftpPath';
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_DOMAIN = 'domain';
	
	// validations
	const GENRE_VALID_VALUES = ''; //TODO: add a list of the genre
	const FLAVOR_VALID_FORMATS = 'mp4,mov,mpg';//there are problems uploading: wmv & avi formats.
	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return IdeticDistributionPlugin::getProvider();
	}
	
	public function getFtpPath()				{return $this->getFromCustomData(self::CUSTOM_DATA_FTP_PATH);}
	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getDomain()					{return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN);}
	
	public function setFtpPath($v)			 	{$this->putInCustomData(self::CUSTOM_DATA_FTP_PATH, $v);}
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setDomain($v)				{$this->putInCustomData(self::CUSTOM_DATA_DOMAIN, $v);}
	
	
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{	    	    
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);
	    
		//validation of flavor format
		$flavorAsset = null;
		$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		// if we have specific flavor assets for this distribution, grab the first one
		if(count($flavorAssets))
		{
			$flavorAsset = reset($flavorAssets);	
			$fileExt = $flavorAsset->getFileExt();				
			$allowedExts = explode(',', self::FLAVOR_VALID_FORMATS);
			if (!in_array($fileExt, $allowedExts))
			{
				KalturaLog::debug('flavor asset id ['.$flavorAsset->getId().'] does not have a valid extension ['.$fileExt.']');
				$errorMsg = 'Flavor format must be one of ['.self::FLAVOR_VALID_FORMATS.']';
	    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);
	    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
	    		$validationError->setValidationErrorParam($errorMsg);
	    		$validationError->setDescription($errorMsg);
	    		$validationErrors[] = $validationError;
			}
		}
		
		$inListOrNullFields = array (
		    IdeticDistributionField::GENRE => explode(',', self::GENRE_VALID_VALUES),		    
		);
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}		
		
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($inListOrNullFields, $allFieldValues, $action));
		//validating Slot is a whole number
		$validationErrors = array_merge($validationErrors, $this->validateIsWholeNumber(IdeticDistributionField::SLOT, $allFieldValues, $action));

		return $validationErrors;
	}
	
	private function validateIsWholeNumber($fieldName, $allFieldValues, $action)
	{
	    $validationErrors = array();
    	$value = isset($allFieldValues[$fieldName]) ? $allFieldValues[$fieldName] : null;
    	if (!is_null($value) && strlen($value) > 0 && !preg_match('/^([0-9])*$/', $value))
		{		        
		    $errorMsg = $this->getUserFriendlyFieldName($fieldName).' value must be an integer';
    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
    		$validationError->setValidationErrorParam($errorMsg);
    		$validationError->setDescription($errorMsg);
    		$validationErrors[] = $validationError;
		}
	    return $validationErrors;
	}
	
	protected function getDefaultFieldConfigArray()
	{
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();
	    
	    //TODO: implement  
	    
	    //media fields
	    //short title
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(IdeticDistributionField::SHORT_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	   
	    //title
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(IdeticDistributionField::TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
	    //synopsis
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(IdeticDistributionField::SYNOPSIS);
	    $fieldConfig->setUserFriendlyFieldName('Entry description');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    //tags
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(IdeticDistributionField::KEYWORD);
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
	    
	    //genre
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(IdeticDistributionField::GENRE);
	    $fieldConfig->setUserFriendlyFieldName('Idetic genre');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/IdeticGenre" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array("/*[local-name()='metadata']/*[local-name()='IdeticGenre']"));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    //slot
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(IdeticDistributionField::SLOT);
	    $fieldConfig->setUserFriendlyFieldName('Idetic slot');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/IdeticSlot" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array("/*[local-name()='metadata']/*[local-name()='IdeticSlot']"));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	     //folder
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(IdeticDistributionField::FOLDER);
	    $fieldConfig->setUserFriendlyFieldName('Idetic folder');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/IdeticFolder" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array("/*[local-name()='metadata']/*[local-name()='IdeticFolder']"));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    //start of availability
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(IdeticDistributionField::START_OF_AVAILABILITY);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunrise');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    
	    //end of availability
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(IdeticDistributionField::END_OF_AVAILABILITY);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunset');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    	    
	    return $fieldConfigArray;
	}
	
}
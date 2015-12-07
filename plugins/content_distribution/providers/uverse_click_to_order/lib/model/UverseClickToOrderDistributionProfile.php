<?php
/**
 * @package plugins.uverseClickToOrderDistribution
 * @subpackage model
 */
class UverseClickToOrderDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL = 'uniqueHashForFeedUrl';
	const CUSTOM_DATA_BACKGROUND_IMAGE_WIDE = 'backgroundImageWide';
	const CUSTOM_DATA_BACKGROUND_IMAGE_STANDARD = 'backgroundImageStandard';
	
	protected $maxLengthValidation= array (
		UverseClickToOrderDistributionField::ITEM_CONTENT => 320,		
		UverseClickToOrderDistributionField::ITEM_TITLE => 25,
	);
	
	protected $inListOrNullValidation= array (
		UverseClickToOrderDistributionField::ITEM_CONTENT_TYPE => array(
			'image', 
			'video', 
			'link'
		),
	);
		
	const CATEGORY_ENTRY_NAME_MAXIMUM_LENGTH = 15;

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return UverseClickToOrderDistributionPlugin::getProvider();

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
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::BACKGROUND_IMAGE_WIDE);
		$fieldConfig->setUserFriendlyFieldName('Background image - wide');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/background_image_wide" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
				
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::BACKGROUND_IMAGE_STANDART);
		$fieldConfig->setUserFriendlyFieldName('Background image - standard');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/background_image_standard" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::CATEGORY_ENTRY_ID);
		$fieldConfig->setUserFriendlyFieldName('Category entry id');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/UverseClickToOrderCategoryEntryId" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		//cateogry image width		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::CATEGORY_IMAGE_WIDTH);
		$fieldConfig->setUserFriendlyFieldName('Category image width');
		$fieldConfig->setEntryMrssXslt('<xsl:text>101</xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		//category image height
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::CATEGORY_IMAGE_HEIGHT);
		$fieldConfig->setUserFriendlyFieldName('Category image height');
		$fieldConfig->setEntryMrssXslt('<xsl:text>60</xsl:text>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::ITEM_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Item title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::ITEM_CONTENT_TYPE);
		$fieldConfig->setUserFriendlyFieldName('Item content type');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/UverseClickToOrderContentType" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::ITEM_DESTINATION);
		$fieldConfig->setUserFriendlyFieldName('Item destination');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/UverseClickToOrderItemDestination" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::ITEM_CONTENT);
		$fieldConfig->setUserFriendlyFieldName('Entry description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::ITEM_DIRECTIONS);
		$fieldConfig->setUserFriendlyFieldName('Item directions');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/UverseClickToOrderItemDirections" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(UverseClickToOrderDistributionField::SORT_ITEMS_BY_FIELD);
		$fieldConfig->setUserFriendlyFieldName('Sort by field');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/UverseClickToOrderSortItemsByField" />');
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
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($this->inListOrNullValidation, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateMaxLengthCategoryName($allFieldValues[UverseClickToOrderDistributionField::CATEGORY_ENTRY_ID], $action));
		
		return $validationErrors;
	}
	
	protected function validateMaxLengthCategoryName($categoryEntryId, $action)
	{
	    $validationErrors = array();
	    $c = new Criteria();
		$c->addAnd(entryPeer::ID, $categoryEntryId, Criteria::EQUAL);
		$categoryEntryIdObject = entryPeer::doSelect($c);
		if ($categoryEntryIdObject)
		{
			$relatedEntryName = $categoryEntryIdObject[0]->getName();	
			if (strlen($relatedEntryName) > self::CATEGORY_ENTRY_NAME_MAXIMUM_LENGTH)
			{
				KalturaLog::debug('related category name exceeds the maximum length of '.self::CATEGORY_ENTRY_NAME_MAXIMUM_LENGTH. ' characters');
				$errorMsg = 'related category name exceeds the maximum length of '.self::CATEGORY_ENTRY_NAME_MAXIMUM_LENGTH. ' characters';			
	    		$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA);    		
	    		$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
	    		$validationError->setValidationErrorParam($errorMsg);
	    		$validationError->setDescription($errorMsg);
	    		$validationErrors[] = $validationError;
			}
		}	    
	    return $validationErrors;
	}
	
	
	public function getFeedUrl()
	{
		$urlParams = array(
			'service' => 'uverseclicktoorderdistribution_uverseclicktoorder',
			'action' => 'getFeed',
			'partnerId' => $this->getPartnerId(),
			'distributionProfileId' => $this->getId(),
			'hash' => $this->getUniqueHashForFeedUrl(),
		);
		return requestUtils::getRequestHost() . '/api_v3/index.php?' . http_build_query($urlParams, null, '&');
	}
		
	public function getUniqueHashForFeedUrl()		{return $this->getFromCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL);}
	public function setUniqueHashForFeedUrl($v)		{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
	
	public function getBackgroundImageWide()		{return $this->getFromCustomData(self::CUSTOM_DATA_BACKGROUND_IMAGE_WIDE);}
	public function setBackgroundImageWide($v)		{$this->putInCustomData(self::CUSTOM_DATA_BACKGROUND_IMAGE_WIDE, $v);}

	public function getBackgroundImageStandard()	{return $this->getFromCustomData(self::CUSTOM_DATA_BACKGROUND_IMAGE_STANDARD);}
	public function setBackgroundImageStandard($v)	{$this->putInCustomData(self::CUSTOM_DATA_BACKGROUND_IMAGE_STANDARD, $v);}
}
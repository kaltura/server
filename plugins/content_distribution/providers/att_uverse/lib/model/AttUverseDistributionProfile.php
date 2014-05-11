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
	const CUSTOM_DATA_FLAVOR_ASSET_FILENAME_XSLT = 'flavorAssetFilenameXslt';
	const CUSTOM_DATA_THUMBNAIL_ASSET_FILENAME_XSLT = 'thumbnailAssetFilenameXslt';
	const CUSTOM_DATA_ASSET_FILENAME_XSLT = 'assetFilenameXslt';

			
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
	public function getFlavorAssetFilenameXslt()	{return $this->getFromCustomData(self::CUSTOM_DATA_FLAVOR_ASSET_FILENAME_XSLT);}
	public function getThumbnailAssetFilenameXslt()	{return $this->getFromCustomData(self::CUSTOM_DATA_THUMBNAIL_ASSET_FILENAME_XSLT);}
	public function getAssetFilenameXslt()			{return $this->getFromCustomData(self::CUSTOM_DATA_ASSET_FILENAME_XSLT);}
	
	public function setUniqueHashForFeedUrl($v)		{$this->putInCustomData(self::CUSTOM_DATA_UNIQUE_HASH_FOR_FEED_URL, $v);}
	public function setFtpPath($v)			 		{$this->putInCustomData(self::CUSTOM_DATA_FTP_PATH, $v);}
	public function setFtpUsername($v)			 	{$this->putInCustomData(self::CUSTOM_DATA_FTP_USERNAME, $v);}
	public function setFtpPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_FTP_PASSWORD, $v);}	
	public function setFtpHost($v)					{$this->putInCustomData(self::CUSTOM_DATA_FTP_HOST, $v);}
	public function setChannelTitle($v)				{$this->putInCustomData(self::CUSTOM_DATA_CHANNEL_TITLE, $v);}
	public function setFlavorAssetFilenameXslt($v)	{$this->putInCustomData(self::CUSTOM_DATA_FLAVOR_ASSET_FILENAME_XSLT, $v);}
	public function setThumbnailAssetFilenameXslt($v){$this->putInCustomData(self::CUSTOM_DATA_THUMBNAIL_ASSET_FILENAME_XSLT, $v);}
	public function setAssetFilenameXslt($v)		{$this->putInCustomData(self::CUSTOM_DATA_ASSET_FILENAME_XSLT, $v);}
	
	
	
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
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();
		
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
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
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
	
	public function getFlavorAssetFilename(EntryDistribution $entryDistribution, $defaultFilename, $flavorAssetId)
	{
		if ($this->getFlavorAssetFilenameXslt())
			return trim($this->transformXslForEntry($entryDistribution, $this->getFlavorAssetFilenameXslt(), array('flavorAssetId' => $flavorAssetId)));
		else
			return $defaultFilename;
	}
	
	public function getThumbnailAssetFilename(EntryDistribution $entryDistribution, $defaultFilename, $thumbnailAssetId)
	{
		if ($this->getThumbnailAssetFilenameXslt())
			return trim($this->transformXslForEntry($entryDistribution, $this->getThumbnailAssetFilenameXslt(), array('thumbnailAssetId' => $thumbnailAssetId)));
		else
			return $defaultFilename;
	}
	
	public function getAssetFilename(EntryDistribution $entryDistribution, $defaultFilename, $thumbnailAssetId)
	{
		if ($this->getAssetFilenameXslt())
			return trim($this->transformXslForEntry($entryDistribution, $this->getAssetFilenameXslt(), array('assetId' => $thumbnailAssetId)));
		else
			return $defaultFilename;
	}
	
	public function transformXslForEntry(EntryDistribution $entryDistribution, $xsl, $xslParams = array())
	{
		$xslParams['entryDistributionId'] = $entryDistribution->getId();
		$xslParams['distributionProfileId'] = $entryDistribution->getDistributionProfileId();
		
		$mrssDoc = $this->getEntryMrssDoc($entryDistribution);
		
		$xslDoc = new DOMDocument();
		$xslDoc->loadXML($xsl);
		
		$xslt = new XSLTProcessor;
		$xslt->registerPHPFunctions(); // it is safe to register all php fuctions here
		$xslt->setParameter('', $xslParams);
		$xslt->importStyleSheet($xslDoc);
		
		return $xslt->transformToXml($mrssDoc);
	}
	
	public function getEntryMrssDoc(EntryDistribution $entryDistribution)
	{
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
				
		// set the default criteria to use the current entry distribution partner id (it is restored later)
		// this is needed for related entries under kMetadataMrssManager which is using retrieveByPK without the correct partner id filter
		$oldEntryCriteria = entryPeer::getCriteriaFilter()->getFilter();
		myPartnerUtils::resetPartnerFilter('entry');
		entryPeer::addPartnerToCriteria($this->getPartnerId(), true);
		
		try
		{
    		$mrss = null;
    		$mrssParams = new kMrssParameters();
    		if ($this->getItemXpathsToExtend())
    			$mrssParams->setItemXpathsToExtend($this->getItemXpathsToExtend());
    		$mrss = kMrssManager::getEntryMrssXml($entry, $mrss, $mrssParams);
    		$mrssStr = $mrss->asXML();
		}
		catch (Exception $e)
		{
		    // restore the original criteria so it will not get stuck due to the exception
		    entryPeer::getCriteriaFilter()->setFilter($oldEntryCriteria);
		    throw $e;
		}
		
		// restore the original criteria
		entryPeer::getCriteriaFilter()->setFilter($oldEntryCriteria);
		
		$mrssObj = new DOMDocument();
        if(!$mrssObj->loadXML($mrssStr))
		    throw new Exception('Entry mrss xml is not valid');
		    
		return $mrssObj;
	}
	
	
}
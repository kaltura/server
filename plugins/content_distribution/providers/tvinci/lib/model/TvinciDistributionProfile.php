<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage model
 */
class TvinciDistributionProfile extends ConfigurableDistributionProfile
{
 	const CUSTOM_DATA_INGEST_URL = 'ingestUrl';
 	const CUSTOM_DATA_USERNAME = 'username';
 	const CUSTOM_DATA_PASSWORD = 'password';
	
// 	// validations
// 	const MEDIA_TITLE_MAXIMUM_LENGTH = 100;
// 	const MEDIA_DESCRIPTION_MAXIMUM_LENGTH = 5000;
// 	const MEDIA_KEYWORDS_MAXIMUM_TOTAL_LENGTH = 500;
// 	const MEDIA_KEYWORDS_MINIMUM_LENGTH_EACH_KEYWORD = 2;
// 	const MEDIA_KEYWORDS_MAXIMUM_LENGTH_EACH_KEYWORD = 30;
// 	const METADATA_CUSTOM_ID_MAXIMUM_LENGTH = 64;
// 	const TV_METADATA_EPISODE_MAXIMUM_LENGTH = 16;
// 	const TV_METADATA_SEASON_MAXIMUM_LENGTH = 16;
// 	const TV_METADATA_EPISODE_TITLE_MAXIMUM_LENGTH = 64;
// 	const TV_METADATA_SHOW_TITLE_MAXIMUM_LENGTH = 64;
// 	const TV_METADATA_TMS_ID_MAXIMUM_LENGTH = 14;
// 	const MOVIE_METADATA_TITLE_MAXIMUM_LENGTH = 64;
// 	const MOVIE_METADATA_TMS_ID_MAXIMUM_LENGTH = 14;
	
// 	const MEDIA_RATING_VALID_VALUES = 'adult,nonadult';
// 	const ALLOW_COMMENTS_VALID_VALUES = 'Always,Approve,Never';
// 	const ALLOW_RESPONSES_VALID_VALUES = 'Always,Approve,Never';
// 	const ALLOW_EMBEDDING_VALID_VALUES = 'true,false';
// 	const ALLOW_RATINGS_VALID_VALUES = 'true,false';
// 	const ADVERTISING_INVIDEO_VALID_VALUES = 'Allow,Deny';
// 	const ADVERTISING_ADSENSE_FOR_VIDEO_VALUES = 'Allow,Deny';
// 	const DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE_VALUES = 'Allow,Deny';
// 	const URGENT_REFERENCE_FILE_VALUES = 'yes,no';
// 	const KEEP_FINGERPRINT_VALUES = 'yes,no';

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return TvinciDistributionPlugin::getProvider();
	}
	
		
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		$maxLengthFields = array (
// 		    TvinciDistributionField::MEDIA_DESCRIPTION => self::MEDIA_DESCRIPTION_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::MEDIA_TITLE => self::MEDIA_TITLE_MAXIMUM_LENGTH,
// 			TvinciDistributionField::MEDIA_KEYWORDS => self::MEDIA_KEYWORDS_MAXIMUM_TOTAL_LENGTH,
// 		    TvinciDistributionField::WEB_METADATA_CUSTOM_ID => self::METADATA_CUSTOM_ID_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::MOVIE_METADATA_CUSTOM_ID => self::METADATA_CUSTOM_ID_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_CUSTOM_ID => self::METADATA_CUSTOM_ID_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_EPISODE => self::TV_METADATA_EPISODE_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_EPISODE_TITLE => self::TV_METADATA_EPISODE_TITLE_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_SEASON => self::TV_METADATA_SEASON_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_SHOW_TITLE => self::TV_METADATA_SHOW_TITLE_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_TMS_ID => self::TV_METADATA_TMS_ID_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::MOVIE_METADATA_TITLE => self::MOVIE_METADATA_TITLE_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::MOVIE_METADATA_TMS_ID => self::MOVIE_METADATA_TMS_ID_MAXIMUM_LENGTH,
		);
		    		
		$inListOrNullFields = array (
// 		    TvinciDistributionField::MEDIA_RATING => explode(',', self::MEDIA_RATING_VALID_VALUES),
// 		    TvinciDistributionField::ALLOW_COMMENTS => explode(',', self::ALLOW_COMMENTS_VALID_VALUES),
// 		    TvinciDistributionField::ALLOW_EMBEDDING => explode(',', self::ALLOW_EMBEDDING_VALID_VALUES),
// 		    TvinciDistributionField::ALLOW_RATINGS => explode(',', self::ALLOW_RATINGS_VALID_VALUES),
// 		    TvinciDistributionField::ALLOW_RESPONSES => explode(',', self::ALLOW_RESPONSES_VALID_VALUES),
// 		    TvinciDistributionField::ADVERTISING_INVIDEO => explode(',', self::ADVERTISING_INVIDEO_VALID_VALUES),
// 		    TvinciDistributionField::ADVERTISING_ADSENSE_FOR_VIDEO => explode(',', self::ADVERTISING_ADSENSE_FOR_VIDEO_VALUES),
// 		    TvinciDistributionField::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE => explode(',', self::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE_VALUES),
// 		    TvinciDistributionField::URGENT_REFERENCE_FILE => explode(',', self::URGENT_REFERENCE_FILE_VALUES),
// 		    TvinciDistributionField::KEEP_FINGERPRINT => explode(',', self::KEEP_FINGERPRINT_VALUES),
		);
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($maxLengthFields, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($inListOrNullFields, $allFieldValues, $action));

		return $validationErrors;
	}

	public function getIngestUrl()				{return $this->getFromCustomData(self::CUSTOM_DATA_INGEST_URL);}
	public function setIngestUrl($v)			{$this->putInCustomData(self::CUSTOM_DATA_INGEST_URL, $v);}
	
	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}

	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	
	public function getPublisher()				{return $this->getFromCustomData(self::CUSTOM_DATA_PUBLISHER);}
	public function setPublisher($v)			{$this->putInCustomData(self::CUSTOM_DATA_PUBLISHER, $v);}

	protected function getDefaultFieldConfigArray()
	{
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();

	    // Set the default XSL expression for AUTOMATIC_DISTRIBUTION_CONDITIONS
	    $fieldConfig = $fieldConfigArray[ConfigurableDistributionField::AUTOMATIC_DISTRIBUTION_CONDITIONS];
	    if ( $fieldConfig )
	    {
			$fieldConfig->setEntryMrssXslt('<xsl:if test="customData/metadata/WorkflowStatus = \'Approved\'">Approved For Automatic Distribution</xsl:if>');
	    }

	    // media fields
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(TvinciDistributionField::MEDIA_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(TvinciDistributionField::MEDIA_DESCRIPTION);
	    $fieldConfig->setUserFriendlyFieldName('Entry description');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

	    $activatePublishingXSLT = '<xsl:choose>'
	    							. '<xsl:when test="customData/metadata/Activate = \'Yes\'">true</xsl:when>'
	    							. '<xsl:otherwise>false</xsl:otherwise>'
	    						. '</xsl:choose>';
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ACTIVATE_PUBLISHING, 'Activate Publishing', 'Activate', false, DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER, $activatePublishingXSLT);

	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::MEDIA_TYPE, 'Media Type', 'MediaType');

	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::GEO_BLOCK_RULE, 'Geo Block Rule', 'GeoBlockRule');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::WATCH_PERMISSIONS_RULE, 'Watch Permission Rule', 'WatchPermissionRule');

	    // Language
	    $languageXSLT =	'<xsl:choose>'
							. '<xsl:when test="customData/metadata/Language != \'\'">'
								. '<xsl:value-of select="customData/metadata/Language"/>'
							. '</xsl:when>'
							. '<xsl:otherwise><xsl:text>eng</xsl:text></xsl:otherwise>'
						. '</xsl:choose>';
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::LANGUAGE, 'Language', 'Language', false, DistributionFieldRequiredStatus::NOT_REQUIRED, $languageXSLT);

	    // Dates
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::START_DATE, 'Start Date', 'StartDate');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::END_DATE, 'End Date', 'FinalEndDate');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::CATALOG_START_DATE, 'Catalog Start Date', 'CatalogStartDate');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::CATALOG_END_DATE, 'Catalog End Date', 'CatalogEndDate');
	    	    
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_RUNTIME, 'Runtime', 'Runtime');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_RELEASE_YEAR, 'Release Year', 'ReleaseYear');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_RELEASE_DATE, 'Release Date', 'ReleaseDate');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_GENRE, 'Genre', 'Genre', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_SUB_GENRE, 'Sub Genre', 'SubGenre', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_RATING, 'Rating', 'Rating', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_COUNTRY, 'Country', 'Country', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_CAST, 'Cast', 'Cast', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_DIRECTOR, 'Director', 'Director', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_AUDIO_LANGUAGE, 'Audio Language', 'AudioLanguage', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_STUDIO, 'Studio', 'Studio', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_STUDIO, 'Studio', 'Studio', true);
	    
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_ASSET_MAIN, 'Main Video Asset', 'MainVideoAsset');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_ASSET_TABLET_MAIN, 'Tablet Video Asset', 'TabletMainVideoAsset');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_ASSET_SMARTPHONE_MAIN, 'Smartphone Video Asset', 'SmartphoneMainVideoAsset');

	    return $fieldConfigArray;
	}
	
	protected function addMetadataDistributionFieldConfig(array &$array, $name, $friendlyName, $metadataName, $multiValue = false, $required = DistributionFieldRequiredStatus::NOT_REQUIRED, $xslt = null)
	{
		$metadataPath = "customData/metadata/$metadataName";
		if ( is_null($xslt) )
		{
			if ( ! $multiValue ) // Single value
			{
				$xslt = '<xsl:value-of select="string('. $metadataPath . ')" />';
			}
			else
			{
				$xslt = '<xsl:for-each select="'. $metadataPath . '">'
							. '<xsl:if test="position() &gt; 1">'
							. '<xsl:text>,</xsl:text>'
							. '</xsl:if>'
							. '<xsl:value-of select="string(.)" />'
						. '</xsl:for-each>'
					;
			}
		}
		
		$updateMetadataArray = array( "/*[local-name()='metadata']/*[local-name()='$metadataName']" );
		
		$this->addDistributionFieldConfig($array, $name, $friendlyName, $xslt, $required, true, $updateMetadataArray);
	}
	
	protected function addDistributionFieldConfig(array &$array, $name, $friendlyName, $xslt, $required = DistributionFieldRequiredStatus::NOT_REQUIRED, $updateOnChange = false, $updateOnParams = array())
	{
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName($name);
		$fieldConfig->setUserFriendlyFieldName($friendlyName);
		$fieldConfig->setEntryMrssXslt($xslt);
		if ($updateOnChange)
			$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setIsRequired($required);
		$fieldConfig->setUpdateParams($updateOnParams);
		$array[$name] = $fieldConfig;
	}

	protected function removeDistributionFieldConfigs(array &$fieldConfigArray, array $fields)
	{
		foreach($fields as $field)
		{
			if (isset($fieldConfigArray[$field]))
				unset($fieldConfigArray[$field]);
		}
	}
}

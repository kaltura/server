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

		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}

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
	    $fieldConfig->setFieldName(TvinciDistributionField::METADATA_SCHEMA_ID);
	    $fieldConfig->setUserFriendlyFieldName('Schema Id');
	    $fieldConfig->setEntryMrssXslt('2'); // See TvinciDistributionField::METADATA_SCHEMA_ID
	    $fieldConfig->setUpdateOnChange(false);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

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

	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_SHORT_TITLE, 'Short Title', 'ShortTitle', false);
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_DIMENSION, 'Dimension', 'Dimension', false);
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_BILLING_TYPE, 'Billing Type', 'BillingType', false);

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
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_PARENTAL_RATING, 'Parental Rating', 'ParentalRating', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_COUNTRY, 'Country', 'Country', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_CAST, 'Cast', 'Cast', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_MAIN_CAST, 'Main Cast', 'MainCast', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_DIRECTOR, 'Director', 'Director', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_AUDIO_LANGUAGE, 'Audio Language', 'AudioLanguage', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_STUDIO, 'Studio', 'Studio', true);

	    // Video assets configuration (see KalturaTvinciDistributionJobProviderData::initPlayManifestUrls())
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(TvinciDistributionField::VIDEO_ASSETS_CONFIGURATION);
	    $fieldConfig->setUserFriendlyFieldName('Video assets configuration');
	    $fieldConfig->setEntryMrssXslt('');
	    $fieldConfig->setUpdateOnChange(false);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

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

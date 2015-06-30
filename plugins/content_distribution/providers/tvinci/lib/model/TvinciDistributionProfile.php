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
 	const CUSTOM_DATA_SCHEMA_ID = 'schemaId';
 	const CUSTOM_DATA_LANGUAGE = 'language';

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

	public function getSchemaId()				{return $this->getFromCustomData(self::CUSTOM_DATA_SCHEMA_ID);}
	public function setSchemaId($v)				{$this->putInCustomData(self::CUSTOM_DATA_SCHEMA_ID, $v);}

	public function getLanguage()				{return $this->getFromCustomData(self::CUSTOM_DATA_LANGUAGE);}
	public function setLanguage($v)				{$this->putInCustomData(self::CUSTOM_DATA_LANGUAGE, $v);}

	protected function getDefaultFieldConfigArray()
	{
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();

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

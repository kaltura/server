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
 	//const CUSTOM_DATA_XSLT = 'xsltFile';

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

	/*public function getXsltFile()				{return $this->getFromCustomData(self::CUSTOM_DATA_XSLT);}
	public function setXsltFile($v)				{$this->putInCustomData(self::CUSTOM_DATA_XSLT, $v);}*/

}

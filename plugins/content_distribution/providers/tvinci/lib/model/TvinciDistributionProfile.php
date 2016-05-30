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
 	const CUSTOM_DATA_XSLT = 'xsltFile';
	const CUSTOM_TAGS = 'tags';
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return TvinciDistributionPlugin::getProvider();
	}

	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(TvinciDistributionField::CUSTOM);
		$fieldConfig->setUserFriendlyFieldName('Custom Data:');
		$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setIsDefault(true);
		$fieldConfig->setUpdateParams( array( entryPeer::CUSTOM_DATA, entryPeer::DESCRIPTION, entryPeer::NAME));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		return $fieldConfigArray;

	}

	public function getUpdateRequiredMetadataXPaths()
	{
		$metadataConfigArray = parent::getUpdateRequiredMetadataXPaths();
		/* we want any change to the metadata to create an update possibility */
		$metadataConfigArray[] = TvinciDistributionField::META;

		return $metadataConfigArray;

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

	public function getXsltFile()				{return $this->getFromCustomData(self::CUSTOM_DATA_XSLT);}
	public function setXsltFile($v)				{$this->putInCustomData(self::CUSTOM_DATA_XSLT, $v);}

	public function getIngestUrl()				{return $this->getFromCustomData(self::CUSTOM_DATA_INGEST_URL);}
	public function setIngestUrl($v)			{$this->putInCustomData(self::CUSTOM_DATA_INGEST_URL, $v);}

	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}

	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}

	public function getPublisher()				{return $this->getFromCustomData(self::CUSTOM_DATA_PUBLISHER);}
	public function setPublisher($v)			{$this->putInCustomData(self::CUSTOM_DATA_PUBLISHER, $v);}

	public function getTags()	                {return $this->getFromCustomData(self::CUSTOM_TAGS);}
	public function setTags($v)	                {$this->putInCustomData(self::CUSTOM_TAGS, $v);}
}

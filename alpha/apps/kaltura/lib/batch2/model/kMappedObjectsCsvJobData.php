<?php
/**
 * @package Core
 * @subpackage model.data
 */
abstract class kMappedObjectsCsvJobData extends kExportCsvJobData
{
	/**
	 * he metadata profile we should look the xpath in.
	 * @var int
	 */
	protected $metadataProfileId;

	/**
	 * The xpath to look in the metadataProfileId and the wanted csv field name
	 * @var array
	 */
	protected $additionalFields;
	
	/**
	 * Dynamic mapping between kUser core fields and report columns
	 * @var array
	 */
	protected $mappedFields;

	/**
	 * @return KalturaKeyValue
	 */
	public function getMappedFields ()
	{
		return $this->mappedFields;
	}
	
	/**
	 * @param array $mappedFields
	 */
	public function setMappedFields ($mappedFields)
	{
		$this->mappedFields=$mappedFields;
	}
	
	/**
	 * @return integer
	 */
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
	}

	/**
	 * @param integer $metadataProfileId
	 */
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}

	/**
	 * @return array
	 */
	public function getAdditionalFields()
	{
		return $this->additionalFields;
	}

	/**
	 * @param array $additionalFields
	 */
	public function setAdditionalFields($additionalFields)
	{
		$this->additionalFields = $additionalFields;
	}
}

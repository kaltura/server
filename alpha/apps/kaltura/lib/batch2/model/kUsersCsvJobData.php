<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kUsersCsvJobData extends kExportCsvJobData
{

	/**
	 * he metadata profile we should look the xpath in.
	 * @var int
	 */
	private $metadataProfileId;


	/**
	 * The xpath to look in the metadataProfileId and the wanted csv field name
	 * @var array
	 */
	private $additionalFields;

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
	
	public function getEngineType ()
	{
		return ExportObjectType::USER;
	}

}

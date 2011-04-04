<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadBaseJobData extends kJobData
{
	const BULK_UPLOAD_CSV_VERSION_V1 = 1; // 5 values in a row
	const BULK_UPLOAD_CSV_VERSION_V2 = 2; // 12 values in a row
	
	
	/**
	 * @var int
	 */
	private $userId;
	
	/**
	 * The screen name of the user
	 * 
	 * @var string
	 */
	private $uploadedBy;
	
	/**
	 * Selected profile id for all bulk entries
	 * 
	 * @var int
	 */
	private $conversionProfileId;
	
	/**
	 * Number of created entries
	 * 
	 * @var int
	 */
	private $numOfEntries;
	
	/**
	 * @return the $userId
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return the $uploadedBy
	 */
	public function getUploadedBy()
	{
		return $this->uploadedBy;
	}

	/**
	 * @return the $conversionProfileId
	 */
	public function getConversionProfileId()
	{
		return $this->conversionProfileId;
	}

	/**
	 * @return the $numOfEntries
	 */
	public function getNumOfEntries()
	{
		return $this->numOfEntries;
	}

	/**
	 * @param int $userId
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}

	/**
	 * @param string $uploadedBy
	 */
	public function setUploadedBy($uploadedBy)
	{
		$this->uploadedBy = $uploadedBy;
	}

	/**
	 * @param int $conversionProfileId
	 */
	public function setConversionProfileId($conversionProfileId)
	{
		$this->conversionProfileId = $conversionProfileId;
	}

	/**
	 * @param int $numOfEntries
	 */
	public function setNumOfEntries($numOfEntries)
	{
		$this->numOfEntries = $numOfEntries;
	}
}

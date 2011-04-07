<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadBaseJobData extends kJobData
{
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
	 * 
	 * The bulk type such as xml, csv
	 * @var unknown_type
	 */
	private $bulkType;
	
	/**
	 * @return the $bulkType
	 */
	public function getBulkType() {
		return $this->bulkType;
	}

	/**
	 * @param unknown_type $bulkType
	 */
	public function setBulkType($bulkType) {
		$this->bulkType = $bulkType;
	}

	/**
	 * @return the $userId
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * @return the $uploadedBy
	 */
	public function getUploadedBy() {
		return $this->uploadedBy;
	}

	/**
	 * @return the $conversionProfileId
	 */
	public function getConversionProfileId() {
		return $this->conversionProfileId;
	}

	/**
	 * @return the $numOfEntries
	 */
	public function getNumOfEntries() {
		return $this->numOfEntries;
	}

	/**
	 * @param int $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
	}

	/**
	 * @param string $uploadedBy
	 */
	public function setUploadedBy($uploadedBy) {
		$this->uploadedBy = $uploadedBy;
	}

	/**
	 * @param int $conversionProfileId
	 */
	public function setConversionProfileId($conversionProfileId) {
		$this->conversionProfileId = $conversionProfileId;
	}

	/**
	 * @param int $numOfEntries
	 */
	public function setNumOfEntries($numOfEntries) {
		$this->numOfEntries = $numOfEntries;
	}
}

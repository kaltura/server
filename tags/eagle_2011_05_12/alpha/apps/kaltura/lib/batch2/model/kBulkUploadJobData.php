<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadJobData extends kJobData
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
	 * Created by the API
	 * 
	 * @var string
	 */
	private $resultsFileLocalPath;
	
	/**
	 * Created by the API
	 * 
	 * @var string
	 */
	private $resultsFileUrl;

	/**
	 * 
	 * The bulk upload job file path
	 * @var string
	 */
	private $filePath;
	
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
	
	/**
	 * @return the $filePath
	 */
	public function getFilePath() {
		return $this->filePath;
	}

	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath) {
		$this->filePath = $filePath;
	}

	/**
	 * @return the $resultsFileLocalPath
	 */
	public function getResultsFileLocalPath() {
		return $this->resultsFileLocalPath;
	}

	/**
	 * @return the $resultsFileUrl
	 */
	public function getResultsFileUrl() {
		return $this->resultsFileUrl;
	}

	/**
	 * @param string $resultsFileLocalPath
	 */
	public function setResultsFileLocalPath($resultsFileLocalPath) {
		$this->resultsFileLocalPath = $resultsFileLocalPath;
	}

	/**
	 * @param string $resultsFileUrl
	 */
	public function setResultsFileUrl($resultsFileUrl) {
		$this->resultsFileUrl = $resultsFileUrl;
	}
}

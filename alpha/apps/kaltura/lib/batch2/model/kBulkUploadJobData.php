<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadJobData extends kBulkUploadBaseJobData
{
	const BULK_UPLOAD_CSV_VERSION_V1 = 1; // 5 values in a row
	const BULK_UPLOAD_CSV_VERSION_V2 = 2; // 12 values in a row
	
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


	//TODO: Roni - delete this once csv KBulkUploadCsvJobData is working
		/**
	 * Created by the API
	 * 
	 * @var string
	 */
	private $csvFilePath;
	
	/**
	 * The version of the csv file
	 * 
	 * @var int
	 */
	private $csvVersion;

	/**
	 * @return the $csvFilePath
	 */
	public function getCsvFilePath() {
		return $this->csvFilePath;
	}
		
	/**
	 * @return the $csvVersion
	 */
	public function getCsvVersion() {
		return $this->csvVersion;
	}
	
	/**
	 * @param string $csvFilePath
	 */
	public function setCsvFilePath($csvFilePath) {
		$this->csvFilePath = $csvFilePath;
	}
	
	/**
	 * @param int $csvVersion
	 */
	public function setCsvVersion($csvVersion) {
		$this->csvVersion = $csvVersion;
	}
	
}

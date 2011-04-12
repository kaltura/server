<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadJobData extends kBulkUploadBaseJobData
{
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

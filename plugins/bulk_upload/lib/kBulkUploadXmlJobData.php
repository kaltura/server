<?php
/**
 * @package plugins
 * @subpackage lib
 */
class kBulkUploadXmlJobData extends kBulkUploadJobData
{
	/**
	 * Created by the API
	 * 
	 * @var string
	 */
	private $xmlFilePath;
		
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
	 * The version of the csv file
	 * 
	 * @var int
	 */
	private  $xmlVersion;
	
	/**
	 * @return the $xmlFilePath
	 */
	public function getXmlFilePath() {
		return $this->xmlFilePath;
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
	 * @return the $xmlVersion
	 */
	public function getXmlVersion() {
		return $this->xmlVersion;
	}

	/**
	 * @param string $xmlFilePath
	 */
	public function setXmlFilePath($xmlFilePath) {
		$this->xmlFilePath = $xmlFilePath;
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

	/**
	 * @param int $xmlVersion
	 */
	public function setXmlVersion($xmlVersion) {
		$this->xmlVersion = $xmlVersion;
	}


}
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
	private $csvFilePath;
	
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
	private $csvVersion;
	
	/**
	 * @return the $csvFilePath
	 */
	public function getCsvFilePath()
	{
		return $this->csvFilePath;
	}

	/**
	 * @return the $resultsFileLocalPath
	 */
	public function getResultsFileLocalPath()
	{
		return $this->resultsFileLocalPath;
	}

	/**
	 * @return the $resultsFileUrl
	 */
	public function getResultsFileUrl()
	{
		return $this->resultsFileUrl;
	}

	/**
	 * @return the $csvVersion
	 */
	public function getCsvVersion()
	{
		return $this->csvVersion;
	}

	/**
	 * @param string $csvFilePath
	 */
	public function setCsvFilePath($csvFilePath)
	{
		$this->csvFilePath = $csvFilePath;
	}

	/**
	 * @param string $resultsFileLocalPath
	 */
	public function setResultsFileLocalPath($resultsFileLocalPath)
	{
		$this->resultsFileLocalPath = $resultsFileLocalPath;
	}

	/**
	 * @param string $resultsFileUrl
	 */
	public function setResultsFileUrl($resultsFileUrl)
	{
		$this->resultsFileUrl = $resultsFileUrl;
	}

	/**
	 * @param int $csvVersion
	 */
	public function setCsvVersion($csvVersion)
	{
		$this->csvVersion = $csvVersion;
	}
}

<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kBulkUploadJobData
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
	 * Number of created entries
	 * 
	 * @var int
	 */
	private $numOfEntries;
	
	/**
	 * The version of the csv file
	 * 
	 * @var int
	 */
	public $csvVersion;
	
	
	
	
	/**
	 * @return the $userId
	 */
	public function getUserId()
	{
		return $this->userId;
	}
	/**
	 * @param $csvVersion the $csvVersion to set
	 */
	public function setCsvVersion($csvVersion)
	{
		$this->csvVersion = $csvVersion;
	}

	/**
	 * @return the $csvVersion
	 */
	public function getCsvVersion()
	{
		return $this->csvVersion;
	}

	/**
	 * @param $numOfEntries the $numOfEntries to set
	 */
	public function setNumOfEntries($numOfEntries)
	{
		$this->numOfEntries = $numOfEntries;
	}

	/**
	 * @return the $numOfEntries
	 */
	public function getNumOfEntries()
	{
		return $this->numOfEntries;
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
	 * @param $userId the $userId to set
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}

	/**
	 * @param $uploadedBy the $uploadedBy to set
	 */
	public function setUploadedBy($uploadedBy)
	{
		$this->uploadedBy = $uploadedBy;
	}

	/**
	 * @param $conversionProfileId the $conversionProfileId to set
	 */
	public function setConversionProfileId($conversionProfileId)
	{
		$this->conversionProfileId = $conversionProfileId;
	}

	/**
	 * @param $csvFilePath the $csvFilePath to set
	 */
	public function setCsvFilePath($csvFilePath)
	{
		$this->csvFilePath = $csvFilePath;
	}

	/**
	 * @param $resultsFileLocalPath the $resultsFileLocalPath to set
	 */
	public function setResultsFileLocalPath($resultsFileLocalPath)
	{
		$this->resultsFileLocalPath = $resultsFileLocalPath;
	}

	/**
	 * @param $resultsFileUrl the $resultsFileUrl to set
	 */
	public function setResultsFileUrl($resultsFileUrl)
	{
		$this->resultsFileUrl = $resultsFileUrl;
	}
	
}

?>
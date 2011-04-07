<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadCsvJobData extends kBulkUploadJobData
{
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
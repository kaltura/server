<<<<<<< .mine
<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadCsvJobData extends KalturaBulkUploadJobData
{
	const BULK_UPLOAD_CSV_VERSION_V1 = 1; // 5 values in a row
	const BULK_UPLOAD_CSV_VERSION_V2 = 2; // 12 values in a row

	/**
	 * The version of the csv file
	 * 
	 * @var int
	 */
	private $csvVersion;

	/**
	 * @return the $csvVersion
	 */
	public function getCsvVersion() {
		return $this->csvVersion;
	}
	
	/**
	 * @param int $csvVersion
	 */
	public function setCsvVersion($csvVersion) {
		$this->csvVersion = $csvVersion;
	}
}=======
<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadCsvJobData extends KalturaBulkUploadJobData
{
	const BULK_UPLOAD_CSV_VERSION_V1 = 1; // 5 values in a row
	const BULK_UPLOAD_CSV_VERSION_V2 = 2; // 12 values in a row
	
	/**
	 * Created by the API
	 * 
	 * @var KalturaBulkUploadCsvVersion
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
}>>>>>>> .r60352

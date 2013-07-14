<?php
/**
 * @package plugins.bulkUploadCsv
 * @subpackage model.data
 */
class kBulkUploadCsvJobData extends kBulkUploadJobData
{
	const BULK_UPLOAD_CSV_VERSION_V1 = 1; // 5 values in a row
	const BULK_UPLOAD_CSV_VERSION_V2 = 2; // 12 values in a row

	/**
	 * The version of the csv file
	 * 
	 * @var int
	 */
	protected $csvVersion;
	
	/**
	 * Array containing the column headers of the CSV file
	 * @var array
	 */
	protected $columns;

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
	/**
     * @return array
     */
    public function getColumns ()
    {
        return $this->columns;
    }

	/**
     * @param array $columns
     */
    public function setColumns ($columns)
    {
        $this->columns = $columns;
    }

}

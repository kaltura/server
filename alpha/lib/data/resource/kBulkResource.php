<?php
/**
 * Used to ingest media that is available on remote server and accessible using the supplied URL, media file will be downloaded using import job in order to make the asset ready. The bulk upload id will be saved on the entry.
 *
 * @package Core
 * @subpackage model.data
 */
class kBulkResource extends kUrlResource 
{
	/**
	 * ID of the bulk upload job to be associated with the entry 
	 * @var int
	 */
	private $bulkUploadId;
	
	/**
	 * @return the $bulkUploadId
	 */
	public function getBulkUploadId()
	{
		return $this->bulkUploadId;
	}

	/**
	 * @param int $bulkUploadId
	 */
	public function setBulkUploadId($bulkUploadId)
	{
		$this->bulkUploadId = $bulkUploadId;
	}
}
<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUpload extends KalturaObject
{
	/**
	 * @var int
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $uploadedBy;
	
	/**
	 * @var string
	 */
	public $uploadedByUserId;
	
	/**
	 * @var int
	 */
	public $uploadedOn;
	
	/**
	 * @var int
	 */
	public $numOfEntries;
	
	/**
	 * @var KalturaBatchJobStatus
	 */
	public $status;
	
	/**
	 * @var string
	 */
	public $logFileUrl;
	
	/**
	 * @var string;
	 */
	public $csvFileUrl;
	
	/**
	 * @var KalturaBulkUploadResultArray;
	 */
	public $results;
	
	public function fromObject($batchJob)
	{
		if($batchJob->getJobType() != BatchJobType::BULKUPLOAD)
			throw new Exception("Bulk upload object can be initialized from bulk upload job only");
		
		$this->id = $batchJob->getId();
		$this->uploadedOn = $batchJob->getCreatedAt(null);
		$this->status = $batchJob->getStatus();
		$this->error = $batchJob->getDescription();

		$this->logFileUrl = requestUtils::getHost() . "/index.php/extwidget/bulkuploadfile/id/{$batchJob->getId()}/pid/{$batchJob->getPartnerId()}/type/log";
		$this->csvFileUrl = requestUtils::getCdnHost() . "/index.php/extwidget/bulkuploadfile/id/{$batchJob->getId()}/pid/{$batchJob->getPartnerId()}/type/csv";
					
		$jobData = $batchJob->getData();
		if($jobData instanceof kBulkUploadJobData)
		{
			$this->uploadedBy = $jobData->getUploadedBy();
			$this->uploadedByUserId = $jobData->getUserId();
			$this->numOfEntries = $jobData->getNumOfEntries();
		}
		
//		$results = BulkUploadResultPeer::retrieveByBulkUploadId($this->id);
//		$this->results = KalturaBulkUploadResultArray::fromBulkUploadResultArray($results);
	}
}
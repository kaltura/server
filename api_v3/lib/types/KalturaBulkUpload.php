<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUpload extends KalturaObject implements IFilterable
{
	/**
	 * @var bigint
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
	 * @var time
	 * @filter gte,lte,eq
	 */
	public $uploadedOn;
	
	/**
	 * @var int
	 * @deprecated User $numOfObjects instead
	 */
	public $numOfEntries;
	
	/**
	 * @var KalturaBatchJobStatus
	 * @filter in,eq
	 */
	public $status;
	
	/**
	 * @var string
	 */
	public $logFileUrl;
	
	/**
	 * @var string;
	 * @deprecated
	 */
	public $csvFileUrl;
	
	/**
	 * @var string;
	 */
	public $bulkFileUrl;
	
	/**
	 * @var KalturaBulkUploadType;
	 */
	public $bulkUploadType;
	
	
	
	/**
	 * @var KalturaBulkUploadResultArray;
	 */
	public $results;
	
	/**
	 * @var string
	 */
	public $error;
	
	/**
	 * @var KalturaBatchJobErrorTypes
	 */
	public $errorType;
	
	/**
	 * @var int
	 */
	public $errorNumber;
	
	/**
	 * @var string
	 */
	public $fileName;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var int
	 *
	 */
	public $numOfObjects;
	
	/**
	 * @var KalturaBulkUploadObjectType
	 * @filter eq,in
	 */
	public $bulkUploadObjectType;
	
	/**
	 * Mapping between the API object properties and te core object properties.
	 * @var unknown_type
	 */
	static private $map_between_objects = array(
	    "id" => "jobId",
	    "uploadedOn" => "createdAt",
	    "status",
	    "error" => "message",
	    "description",
	    "bulkUploadType" => "jobSubType",
	    
	);
	
    public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function fromObject($batchJobObject, IResponseProfile $responseProfile = null)
	{
	    /* @var $batchJobObject BatchJobLog */
	    if (is_null($batchJobObject))
	    {
	        return null;
	    }
		
		if($batchJobObject->getJobType() != BatchJobType::BULKUPLOAD)
			throw new Exception("Bulk upload object can be initialized from bulk upload job only");
		
		parent::fromObject($batchJobObject, $responseProfile);
		
		$this->uploadedOn = $batchJobObject->getCreatedAt(null);
		
		$this->logFileUrl = requestUtils::getHost() . "/api_v3/service/bulkUpload/action/serveLog/id/{$batchJobObject->getJobId()}/ks/" . kCurrentContext::$ks;
//		$this->logFileUrl = requestUtils::getHost() . "/index.php/extwidget/bulkuploadfile/id/{$batchJob->getId()}/pid/{$batchJob->getPartnerId()}/type/log";
		$this->bulkFileUrl = requestUtils::getHost() . "/api_v3/service/bulkUpload/action/serve/id/{$batchJobObject->getJobId()}/ks/" . kCurrentContext::$ks;
//		$this->bulkFileUrl = requestUtils::getCdnHost() . "/index.php/extwidget/bulkuploadfile/id/{$batchJob->getId()}/pid/{$batchJob->getPartnerId()}/type/$type";
		$this->csvFileUrl = $this->bulkFileUrl;
		if (method_exists(get_class($batchJobObject), "getParam1"))
			    $this->bulkUploadObjectType = $batchJobObject->getParam1();
		
	    //if (isset ())
		$jobData = $batchJobObject->getData();
		if($jobData && $jobData instanceof kBulkUploadJobData)
		{
			$this->uploadedBy = $jobData->getUploadedBy();
			$this->uploadedByUserId = $jobData->getUserId();
			$this->numOfEntries = $jobData->getNumOfEntries();
			$this->numOfObjects = $jobData->getNumOfObjects();
			$this->fileName = $jobData->getFileName();
			$this->bulkUploadObjectType = BulkUploadObjectType::ENTRY;
			if ($jobData->getBulkUploadObjectType())
			    $this->bulkUploadObjectType = $jobData->getBulkUploadObjectType();
			    
			if(!$jobData->getFilePath())
			{
				$this->csvFileUrl = null;
				$this->bulkFileUrl = null;
			}
		}
	}

	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
	    return array();
	}
	
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
	    return array();
	}
}
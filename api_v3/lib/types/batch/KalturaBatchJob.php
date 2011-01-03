<?php
/**
 * @package api
 * @subpackage objects
 */

/**
 */
class KalturaBatchJob extends KalturaBaseJob implements IFilterable
{
	/**
	 * @var string
	 * @filter eq
	 */
	public $entryId;
	
	/**
	 * @var string
	 */
	public $entryName;
	
	/**
	 * @var KalturaBatchJobType
	 * @readonly 
	 * @filter eq,in,notin
	 */
    public $jobType;
    
	/**
	 * @var int
	 * @filter eq,in
	 */
    public $jobSubType;
    
	/**
	 * @var int
	 * @filter in
	 */
    public $onStressDivertTo;
    
    
	/**
	 * @var KalturaJobData
	 */
    public $data;

    /**
	 * @var KalturaBatchJobStatus
	 * @filter eq,in,order
	 */
    public $status;
    
    /**
	 * @var int
	 */
    public $abort;
    
    /**
	 * @var int
	 */
    public $checkAgainTimeout;

    /**
	 * @var int
	 */
    public $progress;
    
    /**
	 * @var string
	 */
    public $message ;
    
    /**
	 * @var string
	 */
    public $description ;
    
    /**
	 * @var int
	 */
    public $updatesCount ;
    
    /**
	 * @var int
	 * @filter gte,lte
	 */
    public $priority ;
    
    
    /**
     * The id of identical job
	 * @var int
	 */    
    public $twinJobId;
    
    
    /**
     * The id of the bulk upload job that initiated this job
	 * @var int
	 */    
    public $bulkJobId;
    
    
    /**
     * When one job creates another - the parent should set this parentJobId to be its own id.
	 * @var int
	 */    
    public $parentJobId;
    
    
    /**
     * The id of the root parent job
	 * @var int
	 */    
    public $rootJobId;
    
    
    /**
     * The time that the job was pulled from the queue
	 * @var int
	 * @filter gte,lte,order
	 */    
    public $queueTime;
    
    
    /**
     * The time that the job was finished or closed as failed
	 * @var int
	 * @filter gte,lte,order
	 */    
    public $finishTime;
    
    
    /**
	 * @var KalturaBatchJobErrorTypes
	 * @filter in
	 */    
    public $errType;
    
    
    /**
	 * @var int
	 */    
    public $errNumber;
    
    
    /**
	 * @var int
	 * @filter lt,gt
	 */    
    public $fileSize;
    
    
    /**
	 * @var bool
	 */    
    public $lastWorkerRemote;
	
    
    /**
	 * @var int
	 */    
    public $schedulerId;
	
    
    /**
	 * @var int
	 */    
    public $workerId;
	
    
    /**
	 * @var int
	 */    
    public $batchIndex;
	
    
    /**
	 * @var int
	 */    
    public $lastSchedulerId;
	
    
    /**
	 * @var int
	 */    
    public $lastWorkerId;
	
    
    /**
	 * @var int
	 */    
    public $dc;

	
	private static $map_between_objects = array
	(
		"entryId" ,
		"jobType" , 
	 	"status" , "abort" , "checkAgainTimeout" , "progress" ,
		"message", "description" , "updatesCount" , "parentJobId" ,
		"rootJobId", "bulkJobId" , "twinJobId" , "priority" ,
		"queueTime" , "finishTime" , "errType", "errNumber", "fileSize",
		"lastWorkerRemote", "onStressDivertTo",
		"schedulerId",
		"workerId",
		"batchIndex",
		"lastSchedulerId",
		"lastWorkerId",
		"dc",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	    
	public function fromStatisticsObject($dbBatchJob)
	{
		$this->fromObject($dbBatchJob);
		
		if(!($dbBatchJob instanceof BatchJob))
			return $this;
			
		$entry = $dbBatchJob->getEntry(true);
		if($entry)
			$this->entryName = $entry->getName();
		
		return $this;
	}
	    
	public function fromData($dbData)
	{
		if(!$dbData)
			return;
			
		switch(get_class($dbData))
		{
			case 'kBulkUploadJobData':
				$this->data = new KalturaBulkUploadJobData();
				break;
				
			case 'kConvartableJobData':
				$this->data = new KalturaConvartableJobData();
				break;
				
			case 'kConvertJobData':
				$this->data = new KalturaConvertJobData();
				break;
				
			case 'kConvertProfileJobData':
				$this->data = new KalturaConvertProfileJobData();
				break;
				
			case 'kExtractMediaJobData':
				$this->data = new KalturaExtractMediaJobData();
				break;
				
			case 'kImportJobData':
				$this->data = new KalturaImportJobData();
				break;
				
			case 'kPostConvertJobData':
				$this->data = new KalturaPostConvertJobData();
				break;
				
			case 'kPullJobData':
				$this->data = new KalturaPullJobData();
				break;
				
			case 'kRemoteConvertJobData':
				$this->data = new KalturaRemoteConvertJobData();
				break;
				
			case 'kMailJobData':
				$this->data = new KalturaMailJobData();
				break;
				
			case 'kNotificationJobData':
				$this->data = new KalturaNotificationJobData();
				break;
				
			case 'kBulkDownloadJobData':
				$this->data = new KalturaBulkDownloadJobData();
				break;
				
			case 'kFlattenJobData':
				$this->data = new KalturaFlattenJobData();
				break;
				
			case 'kProvisionJobData':
				$this->data = new KalturaProvisionJobData();
				break;
				
			case 'kConvertCollectionJobData':
				$this->data = new KalturaConvertCollectionJobData();
				break;
				
			case 'kStorageExportJobData':
				$this->data = new KalturaStorageExportJobData();
				break;
				
			case 'kStorageDeleteJobData':
				$this->data = new KalturaStorageDeleteJobData();
				break;
				
			case 'kCaptureThumbJobData':
				$this->data = new KalturaCaptureThumbJobData();
				break;
				
			default:
				$this->data = KalturaPluginManager::loadObject('KalturaJobData', $this->jobType);
		}
			
		if($this->data)
			$this->data->fromObject($dbData);
	}
	    
	public function fromObject($dbBatchJob)
	{
		parent::fromObject( $dbBatchJob );
		$this->queueTime = $dbBatchJob->getQueueTime(null); // to return the timestamp and not string
		$this->finishTime = $dbBatchJob->getFinishTime(null); // to return the timestamp and not string
		
		if(!($dbBatchJob instanceof BatchJob))
			return $this;
			
		$dbData = $dbBatchJob->getData();
		$this->fromData($dbData);
		if($this->data)
			$this->jobSubType = $this->data->fromSubType($dbBatchJob->getJobSubType());
		
		return $this;
	}
	
	public function toData(BatchJob $dbBatchJob)
	{
		$dbData = null;
		
		if(is_null($this->jobType))
			$this->jobType = kPluginableEnumsManager::coreToApi('BatchJobType', $dbBatchJob->getJobType());
		
		switch($dbBatchJob->getJobType())
		{
			case KalturaBatchJobType::BULKUPLOAD:
				$dbData = new kBulkUploadJobData();
				if(is_null($this->data))
					$this->data = new KalturaBulkUploadJobData();
				break;
				
			case KalturaBatchJobType::CONVERT:
				$dbData = new kConvertJobData();
				if(is_null($this->data))
					$this->data = new KalturaConvertJobData();
				break;
				
			case KalturaBatchJobType::CONVERT_PROFILE:
				$dbData = new kConvertProfileJobData();
				if(is_null($this->data))
					$this->data = new KalturaConvertProfileJobData();
				break;
				
			case KalturaBatchJobType::EXTRACT_MEDIA:
				$dbData = new kExtractMediaJobData();
				if(is_null($this->data))
					$this->data = new KalturaExtractMediaJobData();
				break;
				
			case KalturaBatchJobType::IMPORT:
				$dbData = new kImportJobData();
				if(is_null($this->data))
					$this->data = new KalturaImportJobData();
				break;
				
			case KalturaBatchJobType::POSTCONVERT:
				$dbData = new kPostConvertJobData();
				if(is_null($this->data))
					$this->data = new KalturaPostConvertJobData();
				break;
				
			case KalturaBatchJobType::PULL:
				$dbData = new kPullJobData();
				if(is_null($this->data))
					$this->data = new KalturaPullJobData();
				break;
				
			case KalturaBatchJobType::REMOTE_CONVERT:
				$dbData = new kRemoteConvertJobData();
				if(is_null($this->data))
					$this->data = new KalturaRemoteConvertJobData();
				break;
				
			case KalturaBatchJobType::MAIL:
				$dbData = new kMailJobData();
				if(is_null($this->data))
					$this->data = new KalturaMailJobData();
				break;
				
			case KalturaBatchJobType::NOTIFICATION:
				$dbData = new kNotificationJobData();
				if(is_null($this->data))
					$this->data = new KalturaNotificationJobData();
				break;
				
			case KalturaBatchJobType::BULKDOWNLOAD:
				$dbData = new kBulkDownloadJobData();
				if(is_null($this->data))
					$this->data = new KalturaBulkDownloadJobData();
				break;
				
			case KalturaBatchJobType::FLATTEN:
				$dbData = new kFlattenJobData();
				if(is_null($this->data))
					$this->data = new KalturaFlattenJobData();
				break;
				
			case KalturaBatchJobType::PROVISION_PROVIDE:
			case KalturaBatchJobType::PROVISION_DELETE:
				$dbData = new kProvisionJobData();
				if(is_null($this->data))
					$this->data = new KalturaProvisionJobData();
				break;
				
			case KalturaBatchJobType::CONVERT_COLLECTION:
				$dbData = new kConvertCollectionJobData();
				if(is_null($this->data))
					$this->data = new KalturaConvertCollectionJobData();
				break;
				
			case KalturaBatchJobType::STORAGE_EXPORT:
				$dbData = new kStorageExportJobData();
				if(is_null($this->data))
					$this->data = new KalturaStorageExportJobData();
				break;
				
			case KalturaBatchJobType::STORAGE_DELETE:
				$dbData = new kStorageDeleteJobData();
				if(is_null($this->data))
					$this->data = new KalturaStorageDeleteJobData();
				break;
				
			case KalturaBatchJobType::CAPTURE_THUMB:
				$dbData = new kCaptureThumbJobData();
				if(is_null($this->data))
					$this->data = new KalturaCaptureThumbJobData();
				break;
				
			default:
				$dbData = KalturaPluginManager::loadObject('kJobData', $dbBatchJob->getJobType());
				if(is_null($this->data)) {
					$this->data = KalturaPluginManager::loadObject('KalturaJobData', $this->jobType);
				}
		}
		
		if(is_null($dbBatchJob->getData()))
			$dbBatchJob->setData($dbData);
	
		if($this->data instanceof KalturaJobData)
		{
			$dbData = $this->data->toObject($dbBatchJob->getData());
			$dbBatchJob->setData($dbData);
		}
		
		return $dbData;
	}
	
	public function toObject($dbBatchJob = null, $props_to_skip = array())
	{
		if(is_null($dbBatchJob))
			$dbBatchJob = new BatchJob();

		$dbBatchJob = parent::toObject($dbBatchJob);
		
		$this->toData($dbBatchJob);
		if(!is_null($this->jobSubType) && $this->data instanceof KalturaJobData)
			$dbBatchJob->setJobSubType($this->data->toSubType($this->jobSubType));
		
		return $dbBatchJob;
	}   
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	} 
}

?>
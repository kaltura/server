<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBatchJob extends KalturaObject implements IFilterable
{
	
	/**
	 * @var bigint
	 * @readonly
	 * @filter eq,gte
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,notin
	 */
	public $partnerId;
	
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * @var time
	 * @readonly
	 */
	public $deletedAt;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $lockExpiration;
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $executionAttempts;
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $lockVersion;
	
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
	 * @filter eq,in,notin
	 */
    public $jobSubType;
    
	/**
	 * @var KalturaJobData
	 */
    public $data;

    /**
	 * @var KalturaBatchJobStatus
	 * @filter eq,in,notin,order
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
	 * @var string
	 */
    public $message ;
    
    /**
	 * @var string
	 */
    public $description ;
    
    /**
	 * @var int
	 * @filter gte,lte,eq,in,notin,order
	 */
    public $priority ;
    
    /**
     * @var KalturaBatchHistoryDataArray
     */
    public $history ;
    
    /**
     * The id of the bulk upload job that initiated this job
	 * @var int
	 */    
    public $bulkJobId;
    
    /**
     * @var int
     * @filter gte,lte,eq
     */
    public $batchVersion;
    
    
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
	 * @filter eq,in,notin
	 */    
    public $errType;
    
    
    /**
	 * @var int
	 * @filter eq,in,notin
	 */    
    public $errNumber;
    
    
    /**
	 * @var int
	 * @filter lt,gt,order
	 */    
    public $estimatedEffort;
    
    /**
     * @var int
     * @filter lte,gte
     */
    public $urgency;
    
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
    
    /**
     * @var string
     */
    public $jobObjectId;

    /**
     * @var int
     */
	public $jobObjectType;
	
	private static $map_between_objects = array
	(
		"id" ,
		"partnerId" ,
		"createdAt" , "updatedAt" , 
		"entryId" ,
		"jobType" , 
	 	"status" ,  
		"message", "description" , "parentJobId" ,
		"rootJobId", "bulkJobId" , "priority" ,
		"queueTime" , "finishTime" ,  "errType", "errNumber", 
		"dc",
		"lastSchedulerId", "lastWorkerId" , 
		"history",
		"jobObjectId" => "objectId", "jobObjectType" => "objectType"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	    
	public function fromStatisticsObject($dbBatchJob, $dbLockObj = null)
	{
		$dbBatchJobLock = BatchJobLockPeer::retrieveByPK($dbBatchJob->getId());
		$this->fromBatchJob($dbBatchJob, $dbBatchJobLock);
		
		if(!($dbBatchJob instanceof BatchJob))
			return $this;
			
		$entry = $dbBatchJob->getEntry(true);
		if($entry)
			$this->entryName = $entry->getName();
		
		return $this;
	}
	    
	public function fromData(BatchJob $dbBatchJob, $dbData)
	{
		if(!$dbData)
			return;
				
		switch(get_class($dbData))
		{
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
				
			case 'kSshImportJobData':
				$this->data = new KalturaSshImportJobData();
				break;
				
			case 'kPostConvertJobData':
				$this->data = new KalturaPostConvertJobData();
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
				
			case 'kAkamaiProvisionJobData':
				$this->data = new KalturaAkamaiProvisionJobData();
				break;	

			case 'kAkamaiUniversalProvisionJobData':
				$this->data = new KalturaAkamaiUniversalProvisionJobData();
				break;
				
			case 'kConvertCollectionJobData':
				$this->data = new KalturaConvertCollectionJobData();
				break;
				
			case 'kStorageExportJobData':
				$this->data = new KalturaStorageExportJobData();
				break;
				
			case 'kAmazonS3StorageExportJobData':
				$this->data = new KalturaAmazonS3StorageExportJobData();
				break;
				
			case 'kMoveCategoryEntriesJobData':
				$this->data = new KalturaMoveCategoryEntriesJobData();
				break;
				
			case 'kStorageDeleteJobData':
				$this->data = new KalturaStorageDeleteJobData();
				break;
				
			case 'kCaptureThumbJobData':
				$this->data = new KalturaCaptureThumbJobData();
				break;
				
			case 'kMoveCategoryEntriesJobData':
			    $this->data = new KalturaMoveCategoryEntriesJobData();
			    break;

			case 'kIndexJobData':
				$this->data = new KalturaIndexJobData();
				break;
				
			case 'kCopyJobData':
				$this->data = new KalturaCopyJobData();
				break;
				
			case 'kDeleteJobData':
				$this->data = new KalturaDeleteJobData();
				break;

			case 'kDeleteFileJobData':
				$this->data = new KalturaDeleteFileJobData();
				break;
				
			case 'kConvertLiveSegmentJobData':
				$this->data = new KalturaConvertLiveSegmentJobData();
				break;
				
			case 'kConcatJobData':
				$this->data = new KalturaConcatJobData();
				break;
				
			case 'kCopyPartnerJobData':
				$this->data = new KalturaCopyPartnerJobData();
				break;
				
			case 'kSyncCategoryPrivacyContextJobData':
				$this->data = new KalturaSyncCategoryPrivacyContextJobData();
				break;
			
			case 'kLiveReportExportJobData':
				$this->data = new KalturaLiveReportExportJobData();
				break;
			
			case 'kRecalculateResponseProfileCacheJobData':
				$this->data = new KalturaRecalculateResponseProfileCacheJobData();
				break;

			case 'kLiveToVodJobData':
				$this->data = new KalturaLiveToVodJobData();
				break;

			case 'kCopyCaptionsJobData':
				$this->data = new KalturaCopyCaptionsJobData();
				break;

			case 'kExtractDataJobData':
				$this->data = new KalturaExtractDataJobData();
				break;

			default:
				if($dbData instanceof kBulkUploadJobData)
				{
					$this->data = KalturaPluginManager::loadObject('KalturaBulkUploadJobData', $dbBatchJob->getJobSubType());
					if(is_null($this->data))
						KalturaLog::err("Unable to init KalturaBulkUploadJobData for sub-type [" . $dbBatchJob->getJobSubType() . "]");
				}
				else if($dbData instanceof kImportJobData)
				{
					$this->data = KalturaPluginManager::loadObject('KalturaImportJobData', get_class($dbData));
					if(is_null($this->data))
						KalturaLog::err("Unable to init KalturaImportJobData for class [" . get_class($dbData) . "]");
				}
				else
				{
					$this->data = KalturaPluginManager::loadObject('KalturaJobData', $this->jobType, array('coreJobSubType' => $dbBatchJob->getJobSubType()));
				}
		}
		
		if(is_null($this->data))
			KalturaLog::err("Unable to init KalturaJobData for job type [{$this->jobType}] sub-type [" . $dbBatchJob->getJobSubType() . "]");
			
		if($this->data)
			$this->data->fromObject($dbData);
	}
	
	public function fromLockObject(BatchJob $dbBatchJob, BatchJobLock $dbBatchJobLock) 
	{
		$this->lockExpiration = $dbBatchJobLock->getExpiration();
		$this->executionAttempts = $dbBatchJobLock->getExecutionAttempts();
		$this->lockVersion = $dbBatchJobLock->getVersion();
		$this->checkAgainTimeout = $dbBatchJobLock->getStartAt(null);
		$this->estimatedEffort = $dbBatchJobLock->getEstimatedEffort();
		
		$this->schedulerId = $dbBatchJobLock->getSchedulerId();
		$this->workerId = $dbBatchJobLock->getWorkerId();
	}
	
	public function fromBatchJob($dbBatchJob, BatchJobLock $dbBatchJobLock = null) 
	{
		parent::fromObject($dbBatchJob);
		
		$this->queueTime = $dbBatchJob->getQueueTime(null); // to return the timestamp and not string
		$this->finishTime = $dbBatchJob->getFinishTime(null); // to return the timestamp and not string
		
		if(!($dbBatchJob instanceof BatchJob))
			return $this;
			
		$dbData = $dbBatchJob->getData();
		$this->fromData($dbBatchJob, $dbData);
		if($this->data)
			$this->jobSubType = $this->data->fromSubType($dbBatchJob->getJobSubType());
		
		if($dbBatchJobLock) {
			$this->fromLockObject($dbBatchJob, $dbBatchJobLock);
		} else {
			$this->lockVersion = $dbBatchJob->getLockInfo()->getLockVersion();
			$this->estimatedEffort = $dbBatchJob->getLockInfo()->getEstimatedEffort();
		}
		
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
				$jobSubType = $dbBatchJob->getJobSubType();
				$dbData = kAkamaiProvisionJobData::getInstance($jobSubType);
				if(is_null($this->data))
					$this->data = KalturaProvisionJobData::getJobDataInstance($jobSubType);

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
				
			case KalturaBatchJobType::MOVE_CATEGORY_ENTRIES:
				$dbData = new kMoveCategoryEntriesJobData();
				if(is_null($this->data))
					$this->data = new KalturaMoveCategoryEntriesJobData();
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
				
			case KalturaBatchJobType::INDEX:
				$dbData = new kIndexJobData();
				if(is_null($this->data))
					$this->data = new KalturaIndexJobData();
				break;
				
			case KalturaBatchJobType::COPY:
				$dbData = new kCopyJobData();
				if(is_null($this->data))
					$this->data = new KalturaCopyJobData();
				break;
				
			case KalturaBatchJobType::DELETE:
				$dbData = new kDeleteJobData();
				if(is_null($this->data))
					$this->data = new KalturaDeleteJobData();
				break;

			case KalturaBatchJobType::DELETE_FILE:
				$dbData = new kDeleteFileJobData();
				if(is_null($this->data))
					$this->data = new KalturaDeleteFileJobData();
				break;
				
			case KalturaBatchJobType::CONVERT_LIVE_SEGMENT:
				$dbData = new kConvertLiveSegmentJobData();
				if(is_null($this->data))
					$this->data = new KalturaConvertLiveSegmentJobData();
				break;
				
			case KalturaBatchJobType::CONCAT:
				$dbData = new kConcatJobData();
				if(is_null($this->data))
					$this->data = new KalturaConcatJobData();
				break;
					
			case KalturaBatchJobType::COPY_PARTNER:
				$dbData = new kCopyPartnerJobData();
				if(is_null($this->data))
					$this->data = new KalturaCopyPartnerJobData();
				break;
					
			case KalturaBatchJobType::RECALCULATE_CACHE:
				switch($dbBatchJob->getJobSubType())
				{
					case RecalculateCacheType::RESPONSE_PROFILE:
						$dbData = new kRecalculateResponseProfileCacheJobData();
						if(is_null($this->data))
							$this->data = new KalturaRecalculateResponseProfileCacheJobData();
						break;
				}
				break;
			
			case KalturaBatchJobType::LIVE_TO_VOD:
				$dbData = new kLiveToVodJobData();
				if(is_null($this->data))
					$this->data = new KalturaLiveToVodJobData();
 				break;

			case KalturaBatchJobType::COPY_CAPTIONS:
				$dbData = new kCopyCaptionsJobData();
				if(is_null($this->data))
					$this->data = new KalturaCopyCaptionsJobData();
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
		if($this->abort)
			$dbBatchJob->setExecutionStatus(BatchJobExecutionStatus::ABORTED);
		
		if (!is_null($this->data))
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

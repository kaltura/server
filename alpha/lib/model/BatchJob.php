<?php
/**
 * Subclass for representing a row from the 'batch_job' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class BatchJob extends BaseBatchJob implements ISyncableFile
{
	const BATCHJOB_SUB_TYPE_YOUTUBE = 0;
	const BATCHJOB_SUB_TYPE_MYSPACE = 1;
	const BATCHJOB_SUB_TYPE_PHOTOBUCKET = 2;
	const BATCHJOB_SUB_TYPE_JAMENDO = 3;
	const BATCHJOB_SUB_TYPE_CCMIXTER = 4;
	
	const POSTCONVERT_ASSET_TYPE_FLAVOR = 0;
	const POSTCONVERT_ASSET_TYPE_SOURCE = 1;
	const POSTCONVERT_ASSET_TYPE_BYPASS = 2;
	
	const BATCHJOB_STATUS_PENDING = 0;
	const BATCHJOB_STATUS_QUEUED = 1;
	const BATCHJOB_STATUS_PROCESSING = 2;
	const BATCHJOB_STATUS_PROCESSED = 3;
	const BATCHJOB_STATUS_MOVEFILE = 4;
	const BATCHJOB_STATUS_FINISHED = 5;
	const BATCHJOB_STATUS_FAILED = 6;
	const BATCHJOB_STATUS_ABORTED = 7;
	const BATCHJOB_STATUS_ALMOST_DONE = 8;
	const BATCHJOB_STATUS_RETRY = 9;
	const BATCHJOB_STATUS_FATAL = 10;
	const BATCHJOB_STATUS_DONT_PROCESS = 11;
	const BATCHJOB_STATUS_FINISHED_PARTIALLY = 12;
	const BATCHJOB_STATUS_SUSPEND = 13;
	const BATCHJOB_STATUS_SUSPEND_ALMOST_DONE = 14;
	
	const FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD = 1;
	const FILE_SYNC_BATCHJOB_SUB_TYPE_CONFIG = 3;
	
	const HISTORY_LENGTH = 20;
	
	private static $indicator = null;//= new myFileIndicator( "gogobatchjob" );
	
	private $aEntry = null;
	private $aPartner = null;
	private $aParentJob = null;
	private $aRootJob = null;
	
	/*
	 * @var boolean
	 */
	protected $useNewRoot = false;
	
	private static $BATCHJOB_TYPE_NAMES = array(
		BatchJobType::CONVERT => 'Convert',
		BatchJobType::IMPORT => 'Import',
		BatchJobType::DELETE => 'Delete',
		BatchJobType::FLATTEN => 'Flatten',
		BatchJobType::BULKUPLOAD => 'Bulk Upload',
		BatchJobType::DVDCREATOR => 'DVD Creator',
		BatchJobType::DOWNLOAD => 'Download',
		BatchJobType::OOCONVERT => 'OO Convert',
		BatchJobType::CONVERT_PROFILE => 'Convert Profile',
		BatchJobType::POSTCONVERT => 'Post Convert',
		BatchJobType::EXTRACT_MEDIA => 'Extract Media',
		BatchJobType::MAIL => 'Mail',
		BatchJobType::NOTIFICATION => 'Notification',
		BatchJobType::CLEANUP => 'Cleanup',
		BatchJobType::SCHEDULER_HELPER => 'Schedule Helper',
		BatchJobType::BULKDOWNLOAD => 'Bulk Download',
		BatchJobType::DB_CLEANUP => 'DB Cleanup',
		
		BatchJobType::PROVISION_PROVIDE => 'Provision Provide',
		BatchJobType::CONVERT_COLLECTION => 'Convert Collection',
		BatchJobType::STORAGE_EXPORT => 'Storage Export',
		BatchJobType::PROVISION_DELETE => 'Provision Delete',
		BatchJobType::STORAGE_DELETE => 'Storage Delete',
		BatchJobType::EMAIL_INGESTION => 'Email Ingestion',
		
		BatchJobType::METADATA_IMPORT => 'Metadata Import',
		BatchJobType::METADATA_TRANSFORM => 'Metadata Transform',
		
		BatchJobType::FILESYNC_IMPORT => 'File Sync Import',
		BatchJobType::CAPTURE_THUMB => 'Capture Thumbnail',
		
		BatchJobType::INDEX => 'Index',
		BatchJobType::COPY => 'Copy',
		BatchJobType::MOVE_CATEGORY_ENTRIES => 'Move Category Entries',
		BatchJobType::LIVE_TO_VOD => "Live To Vod",
	);
	
	private static $BATCHJOB_STATUS_NAMES = array(
		self::BATCHJOB_STATUS_PENDING => 'Pending',
		self::BATCHJOB_STATUS_QUEUED => 'Queued',
		self::BATCHJOB_STATUS_PROCESSING => 'Processing',
		self::BATCHJOB_STATUS_PROCESSED => 'Processed',
		self::BATCHJOB_STATUS_MOVEFILE => 'Move File',
		self::BATCHJOB_STATUS_FINISHED => 'Finished',
		self::BATCHJOB_STATUS_FAILED => 'Failed',
		self::BATCHJOB_STATUS_ABORTED => 'Aborted',
		self::BATCHJOB_STATUS_ALMOST_DONE => 'Almost Done',
		self::BATCHJOB_STATUS_RETRY => 'Retry',
		self::BATCHJOB_STATUS_FATAL => 'Fatal',
		self::BATCHJOB_STATUS_DONT_PROCESS => 'Dont Process',
		self::BATCHJOB_STATUS_SUSPEND => 'Suspended',
		self::BATCHJOB_STATUS_SUSPEND_ALMOST_DONE => 'Suspended',
	);
	
	
	public static function getStatusName($status)
	{
		$status = (int) $status;
		if(!isset(self::$BATCHJOB_STATUS_NAMES[$status]))
			return "Extended ($status)";
			
		return self::$BATCHJOB_STATUS_NAMES[$status];
	}
	
	public static function getTypeName($type)
	{
		if(!isset(self::$BATCHJOB_TYPE_NAMES[$type]))
			return ucwords(str_replace('.', ' ', $type));
			
		return self::$BATCHJOB_TYPE_NAMES[$type];
	}
	
	public function preInsert(PropelPDO $con = null)
	{
	
		// set the dc ONLY if it wasnt initialized
		// this is required in the special case of file_sync import jobs which are created on one dc but run from the other
		// all other jobs run from the same datacenter they were created on.
		// setting the dc later results in a race condition were the job is picked up by the current datacenter before the dc value is changed
		if(is_null($this->dc) || !$this->isColumnModified(BatchJobPeer::DC))
		{
			// by default set the dc to the current data center. However whenever a batch job is operating on an entry, we rather run it
			// in the DC where the file sync of the entry exists. Since the batch job doesnt refer to a flavor (we only have an entry id member)
			// we check the file sync of the source flavor asset (if one exists)
	
			$dc = kDataCenterMgr::getCurrentDcId();
			$this->setDc ( $dc );
		}
			
		// if the status not set upon creation
		if(is_null($this->status) || !$this->isColumnModified(BatchJobPeer::STATUS))
		{
			//echo "sets the status to " . self::BATCHJOB_STATUS_PENDING . "\n";
			$this->setStatus(self::BATCHJOB_STATUS_PENDING);
		} 
		
		return parent::preInsert($con);
	}
	
	public function preUpdate(PropelPDO $con = null) {
		
		if(!$this->alreadyInSave)
			BatchJobPeer::preBatchJobUpdate($this);
		
		$this->updateDerivedFields();
		
		if(BatchJobLockPeer::shouldUpdateLockObject($this, $con)) {
			BatchJobLockPeer::updateLockObject($this, $con);
		}
		
		if(BatchJobLockPeer::shouldDeleteLockObject($this, $con)) {
			$batchJobLock = $this->getBatchJobLock($con);
			$batchJobLock->delete($con);
			$this->setBatchJobLock(null);
		}
		
		return parent::preUpdate($con);
	}
	
	private function updateDerivedFields() {
		if(is_null($this->getQueueTime()) && $this->getStatus() != BatchJob::BATCHJOB_STATUS_PENDING && $this->getStatus() != BatchJob::BATCHJOB_STATUS_RETRY) {
			$this->setQueueTime(time());
		}
	
		if($this->getStatus() == BatchJob::BATCHJOB_STATUS_RETRY) {
			$this->setQueueTime(null);
		}
	
		if(in_array($this->getStatus(), array(BatchJob::BATCHJOB_STATUS_FAILED, BatchJob::BATCHJOB_STATUS_FATAL, BatchJob::BATCHJOB_STATUS_FINISHED)))
			$this->setFinishTime(time());
	}
	
	public function postInsert(PropelPDO $con = null) {
	
		if((!$this->root_job_id && $this->id) || ($this->useNewRoot))
		{
			// set the root to point to itself
			$this->setRootJobId($this->id);
			$res = parent::save($con);
		}
	
		if(BatchJobLockPeer::shouldCreateLockObject($this,true, $con)) 
			BatchJobLockPeer::createLockObject($this);
	
		return parent::postInsert($con);
	}
	
	public function postUpdate(PropelPDO $con = null) {
		if(!$this->alreadyInSave && BatchJobLockPeer::shouldCreateLockObject($this,false, $con))
			BatchJobLockPeer::createLockObject($this);
		
		return parent::postUpdate($con);
	}
	
	/**
	 * @return Partner
	 */
	public function getPartner()
	{
		if ( $this->aPartner == null && !is_null($this->getPartnerId()) )
		{
			$this->aPartner = PartnerPeer::retrieveByPK( $this->getPartnerId()  );
		}
		return $this->aPartner;
	}
	
	
	/**
	 * @return BatchJob
	 */
	public function getParentJob()
	{
		if ( $this->aParentJob == null && $this->getParentJobId() )
		{
			$this->aParentJob = BatchJobPeer::retrieveByPK( $this->getParentJobId()  );
		}
		return $this->aParentJob;
	}
	
	/**
	 * 
	 * @param $getDeleted
	 * @param $enableCache
	 * 
	 * @return entry
	 */
	public function getEntry($getDeleted = false, $enableCache = true)
	{
		if(!$enableCache)
		{
			$this->aEntry = null;
			entryPeer::clearInstancePool();
		}
		
		if ( $this->aEntry == null && $this->getEntryId() )
		{
			if($getDeleted)
				$this->aEntry = entryPeer::retrieveByPKNoFilter( $this->getEntryId()  );
			else			
				$this->aEntry = entryPeer::retrieveByPK( $this->getEntryId()  );
		}
		return $this->aEntry;
	}
	
	/**
	 * @return BatchJob
	 */
	public function getRootJob()
	{
		if($this->aRootJob == null && $this->getRootJobId())
		{
			$this->aRootJob = BatchJobPeer::retrieveByPK($this->getRootJobId());
		}
		return $this->aRootJob;
	}
	
	/**
	 * @return BaseObject
	 */
	public function getObject()
	{
		switch ($this->getObjectType())
		{
			case BatchJobObjectType::ENTRY:
				entryPeer::setUseCriteriaFilter(false);
				$object = entryPeer::retrieveByPK($this->getObjectId());
				entryPeer::setUseCriteriaFilter(true);
				return $object;
				
			case BatchJobObjectType::ASSET:
				assetPeer::setUseCriteriaFilter(false);
				$object = assetPeer::retrieveById($this->getObjectId());
				assetPeer::setUseCriteriaFilter(true);
				return $object;
				
			case BatchJobObjectType::CATEGORY:
				categoryPeer::setUseCriteriaFilter(false);
				$object = categoryPeer::retrieveByPK($this->getObjectId());
				categoryPeer::setUseCriteriaFilter(true);
				return $object;
				
			case BatchJobObjectType::FILE_SYNC:
				FileSyncPeer::setUseCriteriaFilter(false);
				$object = FileSyncPeer::retrieveByPK($this->getObjectId());
				FileSyncPeer::setUseCriteriaFilter(true);
				return $object;
		
			default:
				// TODO implement IBatchable in relevant plugins
				return KalturaPluginManager::loadObject('IBatchable', $this->getObjectId());
		}
		
		return $this->aRootJob;
	}
	
	
	public function getFormattedCreatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getCreatedAt' , $format );
	}

	public function getFormattedUpdatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getUpdatedAt' , $format );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getSyncKey()
	 */
	public function getSyncKey ( $sub_type , $version = null )
	{
		self::validateFileSyncSubType ( $sub_type );
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::BATCHJOB;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		$key->version = 0;
		
		return $key;
	}

	
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFileName()
	 */
	public function generateFileName( $sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
	
		switch($sub_type)
		{
			case self::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD:
				$ext = 'csv';
				$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUpload');
				foreach($pluginInstances as $pluginInstance)
				{
					$pluginExt = $pluginInstance->getFileExtension($this->getJobSubType());
					if($pluginExt)
					{
						$ext = $pluginExt;
						break;
					}
				}
				
				return 'bulk_' . $this->getId() . '.' . $ext;
				
			case self::FILE_SYNC_BATCHJOB_SUB_TYPE_CONFIG:
				return 'config_' . $this->getId() . '.xml';
		}
		
		return null;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFilePathArr()
	 */
	public function generateFilePathArr( $sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		
		$path = '/content/batchfiles/' . $this->getPartnerId() . '/' . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}
	
	/**
	 * @var FileSync
	 */
	private $m_file_sync;
	
	/**
	 * @return FileSync
	 */
	public function getFileSync ( )
	{
		return $this->m_file_sync; 
	}
	
	public function setFileSync ( FileSync $file_sync )
	{
		 $this->m_file_sync = $file_sync;
	}
	
	private static function validateFileSyncSubType ( $sub_type )
	{
		$valid_sub_types = array(
			self::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOAD, 
			self::FILE_SYNC_BATCHJOB_SUB_TYPE_CONFIG
		);
		
		if (!in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSyncObjectType::BATCHJOB, $sub_type, $valid_sub_types);		
	}
	
	public function getChildJobs(Criteria $c = null)
	{
		if($c) {
			$c = clone $c;
		} else {
			$c = new Criteria();
		}
		
		BatchJobPeer::setUseCriteriaFilter(false);
		// Get by root
		$c1 = clone $c;
		$c1->addAnd($c1->getNewCriterion(BatchJobPeer::ROOT_JOB_ID, $this->id));
		$c1->addAnd($c1->getNewCriterion(BatchJobPeer::PARENT_JOB_ID, $this->id, Criteria::NOT_EQUAL));
		$result1 = BatchJobPeer::doSelect($c1, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
		
		// Get by parent
		$c->addAnd($c->getNewCriterion(BatchJobPeer::PARENT_JOB_ID, $this->id));
		$result2 = BatchJobPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
		
		// Unite
		BatchJobPeer::setUseCriteriaFilter(true);
		return array_merge($result1, $result2);
	}

	public function getOpenStatusChildJobs(Criteria $c = null)
	{
		if($c)
			$c = clone $c;
		else
			$c = new Criteria();

		$c->addAnd($c->getNewCriterion(BatchJobPeer::STATUS, BatchJobPeer::getClosedStatusList(), Criteria::NOT_IN));
		return $this->getChildJobs($c);
	}

	public function getDirectChildJobs()
	{
		$c = new Criteria();
		$c->add(BatchJobPeer::PARENT_JOB_ID, $this->id);
		return BatchJobPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
	}
	
	
	/**
	 * The type and the sub-type are constant in the batch job, therefore they must be proveided
	 * when a new batch job is generated.
	 * @return BatchJob
	 */
	public function createChild( $type, $subType = null, $same_root = true, $dc = null)
	{
		$child = new BatchJob();
		
		$child->setJobType($type);
		if($subType !== null)
			$child->setJobSubType($subType);
		
		$child->setParentJobId($this->id);
		$child->setPartnerId($this->partner_id);
		$child->setEntryId($this->entry_id);
		$child->setBulkJobId($this->bulk_job_id);
		
		// the condition is required in the special case of file_sync import jobs which are created on one dc but run from the other
		$child->setDc($dc === null ? $this->dc : $dc);
		
		if($same_root && $this->root_job_id)
		{
			$child->setRootJobId($this->root_job_id);
		}
		else
		{
			$child->setRootJobId($this->id);
		}
		
		return $child;
	}

	/**
	 * @param boolean  $bypassSerialization enables PS2 support
	 * @return kJobData
	 */
	public function getData($bypassSerialization = false)
	{
		if($bypassSerialization)
			return parent::getData();
		$data = parent::getData();
		if(!is_null($data))
			return unserialize ( $data );
		
		return null;
	}
	
	/**
	 * @param boolean  $bypassSerialization enables PS2 support
	 */
	public function setData($v, $bypassSerialization = false) {
		if ($bypassSerialization)
			return parent::setData ( $v );
		if (! is_null ( $v )) {
			$sereializedValue = serialize ( $v );
			parent::setData ( $sereializedValue );	
		} else
			parent::setData ( null );
	} 
	
	/**
	 * @return kLockInfoData
	 */
	public function getLockInfo()
	{
		$data = parent::getLockInfo();
		if(!is_null($data))
			return unserialize ( $data );
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @param kLockInfoData
	 * @see BaseBatchJob::setLockInfo()
	 */
	public function setLockInfo($v) {
		if (! is_null ( $v )) 
			parent::setLockInfo ( serialize ( $v ) );
		else
			parent::setLockInfo ( null );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BaseBatchJob::getHistory()
	 * @return KalturaBatchHistoryDataArray
	 */
	public function getHistory()
	{
		$data = parent::getHistory();
		if(!is_null($data))
			return unserialize ( $data );
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @param KalturaBatchHistoryDataArray
	 * @see BaseBatchJob::setHistory()
	 */
	public function setHistory($v) {
		if (! is_null ( $v ))
			parent::setHistory ( serialize ( $v ) );
		else
			parent::setHistory ( null );
	}
	
	public function addHistoryRecord(kBatchHistoryData $v) {
		$historyArr = $this->getHistory();
		if($historyArr === null)
			$historyArr = array();
		$historyArr[] = $v;
		$this->setHistory(array_splice($historyArr, -1 * self::HISTORY_LENGTH));
	}
	
	/*
	 * @param boolean $useNewRoot
	*/
	public function setUseNewRoot($useNewRoot)
	{
		$this->useNewRoot = $useNewRoot;
	}
	
	/**
	 * The function fills the batch job log to contain all the information the
	 * current batch job contains in it.
	 */
	public function copyIntoBatchLog(BatchJobLog $copyObj)
	{
		// Batch Job Fields
		$copyObj->setJobType($this->job_type);
		$copyObj->setJobSubType($this->job_sub_type);
		$copyObj->setData($this->data);
		$copyObj->setStatus($this->status);
		$copyObj->setAbort($this->execution_status == BatchJobExecutionStatus::ABORTED);
		$copyObj->setMessage($this->message);
		$copyObj->setDescription($this->description);
		$copyObj->setCreatedAt($this->created_at);
		$copyObj->setUpdatedAt($this->updated_at);
		$copyObj->setPriority($this->priority);
		$copyObj->setQueueTime($this->queue_time);
		$copyObj->setFinishTime($this->finish_time);
		$copyObj->setEntryId($this->entry_id);
		$copyObj->setPartnerId($this->partner_id);
		$copyObj->setLastSchedulerId($this->last_scheduler_id);
		$copyObj->setLastWorkerId($this->last_worker_id);
		$copyObj->setBulkJobId($this->bulk_job_id);
		$copyObj->setRootJobId($this->root_job_id);
		$copyObj->setParentJobId($this->parent_job_id);
		$copyObj->setDc($this->dc);
		$copyObj->setErrType($this->err_type);
		$copyObj->setErrNumber($this->err_number);
		
		// Batch job lock info		
		if($this->getLockInfo() != null) {
			$copyObj->setFileSize($this->getLockInfo()->getEstimatedEffort());
			$copyObj->setLockVersion($this->getLockInfo()->getLockVersion());
		}
		
		// Batch job lock fields
		$dbBatchJobLock = $this->getBatchJobLock();
		if($dbBatchJobLock !== null)
		{
			$copyObj->setProcessorExpiration($dbBatchJobLock->getExpiration());
			$copyObj->setExecutionAttempts($dbBatchJobLock->getExecutionAttempts());
			$copyObj->setCheckAgainTimeout($dbBatchJobLock->getStartAt());
		
			$copyObj->setSchedulerId( $dbBatchJobLock->getSchedulerId());
			$copyObj->setWorkerId($dbBatchJobLock->getWorkerId());
			$copyObj->setBatchIndex($dbBatchJobLock->getBatchIndex());
		}
		
		$copyObj->setNew(true);
		$copyObj->setId(NULL); // this is a auto-increment column, so set to default value
	
	}
}

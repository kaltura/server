<?php
require_once( 'dateUtils.class.php');
require_once( 'myFileIndicator.class.php');
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
	
	const BATCHJOB_SUB_TYPE_POSTCONVERT_FLAVOR = 0;
	const BATCHJOB_SUB_TYPE_POSTCONVERT_SOURCE = 1;
	const BATCHJOB_SUB_TYPE_POSTCONVERT_BYPASS = 2;
	
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
	
	const FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV = 1;
	const FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG = 2;
	const FILE_SYNC_BATCHJOB_SUB_TYPE_CONFIG = 3;

	private static $indicator = null;//= new myFileIndicator( "gogobatchjob" );
	
	private $aEntry = null;
	private $aPartner = null;
	private $aParentJob = null;
	private $aRootJob = null;
	
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
		BatchJobType::PULL => 'Pull',
		BatchJobType::REMOTE_CONVERT => 'Remote Convert',
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
	
	public static function createDeleteEntryJob ( entry $entry )
	{
//		if ( $entry == null ) return;
//		
//		KalturaLog::log("BatchJob::create Delete Entry Job Entry [" . $entry->getId() . "] Partner [" . $entry->getPartnerId() . "]");
//		
////		if ( ! myPartnerUtils::shouldModerate( $entry->getPartnerId() ) ) return;
//		$batch_job = new BatchJob();
//		$currentDc = kDataCenterMgr::getCurrentDc();
//		$batch_job->setDc($currentDc["name"]);
//		$batch_job->setPartnerId($entry->getPartnerId());
//		$batch_job->setJobType( BatchJobType::DELETE );
//		$batch_job->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
//		$batch_job->setEntryId( $entry->getId() );
//		$batch_job->save();
	}
	
	public function save(PropelPDO $con = null)
	{
		KalturaLog::log( "BatchJob [{$this->getJobType()}][{$this->getJobSubType()}]: save()" );
		$is_new = $this->isNew() ;
		
		if ( $this->isNew() )
		{
			$this->setDc ( kDataCenterMgr::getCurrentDcId());
		
			// if the status not set upon creation
			if(is_null($this->status) || !$this->isColumnModified(BatchJobPeer::STATUS))
			{
				//echo "sets the status to " . self::BATCHJOB_STATUS_PENDING . "\n";
				$this->setStatus(self::BATCHJOB_STATUS_PENDING);
			}
		}
			
		$res = parent::save( $con );
		
		if($is_new && !$this->root_job_id && $this->id)
		{
			// set the root to point to itself
			$this->setRootJobId($this->id);
			$res = parent::save($con);
		}
/*		
 * 	remove - no need to use file indicators any more
		// when new object or status is pending - add the indicator for the batch job to start running
		if ( $is_new || ( $this->getStatus() == self::BATCHJOB_STATUS_PENDING ) )
		{
			self::addIndicator( $this->getId() , $this->getJobType() );
			KalturaLog::log ( "BatchJob: Added indicator for BatchJob [" . $this->getId() . "] of type [{$this->getJobType() }]" );
			//debugUtils::st();			
		}
		else
		{
			KalturaLog::log ( "BatchJob: Didn't add an indicator for BatchJob [" . $this->getId() . "]" );
		}
*/		
		return $res;
		
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
	
	
	public function getFormattedCreatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getCreatedAt' , $format );
	}

	public function getFormattedUpdatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getUpdatedAt' , $format );
	}
	
	public static function isIndicatorSet ( $type = BatchJobType::IMPORT )
	{
		return self::getIndicator( $type )->isIndicatorSet();
	}
	
	public static function addIndicator ( $id , $type = BatchJobType::IMPORT)
	{
		// TODO - remove the double indicator !
		self::getIndicator( $type )->addIndicator( $id );
		self::getIndicator( $type )->addIndicator( $id . "_"); // for now add an extra indicator 
	}
	
	
	public static function removeIndicator ( $type = BatchJobType::IMPORT )
	{
		self::getIndicator( $type )->removeIndicator();
	}
	
	private static function getIndicator( $type = BatchJobType::IMPORT )
	{
		if ( ! self::$indicator ) self::$indicator = array();
		
		if ( ! isset ( self::$indicator[$type] ) )
		{
			self::$indicator[$type] = new myFileIndicator( "gogobatchjob_{$type}" ); 
		}
		
		return self::$indicator[$type];
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
			case self::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV:
				return "csv_".$this->getId().".csv";
				
			case self::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG:
				return "log_".$this->getId().".csv";
				
			case self::FILE_SYNC_BATCHJOB_SUB_TYPE_CONFIG:
				return "config_".$this->getId().".xml";
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
			self::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV, 
			self::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG, 
			self::FILE_SYNC_BATCHJOB_SUB_TYPE_CONFIG
		);
		
		if (!in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSyncObjectType::BATCHJOB, $sub_type, $valid_sub_types);		
	}
	
	public function isRetriesExceeded()
	{
		return ($this->execution_attempts >= BatchJobPeer::getMaxExecutionAttempts($this->job_type));
	}
	
	public function getTwinJobs()
	{
		$c = new Criteria();
		$c->add(BatchJobPeer::TWIN_JOB_ID, $this->id);
		return BatchJobPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
	}
	
	public function getChildJobs()
	{
		$c = new Criteria();
		$crit = $c->getNewCriterion(BatchJobPeer::ROOT_JOB_ID, $this->id);
		$crit->addOr($c->getNewCriterion(BatchJobPeer::PARENT_JOB_ID, $this->id));
		$c->addAnd($crit);
		return BatchJobPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
	}
	
	public function getDirectChildJobs()
	{
		$c = new Criteria();
		$c->add(BatchJobPeer::PARENT_JOB_ID, $this->id);
		return BatchJobPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
	}
	
	
	/**
	 * @return BatchJob
	 */
	public function createChild($same_root = true)
	{
		$child = new BatchJob();
		
		$child->setStatus(self::BATCHJOB_STATUS_PENDING);
		$child->setParentJobId($this->id);
		$child->setPartnerId($this->partner_id);
		$child->setEntryId($this->entry_id);
		$child->setPriority($this->priority);
		$child->setSubpId($this->subp_id);
		$child->setBulkJobId($this->bulk_job_id);
		$child->setDc($this->dc);
		
		if($same_root && $this->root_job_id)
		{
			$child->setRootJobId($this->root_job_id);
		}
		else
		{
			$child->setRootJobId($this->id);
		}
		
		$child->save();
		
		return $child;
	}

	/**
	 * @param boolean  $bypassSerialization enables PS2 support
	 */
	public function getData($bypassSerialization = false)
	{
		if($bypassSerialization)
			return parent::getData();
			
		$data = parent::getData();
		if(!is_null($data))
		{
			try{
				return unserialize($data);
			}
			catch(Exception $e){
				return null;
			}
		}
			
		return null;
	} 
	
	/**
	 * @param boolean  $bypassSerialization enables PS2 support
	 */
	public function setData($v, $bypassSerialization = false)
	{
		if($bypassSerialization)
			return parent::setData($v);
			
		$this->setDuplicationKey(BatchJobPeer::createDuplicationKey($this->getJobType(), $v));
		
		if(!is_null($v))
			parent::setData(serialize($v));
		else	
			parent::setData(null);
	} 
	
	
	// make this attribute readonly
	public function setProcessorExpiration($v)
	{
		if(is_null($v))
			parent::setProcessorExpiration(null);
	}
}

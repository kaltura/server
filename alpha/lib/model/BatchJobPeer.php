<?php

/**
 * Subclass for performing query and update operations on the 'batch_job' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class BatchJobPeer extends BaseBatchJobPeer
{
	public static function getInProcStatus()
	{
		return BatchJob::BATCHJOB_STATUS_QUEUED;
	}
	
	public static function getInProcStatusList()
	{
		return BatchJob::BATCHJOB_STATUS_QUEUED . "," . 
			BatchJob::BATCHJOB_STATUS_PROCESSING. "," .
			BatchJob::BATCHJOB_STATUS_PROCESSED. "," .
			BatchJob::BATCHJOB_STATUS_MOVEFILE;
	}
	
	public static function getUnClosedStatusList()
	{
		return array(
			BatchJob::BATCHJOB_STATUS_PENDING,
			BatchJob::BATCHJOB_STATUS_QUEUED,
			BatchJob::BATCHJOB_STATUS_PROCESSING,
			BatchJob::BATCHJOB_STATUS_PROCESSED,
			BatchJob::BATCHJOB_STATUS_MOVEFILE,
			BatchJob::BATCHJOB_STATUS_RETRY,
		);
	}
	
	public static function getClosedStatusList()
	{
		return array(
			BatchJob::BATCHJOB_STATUS_FINISHED,
			BatchJob::BATCHJOB_STATUS_FAILED,
			BatchJob::BATCHJOB_STATUS_ABORTED,
			BatchJob::BATCHJOB_STATUS_FATAL,
			BatchJob::BATCHJOB_STATUS_DONT_PROCESS,
			BatchJob::BATCHJOB_STATUS_FINISHED_PARTIALLY
		);
	}
	
	public static function retrieveByEntryIdAndType($entryId, $jobType, $jobSubType = null)
	{
		$c = new Criteria();
		$c->add ( self::ENTRY_ID , $entryId );
		$c->add ( self::JOB_TYPE , $jobType );
		
		if(!is_null($jobSubType))
			$c->add ( self::JOB_SUB_TYPE , $jobSubType );
			
		return self::doSelect( $c );
	}
	
	public static function retrieveByJobTypeAndObject($objectId, $objectType, $jobType, $jobSubType = null) 
	{
		$c = new Criteria();
		$c->add ( self::OBJECT_ID , $objectId );
		$c->add ( self::OBJECT_TYPE , $objectType );
		$c->add ( self::JOB_TYPE , $jobType );
		
		if(!is_null($jobSubType))
			$c->add ( self::JOB_SUB_TYPE , $jobSubType );
		
		$c->addAscendingOrderByColumn(self::CREATED_AT);
		return self::doSelect( $c );
	}
	
	public static function retrieveByEntryId($obj_id)
	{
		$c = new Criteria();
		$c->add ( self::ENTRY_ID , $obj_id );
		return self::doSelect( $c );
	}
	
	public static function doAvgTimeDiff($jobType, $t1, $t2, PDO $con = null)
	{
		if(is_null($con))
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME);
		
		$stmt = null;
		try {
			$sql = "SELECT	AVG(TIMEDIFF($t2, $t1)) "; 
			$sql .= "FROM	batch_job "; 
			$sql .= "FORCE INDEX (created_at_job_type_status_index) ";
			$sql .= "WHERE	CREATED_AT > DATE_ADD(NOW(), INTERVAL -1 HOUR) ";
			$sql .= "AND	JOB_TYPE = " . (int) $jobType . " ";
			$sql .= "AND	$t1 IS NOT NULL ";
			$sql .= "AND	$t2 IS NOT NULL ";

			$stmt = $con->query($sql);
			
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}
	
		$ret = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
		if(count($ret))
			return reset($ret);
			
		return 0;
	}

	public static function doQueueStatus(PDO $con = null)
	{
		if(is_null($con))
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//			$con = Propel::getConnection(BatchJobPeer::DATABASE_NAME);
		
		$stmt = null;
		try {
			$now = time();
			$sql = "SELECT		AVG(TIMEDIFF(NOW(), CREATED_AT)) AS CREATED_AT_AVG,
								COUNT(JOB_TYPE) AS JOB_TYPE_COUNT, 
								JOB_TYPE 
					FROM		batch_job 
					FORCE INDEX (created_at_job_type_status_index)
					WHERE		STATUS IN (" . BatchJob::BATCHJOB_STATUS_PENDING . ',' . BatchJob::BATCHJOB_STATUS_RETRY . ")
					AND			(
									CHECK_AGAIN_TIMEOUT < $now
									OR
									CHECK_AGAIN_TIMEOUT IS NULL
								)
					AND			JOB_TYPE IS NOT NULL
					AND			CREATED_AT > DATE_ADD(NOW(), INTERVAL -1 HOUR)
					GROUP BY	JOB_TYPE";
			
			$stmt = $con->query($sql);
			
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException($e);
		}
	
		return $stmt->fetchAll();
	}
	
	
	public static function postLockUpdate(kExclusiveLockKey $lockKey, array $exclusive_objects_ids, $con)
	{
		
		$batchJobs = BatchJobPeer::retrieveByPKs($exclusive_objects_ids);
		
		foreach($batchJobs as $batchJob) {
			
			/* @var $batchJob BatchJob */

			// Set history
			$uniqueId = new UniqueId();
			$historyRecord = new kBatchHistoryData();
			$historyRecord->setWorkerId($lockKey->getWorkerId());
			$historyRecord->setSchedulerId($lockKey->getSchedulerId());
			$historyRecord->setBatchIndex($lockKey->getBatchIndex());
			$historyRecord->setHostName((isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname()));
			$historyRecord->setSessionId((string)$uniqueId);
			
			$batchJob->addHistoryRecord($historyRecord);
			
			// Set fields
			$batchJob->setLastWorkerId($lockKey->getWorkerId());
			$batchJob->setLastSchedulerId($lockKey->getSchedulerId());
			
			// Set fields from batch job lock
			$lockInfo = $batchJob->getLockInfo();
			$lockInfo->setLockVersion($lockInfo->getLockVersion() + 1);
			$batchJob->setLockInfo($lockInfo);
				
			$batchJob->save($con);
		}
	
		return $batchJobs;
	}
	
	public static function preBatchJobUpdate(BatchJob $batchJob) {
		if($batchJob->isColumnModified(BatchJobPeer::ERR_NUMBER) || $batchJob->isColumnModified(BatchJobPeer::ERR_TYPE) || 
				$batchJob->isColumnModified(BatchJobPeer::MESSAGE)) {
			
			$historyRecord = new kBatchHistoryData();
			$historyRecord->setErrNumber($batchJob->getErrNumber());
			$historyRecord->setErrType($batchJob->getErrType());
			$historyRecord->setMessage($batchJob->getMessage());
				
			$batchJob->addHistoryRecord($historyRecord);
		}
	}
}

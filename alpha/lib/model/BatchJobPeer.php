<?php

/**
 * Subclass for performing query and update operations on the 'batch_job' table.
 *
 * 
 *
 * @package lib.model
 */ 
class BatchJobPeer extends BaseBatchJobPeer
{
	const COUNT = 'COUNT(batch_job.ID)';
	
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
			BatchJob::BATCHJOB_STATUS_DONT_PROCESS
		);
	}
	
	public static function getCheckAgainTimeout($job_type = null)
	{
		$jobCheckAgainTimeouts = kConf::get('job_retry_intervals');
		if(isset($jobCheckAgainTimeouts[$job_type]))
			return $jobCheckAgainTimeouts[$job_type];
			
		return kConf::get('default_job_retry_interval');
	}
	
	public static function getMaxExecutionAttempts($job_type = null)
	{
		$jobMaxExecutionAttempts = kConf::get('job_execution_attempt');
		if(isset($jobMaxExecutionAttempts[$job_type]))
			return $jobMaxExecutionAttempts[$job_type];
			
		return kConf::get('default_job_execution_attempt');
	}
	
	public static function getMaxDuplicationTime($job_type = null)
	{
		$jobMaxDuplicationTimes = kConf::get('job_duplication_time_frame');
		if(isset($jobMaxDuplicationTimes[$job_type]))
			return $jobMaxDuplicationTimes[$job_type];
			
		return kConf::get('default_duplication_time_frame');
	}
	
	public static function createDuplicationKey($jobType, $data)
	{
		switch($jobType)
		{
			case BatchJob::BATCHJOB_TYPE_IMPORT:
				if($data instanceof kImportJobData)
					return sha1($data->getSrcFileUrl());
				return null;
				
			case BatchJob::BATCHJOB_TYPE_PULL:
				if($data instanceof kPullJobData)
					return sha1($data->getSrcFileUrl());
				return null;
				
			case BatchJob::BATCHJOB_TYPE_EXTRACT_MEDIA:
				if($data instanceof kExtractMediaJobData)
					return sha1($data->getSrcFileSyncLocalPath());
				return null;
				
			case BatchJob::BATCHJOB_TYPE_CONVERT:
			case BatchJob::BATCHJOB_TYPE_DELETE:
			case BatchJob::BATCHJOB_TYPE_FLATTEN:
			case BatchJob::BATCHJOB_TYPE_BULKUPLOAD:
			case BatchJob::BATCHJOB_TYPE_DOWNLOAD:
			case BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE:
			case BatchJob::BATCHJOB_TYPE_POSTCONVERT:
			case BatchJob::BATCHJOB_TYPE_REMOTE_CONVERT:
			default:
				return null;
		}
		return null;
	}
	
	public static function retrieveDuplicated($jobType, $data)
	{
		$duplicationKey = self::createDuplicationKey($jobType, $data);
		if(!$duplicationKey)
			return null;
			
		$c = new Criteria();
		$c->add (self::CREATED_AT, date('Y-m-d H:i', time() - self::getMaxDuplicationTime($jobType)), Criteria::GREATER_THAN);
		$c->add (self::STATUS, BatchJob::BATCHJOB_STATUS_FINISHED);
		$c->add (self::DUPLICATION_KEY, $duplicationKey);
		$c->addDescendingOrderByColumn(self::CREATED_AT);
		$duplicatedJobs = self::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		if(!count($duplicatedJobs))
			return null;
			
		switch($jobType)
		{
			case BatchJob::BATCHJOB_TYPE_IMPORT:
				foreach($duplicatedJobs as $index => $duplicatedJob)
				{
					$duplicatedData = $duplicatedJob->getData();
					if(!($duplicatedData instanceof kImportJobData) || $duplicatedData->getSrcFileUrl() != $data->getSrcFileUrl())
						unset($duplicatedJobs[$index]);
				}
				return $duplicatedJobs;
				
			case BatchJob::BATCHJOB_TYPE_PULL:
				foreach($duplicatedJobs as $index => $duplicatedJob)
				{
					$duplicatedData = $duplicatedJob->getData();
					if(!($duplicatedData instanceof kPullJobData) || $duplicatedData->getSrcFileUrl() != $data->getSrcFileUrl())
						unset($duplicatedJobs[$index]);
				}
				return $duplicatedJobs;
				
			case BatchJob::BATCHJOB_TYPE_EXTRACT_MEDIA:
				foreach($duplicatedJobs as $index => $duplicatedJob)
				{
					$duplicatedData = $duplicatedJob->getData();
					if(!($duplicatedData instanceof kExtractMediaJobData) || $duplicatedData->getSrcFileSyncLocalPath() != $data->getSrcFileSyncLocalPath())
						unset($duplicatedJobs[$index]);
				}
				return $duplicatedJobs;
				
			case BatchJob::BATCHJOB_TYPE_CONVERT:
			case BatchJob::BATCHJOB_TYPE_DELETE:
			case BatchJob::BATCHJOB_TYPE_FLATTEN:
			case BatchJob::BATCHJOB_TYPE_BULKUPLOAD:
			case BatchJob::BATCHJOB_TYPE_DOWNLOAD:
			case BatchJob::BATCHJOB_TYPE_CONVERT_PROFILE:
			case BatchJob::BATCHJOB_TYPE_POSTCONVERT:
			case BatchJob::BATCHJOB_TYPE_REMOTE_CONVERT:
			default:
				return null;
		}
		return null;
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

	public static function doCountGroupBy(Criteria $criteria, $con = null)
	{
		$criteria = clone $criteria;
		$criteria->addSelectColumn(BatchJobPeer::COUNT);

		foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$cols = $criteria->getSelectColumns();
		
		return self::doSelectStmt($criteria, $con);
		
//		$rs->setFetchMode(ResultSet::FETCHMODE_ASSOC);
//			
//		$results = array();
//		while($rs->next()) 
//			$results[] = $rs->getRow();
//			
//		return $results;
	}	
	
}

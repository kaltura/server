<?php
  /**
	* Will perform get,update and free operations on database BatchJob objects
	* 
 * @package Core
 * @subpackage Batch
	*/
class kBatchExclusiveLock 
{
	private static function lockObjects(kExclusiveLockKey $lockKey, array $objects, $max_execution_time)
	{
		
		$exclusive_objects = array();

		// make sure the objects where not taken -
		$con = Propel::getConnection();
		
		$not_exclusive_count = 0;

		foreach ( $objects as $object )
		{
			$lock_version = $object->getLockVersion() ;
			$criteria_for_exclusive_update = new Criteria();
			$criteria_for_exclusive_update->add(BatchJobPeer::ID,$object->getId()); 
			$criteria_for_exclusive_update->add(BatchJobPeer::LOCK_VERSION, $lock_version);
			
			$update = new Criteria();

			// increment the lock_version - this will make sure it's exclusive
			$update->add(BatchJobPeer::LOCK_VERSION, $lock_version + 1);
			// increment the execution_attempts 
			$update->add(BatchJobPeer::EXECUTION_ATTEMPTS, $object->getExecutionAttempts() + 1);

			$update->add(BatchJobPeer::SCHEDULER_ID, $lockKey->getSchedulerId() );
			$update->add(BatchJobPeer::WORKER_ID, $lockKey->getWorkerId() );
			$update->add(BatchJobPeer::BATCH_INDEX, $lockKey->getBatchIndex() );
			
			$update->add(BatchJobPeer::LAST_SCHEDULER_ID, $lockKey->getSchedulerId() );
			$update->add(BatchJobPeer::LAST_WORKER_ID, $lockKey->getWorkerId() );
			
			$processor_expiration = time() + $max_execution_time;
			$update->add(BatchJobPeer::PROCESSOR_EXPIRATION, $processor_expiration);
			
			$affectedRows = BasePeer::doUpdate( $criteria_for_exclusive_update, $update, $con);
			
			KalturaLog::log("Lock update affected rows [$affectedRows] on job id [" . $object->getId() . "] lock version [$lock_version]");
			
			if ( $affectedRows == 1 )
			{
				// fix the object to reflect what is in the DB
				$object->setLockVersion ( $lock_version+1 );
				$object->setExecutionAttempts ( $object->getExecutionAttempts()+1 );
				$object->setSchedulerId ( $lockKey->getSchedulerId() );
				$object->setWorkerId ( $lockKey->getWorkerId() );
				$object->setBatchIndex ( $lockKey->getBatchIndex() );
				$object->setProcessorExpiration ( $processor_expiration );
				
				KalturaLog::log("Job id [" . $object->getId() . "] locked and returned");
			
				$exclusive_objects[] = $object;
			}
			else
			{
				$not_exclusive_count++;
				KalturaLog::log ( "Object not exclusive: [" . get_class ( $object ) . "] id [" . $object->getId() . "]" );  
			}
		}
		
		return $exclusive_objects;
	}
	
	
	/**
	 * will return BatchJob objects.
	 *
	 * @param kExclusiveLockKey $lockKey
	 * @param int $max_execution_time
	 * @param int $number_of_objects
	 * @param int $jobType
	 * @param BatchJobFilter $filter
	 */
	public static function getExclusiveJobs(kExclusiveLockKey $lockKey, $max_execution_time, $number_of_objects, $jobType, BatchJobFilter $filter)
	{
		$priority = kBatchManager::getNextJobPriority($jobType);
		
		$c = new Criteria();
		
		// added to support nfs delay
		if($jobType == BatchJobType::EXTRACT_MEDIA || $jobType == BatchJobType::POSTCONVERT || $jobType == BatchJobType::STORAGE_EXPORT)
			$c->add ( BatchJobPeer::CREATED_AT, (time() - 30), Criteria::LESS_THAN);
		
		$c->add ( BatchJobPeer::JOB_TYPE, $jobType );
		$c->add ( BatchJobPeer::PRIORITY, $priority, Criteria::GREATER_EQUAL );
		
		$filter->attachToCriteria($c);
		
		$c->addAscendingOrderByColumn(BatchJobPeer::PRIORITY);
		
		$max_exe_attempts = BatchJobPeer::getMaxExecutionAttempts($jobType);
		return self::getExclusive($c, $lockKey, $max_execution_time, $number_of_objects, $max_exe_attempts);
	}
	
	public static function getQueueSize(Criteria $c, $schedulerId, $workerId, $priority, $jobType)
	{
		$c->add ( BatchJobPeer::JOB_TYPE, $jobType );
		$c->add ( BatchJobPeer::PRIORITY, $priority, Criteria::GREATER_EQUAL );
		$c->addAscendingOrderByColumn(BatchJobPeer::PRIORITY);
		
		$max_exe_attempts = BatchJobPeer::getMaxExecutionAttempts($jobType);
		return self::getQueue($c, $schedulerId, $workerId, $max_exe_attempts);
	}

	public static function getExclusiveAlmostDoneJobs(Criteria $c, kExclusiveLockKey $lockKey, $max_execution_time, $number_of_objects, $priority, $jobType)
	{
		$c->add ( BatchJobPeer::JOB_TYPE, $jobType );
		$c->add ( BatchJobPeer::PRIORITY, $priority, Criteria::GREATER_EQUAL );
		$c->addAscendingOrderByColumn(BatchJobPeer::PRIORITY);
		
		$max_exe_attempts = BatchJobPeer::getMaxExecutionAttempts($jobType);
		return self::getExclusiveAlmostDone($c, $lockKey, $max_execution_time, $number_of_objects, $max_exe_attempts);
	}
	
	private static function getExclusiveAlmostDone(Criteria $c, kExclusiveLockKey $lockKey, $max_execution_time, $number_of_objects, $max_exe_attempts)
	{
		$schd = BatchJobPeer::SCHEDULER_ID;
		$work = BatchJobPeer::WORKER_ID;
		$btch = BatchJobPeer::BATCH_INDEX;
		$stat = BatchJobPeer::STATUS;
		$atmp = BatchJobPeer::EXECUTION_ATTEMPTS;
		$expr = BatchJobPeer::PROCESSOR_EXPIRATION;
		$recheck = BatchJobPeer::CHECK_AGAIN_TIMEOUT;
		
		$schd_id = $lockKey->getSchedulerId();
		$work_id = $lockKey->getWorkerId();
		$btch_id = $lockKey->getBatchIndex();
		$now = time();
		$now_str = date('Y-m-d H:i:s', $now);
		
		$query = "	(
							batch_job.STATUS = " . BatchJob::BATCHJOB_STATUS_ALMOST_DONE . "
						AND (
								$expr <= '$now_str'
							OR	(
									$schd = $schd_id 
								AND $work = $work_id 
								AND $btch = $btch_id 
							)
							OR	(
									$schd IS NULL 
								AND $work IS NULL 
								AND $btch IS NULL 
								AND (
										$recheck <= $now
									OR	$recheck IS NULL
								)
							)
						) 
						AND (
								$atmp <= $max_exe_attempts
							OR	$atmp IS NULL
						)
					)";
			
		$c->addAnd($c->getNewCriterion($stat, $query, Criteria::CUSTOM));
		$c->addAnd($c->getNewCriterion(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId()));
		
		$c->addAscendingOrderByColumn(BatchJobPeer::ID);
		$c->setLimit($number_of_objects);
		
//		$objects = BatchJobPeer::doSelect ( $c );
		$objects = BatchJobPeer::doSelect ( $c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
		
		return self::lockObjects($lockKey, $objects, $max_execution_time);
	}
	
	
	private static function getQueue(Criteria $c, $schedulerId, $workerId, $max_exe_attempts)
	{
		$schd = BatchJobPeer::SCHEDULER_ID;
		$work = BatchJobPeer::WORKER_ID;
		$stat = BatchJobPeer::STATUS;
		$atmp = BatchJobPeer::EXECUTION_ATTEMPTS;
		$expr = BatchJobPeer::PROCESSOR_EXPIRATION;
		$recheck = BatchJobPeer::CHECK_AGAIN_TIMEOUT;
		
		$schd_id = $schedulerId;
		$work_id = $workerId;
		$now = time();
		$now_str = date('Y-m-d H:i:s', $now);
		
		// same workers unfinished jobs 
		$query1 = "(
							$schd = $schd_id 
						AND $work = $work_id 
						AND $stat IN (" . BatchJobPeer::getInProcStatusList() . ") 
					)";
			
			
		//	"others unfinished jobs " - the expiration should be SMALLER than the current time to make sure the job is not 
		// being processed
		$closedStatuses = implode(',', BatchJobPeer::getClosedStatusList());
		$query2 = "(
							$stat NOT IN ($closedStatuses)
						AND	$expr <= '$now_str'
					)";
		
		// "retry jobs"
		$query3 = "(
						$stat = " . BatchJob::BATCHJOB_STATUS_RETRY  . "
						AND $recheck <= $now
					)";
									
		// "max attempts jobs"
		$queryMaxAttempts = "(
								$atmp <= $max_exe_attempts
								OR
								$atmp IS NULL
							)";
								
		$crit1 = $c->getNewCriterion($stat, BatchJob::BATCHJOB_STATUS_PENDING);
		$crit1->addOr($c->getNewCriterion($schd, $query1, Criteria::CUSTOM));
		$crit1->addOr($c->getNewCriterion($schd, $query2, Criteria::CUSTOM));
		$crit1->addOr($c->getNewCriterion($schd, $query3, Criteria::CUSTOM));
		
		$c->addAnd($crit1);
		$c->addAnd($c->getNewCriterion($atmp, $queryMaxAttempts, Criteria::CUSTOM));
		$c->addAnd($c->getNewCriterion(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId()));
		
//		$objects = BatchJobPeer::doCount ( $c );
		return BatchJobPeer::doCount( $c, false, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
	}
	
	/**
	 * will return $max_count of objects using the peer.
	 * The criteria will be used to filter the basic parameter, the function will encapsulate the inner logic of the BatchJob
	 * and the exclusiveness.
	 *
	 * @param Criteria $c
	 */
	private static function getExclusive(Criteria $c, kExclusiveLockKey $lockKey, $max_execution_time, $number_of_objects, $max_exe_attempts)
	{
		$schd = BatchJobPeer::SCHEDULER_ID;
		$work = BatchJobPeer::WORKER_ID;
		$btch = BatchJobPeer::BATCH_INDEX;
		$stat = BatchJobPeer::STATUS;
		$atmp = BatchJobPeer::EXECUTION_ATTEMPTS;
		$expr = BatchJobPeer::PROCESSOR_EXPIRATION;
		$recheck = BatchJobPeer::CHECK_AGAIN_TIMEOUT;
		
		$schd_id = $lockKey->getSchedulerId();
		$work_id = $lockKey->getWorkerId();
		$btch_id = $lockKey->getBatchIndex();
		$now = time();
		$now_str = date('Y-m-d H:i:s', $now);
		
		$unClosedStatuses = implode(',', BatchJobPeer::getUnClosedStatusList());
		$inProgressStatuses = BatchJobPeer::getInProcStatusList();
		
		$query = "	
						$stat IN ($unClosedStatuses)
					AND	(
							$expr <= '$now_str'
						OR	(
								(
									$stat = " . BatchJob::BATCHJOB_STATUS_PENDING . " 
								OR (
										$stat = " . BatchJob::BATCHJOB_STATUS_RETRY . "
									AND $recheck <= $now
								)
							) 
							AND (
									$schd IS NULL
								AND $work IS NULL 
								AND $btch IS NULL 
							)
						) 
						OR (
								$schd = $schd_id 
							AND $work = $work_id 
							AND $btch = $btch_id 
							AND $stat IN ($inProgressStatuses) 
						)
					) 
					AND (
							$atmp <= $max_exe_attempts
						OR	$atmp IS NULL
					)";
				
		$c->add($stat, $query, Criteria::CUSTOM);
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId());
		
		$c->addAscendingOrderByColumn(BatchJobPeer::ID);
		$c->setLimit($number_of_objects);
		
//		$objects = BatchJobPeer::doSelect ( $c );
		$objects = BatchJobPeer::doSelect ( $c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
		
		return self::lockObjects($lockKey, $objects, $max_execution_time);
	}

	public static function getExpiredJobs()
	{
		$jobTypes = kPluginableEnumsManager::coreValues('BatchJobType');
				
		$c = new Criteria();
		$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_FATAL, Criteria::NOT_EQUAL);
		$c->add(BatchJobPeer::DC, kDataCenterMgr::getCurrentDcId()); // each DC should clean its own jobs
//		$c->add(BatchJobPeer::PROCESSOR_EXPIRATION, time(), Criteria::LESS_THAN);
//		$c->add(BatchJobPeer::SCHEDULER_ID, 0, Criteria::GREATER_THAN);
//		$c->add(BatchJobPeer::WORKER_ID, 0, Criteria::GREATER_THAN);
		
		$jobs = array();
		foreach($jobTypes as $jobType)
		{
			$typedCrit = clone $c;
			$typedCrit->add(BatchJobPeer::EXECUTION_ATTEMPTS, BatchJobPeer::getMaxExecutionAttempts($jobType), Criteria::GREATER_THAN);
			$typedCrit->add(BatchJobPeer::JOB_TYPE, $jobType);
			
			$typedJobs = BatchJobPeer::doSelect($typedCrit, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
			foreach($typedJobs as $typedJob)
				$jobs[] = $typedJob;
		}
		
		return $jobs;
	}
	
	/**
	 * @param int $id
	 * @param kExclusiveLockKey $lockKey
	 * @param BatchJob $object
	 * @return BatchJob
	 */
	public static function updateExclusive($id, kExclusiveLockKey $lockKey, BatchJob $object)
	{
		$c = new Criteria();
		$c->add(BatchJobPeer::ID, $id );
		$c->add(BatchJobPeer::SCHEDULER_ID, $lockKey->getSchedulerId() );			
		$c->add(BatchJobPeer::WORKER_ID, $lockKey->getWorkerId() );			
		$c->add(BatchJobPeer::BATCH_INDEX, $lockKey->getBatchIndex() );
		
		$db_object = BatchJobPeer::doSelectOne($c);
		if ( $db_object  )
		{
			baseObjectUtils::fillObjectFromObject( BatchJobPeer::getFieldNames() ,  $object , $db_object , baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME );
			$db_object->save();
			return $db_object;
		}
		
		$db_object = BatchJobPeer::retrieveByPk ( $id  );
		throw new APIException ( APIErrors::UPDATE_EXCLUSIVE_JOB_FAILED , $id,$lockKey->getSchedulerId(), $lockKey->getWorkerId(), $lockKey->getBatchIndex(), print_r ( $db_object , true ));
	}
	
	
	/**
	 * 
	 * @param $id
	 * @param kExclusiveLockKey $lockKey
	 * @param $pending_status - optional. will be used to set the status once the object is free 
	 * @return BatchJob
	 */
	public static function freeExclusive($id, kExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$c = new Criteria();
		
		$c->add(BatchJobPeer::ID, $id );
		$c->add(BatchJobPeer::SCHEDULER_ID, $lockKey->getSchedulerId() );			
		$c->add(BatchJobPeer::WORKER_ID, $lockKey->getWorkerId() );			
		$c->add(BatchJobPeer::BATCH_INDEX, $lockKey->getBatchIndex() );
		
		$db_object = BatchJobPeer::doSelectOne ( $c );
		if(!$db_object)
			throw new APIException(APIErrors::FREE_EXCLUSIVE_JOB_FAILED, $id, $lockKey->getSchedulerId(), $lockKey->getWorkerId(), $lockKey->getBatchIndex());
		
		if($resetExecutionAttempts)
			$db_object->setExecutionAttempts(0);
			
		$db_object->setSchedulerId( null );
		$db_object->setWorkerId( null );
		$db_object->setBatchIndex( null );
		$db_object->setProcessorExpiration( null );
		$db_object->save();
	
		
		if($db_object->getStatus() != BatchJob::BATCHJOB_STATUS_ABORTED && $db_object->getAbort())
		{
			$db_object = kJobsManager::abortDbBatchJob($db_object);
		}
		
		return $db_object;
	}
}


?>
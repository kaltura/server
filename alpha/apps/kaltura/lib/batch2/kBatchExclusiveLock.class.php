<?php
  /**
	* Will perform get,update and free operations on database BatchJob objects
	*
 * @package Core
 * @subpackage Batch
	*/
class kBatchExclusiveLock
{
	const UNLIMITED_QUOTA = 99999;
	
	private static function lockObjects(kExclusiveLockKey $lockKey, array $objects, $max_execution_time)
	{
		
		$exclusive_objects_ids = array();

		// make sure the objects where not taken -
		$con = Propel::getConnection();
		
		$not_exclusive_count = 0;

		foreach ( $objects as $object )
		{
			$lock_version = $object->getVersion() ;
			$criteria_for_exclusive_update = new Criteria();
			$criteria_for_exclusive_update->add(BatchJobLockPeer::ID,$object->getId());
			$criteria_for_exclusive_update->add(BatchJobLockPeer::VERSION, $lock_version);
			
			$update = new Criteria();

			// increment the lock_version - this will make sure it's exclusive
			$update->add(BatchJobLockPeer::VERSION, $lock_version + 1);
			// increment the execution_attempts
			$update->add(BatchJobLockPeer::EXECUTION_ATTEMPTS, $object->getExecutionAttempts() + 1);

			$update->add(BatchJobLockPeer::SCHEDULER_ID, $lockKey->getSchedulerId() );
			$update->add(BatchJobLockPeer::WORKER_ID, $lockKey->getWorkerId() );
			$update->add(BatchJobLockPeer::BATCH_INDEX, $lockKey->getBatchIndex() );
			
			$expiration = time() + $max_execution_time;
			$update->add(BatchJobLockPeer::EXPIRATION, $expiration);
			
			$affectedRows = BasePeer::doUpdate( $criteria_for_exclusive_update, $update, $con);
			
			KalturaLog::log("Lock update affected rows [$affectedRows] on job id [" . $object->getId() . "] lock version [$lock_version]");
			
			if ( $affectedRows == 1 )
			{
				// fix the object to reflect what is in the DB
				$object->setVersion ( $lock_version+1 );
				$object->setExecutionAttempts ( $object->getExecutionAttempts()+1 );
				$object->setSchedulerId ( $lockKey->getSchedulerId() );
				$object->setWorkerId ( $lockKey->getWorkerId() );
				$object->setBatchIndex ( $lockKey->getBatchIndex() );
				$object->setExpiration ( $expiration );
				
				KalturaLog::log("Job id [" . $object->getId() . "] locked and returned");
				PartnerLoadPeer::updatePartnerLoad($object->getPartnerId(), $object->getJobType(), $object->getJobSubType(), $con);
			
				$exclusive_objects_ids[] = $object->getId();
			}
			else
			{
				$not_exclusive_count++;
				KalturaLog::log ( "Object not exclusive: [" . get_class ( $object ) . "] id [" . $object->getId() . "]" );
			}
		}
		
		return BatchJobPeer::postLockUpdate($lockKey, $exclusive_objects_ids, $con);
	}
	
	
	/**
	 * will return BatchJob objects.
	 *
	 * @param kExclusiveLockKey $lockKey
	 * @param int $max_execution_time
	 * @param int $number_of_objects
	 * @param int $jobType
	 * @param BatchJobFilter $filter
	 * @param int $maxOffset
	 * @param int $maxJobToPullForCache
	 */
	public static function getExclusiveJobs(kExclusiveLockKey $lockKey, $max_execution_time, $number_of_objects, $jobType,
			BatchJobFilter $filter, $maxJobToPullForCache = 0)
	{
		$c = new Criteria();
		$filter->attachToCriteria($c);
		
		$objects = kJobsCacher::getJobs($c, $lockKey, $number_of_objects, $jobType, $maxJobToPullForCache);
		KalturaLog::debug("Return allocated job with ids: " .print_r(array_map(function($job){return $job->getId();},$objects), true));
		return self::lockObjects($lockKey, $objects, $max_execution_time);
	}
	
	public static function getQueueSize(Criteria $c, $workerId, $jobType)
	{
		$c->add ( BatchJobLockPeer::JOB_TYPE, $jobType );
		
		$max_exe_attempts = BatchJobLockPeer::getMaxExecutionAttempts($jobType);
		return kJobsCacher::getQueue($c, $workerId, $max_exe_attempts);
	}

	public static function getExclusiveAlmostDone(Criteria $c, kExclusiveLockKey $lockKey, $max_execution_time, $number_of_objects, $jobType)
	{
		$schd = BatchJobLockPeer::SCHEDULER_ID;
		$work = BatchJobLockPeer::WORKER_ID;
		$btch = BatchJobLockPeer::BATCH_INDEX;
		$stat = BatchJobLockPeer::STATUS;
		$atmp = BatchJobLockPeer::EXECUTION_ATTEMPTS;
		$expr = BatchJobLockPeer::EXPIRATION;
		$recheck = BatchJobLockPeer::START_AT;
		$partnerLoadQuota = PartnerLoadPeer::QUOTA;
		
		$schd_id = $lockKey->getSchedulerId();
		$work_id = $lockKey->getWorkerId();
		$btch_id = $lockKey->getBatchIndex();
		$now = time();
		$now_str = date('Y-m-d H:i:s', $now);
		
		$c->add ( BatchJobLockPeer::JOB_TYPE, $jobType );
		$c->addAnd($c->getNewCriterion(BatchJobLockPeer::DC, kDataCenterMgr::getCurrentDcId()));
		$c->add(BatchJobLockPeer::BATCH_VERSION, BatchJobLockPeer::getBatchVersion($jobType), Criteria::EQUAL);
		
		$prioritizers_ratio = BatchJobLockPeer::getPrioritizersRatio($jobType);
		$shouldUseJoin = (BatchJobLockPeer::getMaxJobsForPartner($jobType) != self::UNLIMITED_QUOTA);
		self::addPrioritizersCondition($c, $prioritizers_ratio, $shouldUseJoin);
		
		// Query Parts
		$statusCondition = "$stat = " . BatchJob::BATCHJOB_STATUS_ALMOST_DONE;
		$lockExpiredCondition = "$expr <= '$now_str'";
		
		$recheckCondition = "( $recheck <= '$now_str' OR $recheck IS NULL)";
		$unhandledJobCondition = "$schd IS NULL AND $work IS NULL AND $btch IS NULL ";
		$jobAlreadyHandledByWorker = "$schd = $schd_id AND $work = $work_id AND $btch = $btch_id";
		$newJobsCond = "($unhandledJobCondition AND ($recheckCondition))";
		if($shouldUseJoin) {
			$partnerLoadCondition = "(($partnerLoadQuota > 0) OR ($partnerLoadQuota is null))";
			$newJobsCond .= " AND $partnerLoadCondition";
		}
			
		$max_exe_attempts = BatchJobLockPeer::getMaxExecutionAttempts($jobType);
		$jobWasntExecutedTooMany = "$atmp <= $max_exe_attempts OR $atmp IS NULL";
		
		// Generate query
		$query = "	($statusCondition
						AND ($lockExpiredCondition
							OR	($newJobsCond)
							OR	($jobAlreadyHandledByWorker)
						)
						AND ($jobWasntExecutedTooMany)
					)";
			
		$c->addAnd($c->getNewCriterion($stat, $query, Criteria::CUSTOM));
		$c->setLimit($number_of_objects);
		
		$objects = BatchJobLockPeer::doSelect ( $c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
		
		return self::lockObjects($lockKey, $objects, $max_execution_time);
	}
	
	
	public static function getQueue(Criteria $c, $max_exe_attempts)
	{
		$schd = BatchJobLockPeer::SCHEDULER_ID;
		$stat = BatchJobLockPeer::STATUS;
		$atmp = BatchJobLockPeer::EXECUTION_ATTEMPTS;
		$expr = BatchJobLockPeer::EXPIRATION;
		$recheck = BatchJobLockPeer::START_AT;
		
		$now = time();
		$now_str = date('Y-m-d H:i:s', $now);
		
			
		//	"others unfinished jobs " - the expiration should be SMALLER than the current time to make sure the job is not
		// being processed
		$unclosedStatuses = BatchJobPeer::getUnClosedStatusList();
		$unclosedStatuses = implode(',', $unclosedStatuses);
		
		$query1 = "(
							$stat IN ($unclosedStatuses)
						AND	$expr <= '$now_str'
					)";
		
		// "retry jobs"
		$query2 = "(
						$stat IN (" . BatchJob::BATCHJOB_STATUS_RETRY  . ", " . BatchJob::BATCHJOB_STATUS_ALMOST_DONE  . ")
						AND $recheck <= '$now_str'
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
		
		$c->addAnd($crit1);
		$c->addAnd($c->getNewCriterion($atmp, $queryMaxAttempts, Criteria::CUSTOM));
		$c->addAnd($c->getNewCriterion(BatchJobLockPeer::DC, kDataCenterMgr::getCurrentDcId()));
		
		return BatchJobLockPeer::doCount( $c, false, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
	}
	
	/**
	 * will return $max_count of objects using the peer.
	 * The criteria will be used to filter the basic parameter, the function will encapsulate the inner logic of the BatchJob
	 * and the exclusiveness.
	 *
	 * @param Criteria $c
	 */
	public static function getJobs(Criteria $c, $number_of_objects, $jobType)
	{
		$schd = BatchJobLockPeer::SCHEDULER_ID;
		$work = BatchJobLockPeer::WORKER_ID;
		$btch = BatchJobLockPeer::BATCH_INDEX;
		$stat = BatchJobLockPeer::STATUS;
		$atmp = BatchJobLockPeer::EXECUTION_ATTEMPTS;
		$expr = BatchJobLockPeer::EXPIRATION;
		$recheck = BatchJobLockPeer::START_AT;
		$partnerLoadQuota = PartnerLoadPeer::QUOTA;

		$now = time();
		$now_str = date('Y-m-d H:i:s', $now);
		
		$delayedJobTypes = kConf::get('delayed_job_types');
		$apiJobType = kPluginableEnumsManager::coreToApi('BatchJobType', $jobType);
		
		// added to support nfs delay
		if(in_array($apiJobType, $delayedJobTypes))
		{
			$interval = kConf::hasParam('nfs_safety_margin_sec') ? kConf::get('nfs_safety_margin_sec') : 5;
			$c->add ( BatchJobLockPeer::CREATED_AT, (time() - $interval), Criteria::LESS_THAN);
		}
		
		$c->add ( BatchJobLockPeer::JOB_TYPE, $jobType );
		$c->add(BatchJobLockPeer::DC, kDataCenterMgr::getCurrentDcId());
		$c->add(BatchJobLockPeer::BATCH_VERSION, BatchJobLockPeer::getBatchVersion($jobType), Criteria::EQUAL);
		
		$prioritizers_ratio = BatchJobLockPeer::getPrioritizersRatio($jobType);
		$shouldUseJoin = (BatchJobLockPeer::getMaxJobsForPartner($jobType) != self::UNLIMITED_QUOTA);
		self::addPrioritizersCondition($c, $prioritizers_ratio, $shouldUseJoin);
		
		// Query Parts
		$unClosedStatuses = implode(',', BatchJobPeer::getUnClosedStatusList());
		$statusCondition = "$stat IN ($unClosedStatuses)";
		
		$lockExpiredCondition = "$expr <= '$now_str'";
		
		$pendingJobsCondition = "( $stat = " . BatchJob::BATCHJOB_STATUS_PENDING .
			" OR ( $stat = " . BatchJob::BATCHJOB_STATUS_RETRY . " AND $recheck <= '$now_str' ))";
		$unhandledJobCondition = "$schd IS NULL AND $work IS NULL AND $btch IS NULL ";
		$newJobsCond = "($pendingJobsCondition AND ($unhandledJobCondition))";
		if($shouldUseJoin) {
			$partnerLoadCondition = "(($partnerLoadQuota > 0) OR ($partnerLoadQuota is null))";
			$newJobsCond .= " AND $partnerLoadCondition";
		}
		
			
		$max_exe_attempts = BatchJobLockPeer::getMaxExecutionAttempts($jobType);
		$jobWasntExecutedTooMany = "$atmp <= $max_exe_attempts OR $atmp IS NULL";
		
		// Generate query
		$query = "	$statusCondition
					AND	(
						$lockExpiredCondition
						OR	($newJobsCond)
					)
					AND ($jobWasntExecutedTooMany)";
				
		$c->add($stat, $query, Criteria::CUSTOM);

		$c->setLimit($number_of_objects);
		
		return BatchJobLockPeer::doSelect ( $c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2) );
	}
	
	/**
	 * Add the prioritizer specific condition.
	 * @param Criteria $c The criteria to update
	 * @param int $prioritizers_ratio the rate between the max throughput prioritizers to the fair one
	 * @param bool $shouldUseJoin whether we should use join
	 * @return bool whether we use the fair prioritizer
	 */
	private static function addPrioritizersCondition(Criteria $c, $prioritizers_ratio, $shouldUseJoin = true)
	{
		$rand = rand(0, 100);
		if($shouldUseJoin) {
			$c->addMultipleJoin(array(array(BatchJobLockPeer::PARTNER_ID, PartnerLoadPeer::PARTNER_ID  ),
					array(BatchJobLockPeer::JOB_TYPE, PartnerLoadPeer::JOB_TYPE),
					array(BatchJobLockPeer::JOB_SUB_TYPE, PartnerLoadPeer::JOB_SUB_TYPE),
					array(BatchJobLockPeer::DC, PartnerLoadPeer::DC)), Criteria::LEFT_JOIN);
		} else {
			return false;
		}
		
		if($rand < $prioritizers_ratio)
		{	// Throughput
			$c->addAscendingOrderByColumn(BatchJobLockPeer::URGENCY);
			$c->addAscendingOrderByColumn(BatchJobLockPeer::ESTIMATED_EFFORT);
			return false;
		} else {
			// Fairness
			$c->addAscendingOrderByColumn(PartnerLoadPeer::WEIGHTED_PARTNER_LOAD);
			$c->addAscendingOrderByColumn(BatchJobLockPeer::PRIORITY);
			$c->addAscendingOrderByColumn(BatchJobLockPeer::URGENCY);
			$c->addAscendingOrderByColumn(BatchJobLockPeer::ESTIMATED_EFFORT);
			return true;
		}
	}

	/**
	 * This function returns all batch jobs in which the batch_job_lock status is RETRY
	 * and the batch_job_sep status is fatal.
	 * Those kind of cases can happen when a fatal error occured - Those cases must be investigated
	 */
	public static function getStatusInconsistentJob()
	{
		$c = new Criteria();
		$c->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_RETRY, Criteria::EQUAL);
		$c->addJoin(BatchJobLockPeer::BATCH_JOB_ID, BatchJobPeer::ID, Criteria::JOIN);
		$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_FATAL, Criteria::EQUAL);
		$c->add(BatchJobLockPeer::DC, kDataCenterMgr::getCurrentDcId());
		
		$jobs = BatchJobLockPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		return $jobs;
	}
	
	public static function getExpiredJobs()
	{
		$jobTypes = kPluginableEnumsManager::coreValues('BatchJobType');
		$executionAttempts2jobTypes = array();
		
		// Map between max execution attempts and job types
		foreach($jobTypes as $jobType)
		{
			$executionAttempts = BatchJobLockPeer::getMaxExecutionAttempts($jobType);
			if(array_key_exists($executionAttempts, $executionAttempts2jobTypes))
				$executionAttempts2jobTypes[$executionAttempts][] = $jobType;
			else
				$executionAttempts2jobTypes[$executionAttempts] = array($jobType);
		}
				
		// create query
		$c = new Criteria();
		$c->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_FATAL, Criteria::NOT_EQUAL);
		$c->add(BatchJobLockPeer::DC, kDataCenterMgr::getCurrentDcId()); // each DC should clean its own jobs
		
		// Query for each job type
		$batchJobLocks = array();
		foreach($executionAttempts2jobTypes as $execAttempts => $jobTypes)
		{
			$typedCrit = clone $c;
			$typedCrit->add(BatchJobLockPeer::EXECUTION_ATTEMPTS, $execAttempts, Criteria::GREATER_THAN);
			$typedCrit->add(BatchJobLockPeer::JOB_TYPE, $jobTypes, Criteria::IN);
			
			$typedJobs = BatchJobLockPeer::doSelect($typedCrit, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
			foreach($typedJobs as $typedJob) {
				$batchJobLocks[$typedJob->getId()] = $typedJob;
			}
		}
		
		// get matching batch jobs
		return BatchJobPeer::retrieveByPKs(array_keys($batchJobLocks));
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
		$c->add(BatchJobLockPeer::ID, $id );
		$c->add(BatchJobLockPeer::SCHEDULER_ID, $lockKey->getSchedulerId() );
		$c->add(BatchJobLockPeer::WORKER_ID, $lockKey->getWorkerId() );
		$c->add(BatchJobLockPeer::BATCH_INDEX, $lockKey->getBatchIndex() );
		
		$db_lock_object = BatchJobLockPeer::doSelectOne($c);
		if(!$db_lock_object) {
			throw new APIException ( APIErrors::UPDATE_EXCLUSIVE_JOB_FAILED , $id,$lockKey->getSchedulerId(), $lockKey->getWorkerId(), $lockKey->getBatchIndex(), print_r ( $db_lock_object , true ));
		}
		
		if($db_lock_object) {
			$db_object = $db_lock_object->getBatchJob();
		} else {
			$db_object = BatchJobPeer::retrieveByPk ($id);
		}
		
		baseObjectUtils::fillObjectFromObject( BatchJobPeer::getFieldNames() ,  $object , $db_object , baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME );
		$db_object->save();
		return $db_object;
	}
		
	
	/**
	 *
	 * @param $id
	 * @param kExclusiveLockKey db_lock_object
	 * @param db_lock_objectstatus - optional. will be used to set the status once the object is free
	 * @return BatchJob
	 */
	public static function freeExclusive($id, kExclusiveLockKey $lockKey, $resetExecutionAttempts = false)
	{
		$c = new Criteria();
		
		$c->add(BatchJobLockPeer::ID, $id );
		$c->add(BatchJobLockPeer::SCHEDULER_ID, $lockKey->getSchedulerId() );
		$c->add(BatchJobLockPeer::WORKER_ID, $lockKey->getWorkerId() );
		$c->add(BatchJobLockPeer::BATCH_INDEX, $lockKey->getBatchIndex() );
		
		$db_lock_object = BatchJobLockPeer::doSelectOne ( $c );
		
		if(!$db_lock_object) {
			if(BatchJobLockPeer::retrieveByPK($id))
				throw new APIException(APIErrors::FREE_EXCLUSIVE_JOB_FAILED, $id, $lockKey->getSchedulerId(), $lockKey->getWorkerId(), $lockKey->getBatchIndex());
			else
				return BatchJobPeer::retrieveByPK($id);
		}
		
		$db_object = $db_lock_object->getBatchJob();
		if($resetExecutionAttempts || in_array($db_lock_object->getStatus(), BatchJobPeer::getClosedStatusList()))
			$db_lock_object->setExecutionAttempts(0);
			
		$db_lock_object->setSchedulerId( null );
		$db_lock_object->setWorkerId( null );
		$db_lock_object->setBatchIndex( null );
		$db_lock_object->setExpiration( null );
		$db_lock_object->save();
	
		
		if(($db_object->getStatus() != BatchJob::BATCHJOB_STATUS_ABORTED) &&
				($db_object->getExecutionStatus() == BatchJobExecutionStatus::ABORTED))
			$db_object = kJobsManager::abortDbBatchJob($db_object);
		
		return $db_object;
	}
}


?>
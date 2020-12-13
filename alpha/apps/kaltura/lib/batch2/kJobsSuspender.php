<?php

/**
 * This class centralizes job suspension utilities
 * 
 * @package Core
 * @subpackage Batch
 *
 */
class kJobsSuspender {
	
	/** The maximum number of jobs we will handle for each (dc,partner_id,job_type,job_sub_type) */
	const MAX_PROCESSED_ROWS = 500;
	
	/**
	 * Entry point to job balancing.
	 * - Move jobs from status '0' to status '11' in case there are too many pending jobs
	 * - move jobs back from status '11' to status '0' in case the load is over.
	 * 
	 */
	public static function balanceJobsload() {
	
		$minPendingJobs = self::getSuspenderMinPendingJobs();
		$maxPendingJobs = self::getSuspenderMaxPendingJobs();
		
		$dcId = kDataCenterMgr::getCurrentDcId();
		$loadedKeys = array();
	
		// Suspend Jobs
		$c = self::createJobBalanceQuery($dcId, $minPendingJobs);
		$stmt = BatchJobLockPeer::doSelectStmt($c);
		$rows= $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		foreach ($rows as $row) {
			$partnerId = $row['PARTNER_ID'];
			$jobType = $row['JOB_TYPE'];
			$jobSubType = $row['JOB_SUB_TYPE'];
				
			$loadedKeys[] = $partnerId . "#" . $jobType . "#" . $jobSubType;
			$jobCount = $row[BatchJobLockPeer::COUNT];

			if($jobCount > $maxPendingJobs) {
				self::suspendJobs(($jobCount - $maxPendingJobs), $dcId, $partnerId, $jobType, $jobSubType);
			}
		}
	
		// Unsuspend jobs
		$c = self::createReturnBalancedJobsQuery($dcId);
		$stmt = BatchJobLockSuspendPeer::doSelectStmt($c);
		$rows= $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		foreach ($rows as $row) {
			$partnerId = $row['PARTNER_ID'];
			$jobType = $row['JOB_TYPE'];
			$jobSubType = $row['JOB_SUB_TYPE'];
			$key = $partnerId . "#" . $jobType . "#" . $jobSubType;
				
			if(!in_array($key, $loadedKeys))
				self::unsuspendJobs(($maxPendingJobs - $minPendingJobs), $dcId, $partnerId, $jobType, $jobSubType);
		}
	}
	
	/**
	 * This function generates the query that gets all the jobs that needs to be suspended.
	 */
	private static function createJobBalanceQuery($dc, $value) {
		$c = new Criteria();
	
		// Where
		$c->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_PENDING, Criteria::EQUAL);
		$c->add(BatchJobLockPeer::DC, $dc, Criteria::EQUAL);
		$c->add(BatchJobLockPeer::SCHEDULER_ID, null, Criteria::ISNULL);
		// Group by
		$c->addGroupByColumn(BatchJobLockPeer::DC);
		$c->addGroupByColumn(BatchJobLockPeer::PARTNER_ID);
		$c->addGroupByColumn(BatchJobLockPeer::JOB_TYPE);
		$c->addGroupByColumn(BatchJobLockPeer::JOB_SUB_TYPE);
		// Having
		$c->addHaving($c->getNewCriterion(BatchJobLockPeer::ID, BatchJobLockPeer::COUNT . '>' . $value, Criteria::CUSTOM));
		// Select
		$c->addSelectColumn(BatchJobLockPeer::COUNT);
		foreach($c->getGroupByColumns() as $column)
			$c->addSelectColumn($column);
		return $c;
	}
	
	/**
	 * This function generates the query that gets all the jobs that needs to be unsuspended.
	 * All those jobs will appear in batch_job_lock_suspend table
	 */
	private static function createReturnBalancedJobsQuery($dc) {
		$c = new Criteria();
	
		// Where
		$c->add(BatchJobLockSuspendPeer::DC, $dc, Criteria::EQUAL);
		// Group by
		$c->addGroupByColumn(BatchJobLockSuspendPeer::DC);
		$c->addGroupByColumn(BatchJobLockSuspendPeer::PARTNER_ID);
		$c->addGroupByColumn(BatchJobLockSuspendPeer::JOB_TYPE);
		$c->addGroupByColumn(BatchJobLockSuspendPeer::JOB_SUB_TYPE);
		foreach($c->getGroupByColumns() as $column)
			$c->addSelectColumn($column);
		return $c;
	}
	
	/**
	 * This function suspends up to ($limit) jobs of a given ($dc, $partnerId, $jobType, $jobSubType) 
	 */
	private static function suspendJobs($limit, $dc, $partnerId, $jobType, $jobSubType) {
	
		$limit = min(self::MAX_PROCESSED_ROWS, $limit);
		
		// Find IDs
		$c = new Criteria();
		$c->addSelectColumn(BatchJobLockPeer::ID);
		$c->add( BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_PENDING, Criteria::EQUAL);
		$c->add( BatchJobLockPeer::DC, $dc, Criteria::EQUAL);
		$c->add( BatchJobLockPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		$c->add( BatchJobLockPeer::JOB_TYPE, $jobType, Criteria::EQUAL);
		$c->add( BatchJobLockPeer::JOB_SUB_TYPE, $jobSubType, Criteria::EQUAL);
		$c->add( BatchJobLockPeer::SCHEDULER_ID, null, Criteria::ISNULL);
	
		$c->addDescendingOrderByColumn(BatchJobLockPeer::PRIORITY);
		$c->addDescendingOrderByColumn(BatchJobLockPeer::URGENCY);
		$c->addDescendingOrderByColumn(BatchJobLockPeer::ESTIMATED_EFFORT);
		$c->setLimit($limit);
	
		$stmt = BatchJobLockPeer::doSelectStmt($c);
		$jobIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
	
		// Suspend chosen ids
		$res = self::setJobsStatus($jobIds, BatchJob::BATCHJOB_STATUS_SUSPEND, BatchJob::BATCHJOB_STATUS_PENDING, true);
		$rootJobIds = self::suspendRootJobs($jobIds);
		self::moveToSuspendedJobsTable(array_merge($jobIds, $rootJobIds));
		KalturaLog::info("$res jobs of partner ($partnerId) job type ($jobType / $jobSubType) on DC ($dc) were suspended");
		KalturaLog::info("As a result, ". count($rootJobIds) . " root jobs were suspended.");
	}
	
	/**
	 * This function unsuspends up to ($limit) jobs of a given ($dc, $partnerId, $jobType, $jobSubType)
	 */
	private static function unsuspendJobs($limit, $dc, $partnerId, $jobType, $jobSubType) {
	
		$limit = min(self::MAX_PROCESSED_ROWS, $limit);
		
		// Find IDs from Batch Job Suspend Table
		$c = new Criteria();
		$c->addSelectColumn(BatchJobLockSuspendPeer::ID);
		$c->add( BatchJobLockSuspendPeer::DC, $dc, Criteria::EQUAL);
		$c->add( BatchJobLockSuspendPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		$c->add( BatchJobLockSuspendPeer::JOB_TYPE, $jobType, Criteria::EQUAL);
		$c->add( BatchJobLockSuspendPeer::JOB_SUB_TYPE, $jobSubType, Criteria::EQUAL);
		$c->add( BatchJobLockSuspendPeer::STATUS, BatchJob::BATCHJOB_STATUS_SUSPEND);
	
		$c->addAscendingOrderByColumn(BatchJobLockSuspendPeer::PRIORITY);
		$c->addAscendingOrderByColumn(BatchJobLockSuspendPeer::URGENCY);
		$c->addAscendingOrderByColumn(BatchJobLockSuspendPeer::ESTIMATED_EFFORT);
		$c->setLimit($limit);
	
		$stmt = BatchJobLockSuspendPeer::doSelectStmt($c);
		$jobIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
	
		// Return the jobs from batch_job_lock_suspend table
		self::moveFromSuspendedJobsTable($jobIds);
		$rootJobIds = self::unsuspendRootJob($jobIds, false);
		// Update the jobs status to pending
		$res = self::setJobsStatus($jobIds, BatchJob::BATCHJOB_STATUS_PENDING, BatchJob::BATCHJOB_STATUS_SUSPEND, false);
		$resRoot = self::setJobsStatus($rootJobIds, BatchJob::BATCHJOB_STATUS_ALMOST_DONE, BatchJob::BATCHJOB_STATUS_SUSPEND_ALMOST_DONE, false);
		KalturaLog::info("$res jobs of partner ($partnerId) job type ($jobType / $jobSubType) on DC ($dc) were unsuspended");
		KalturaLog::info("As a result $resRoot were unsuspended.");
	}
	
	/**
	 * This function sets the status of the given $jobIds to the given $status.
	 * We're saved from the kFlowManager::updatedJob callback since we work directly through the DB. 
	 * No callbacks are called.
	 * Returns the number of the affected rows.
	 */
	private static function setJobsStatus($jobIds, $status, $oldStatus, $addSchedulerCond) {
	
		$suspenderUpdateChunk = self::getSuspenderUpdateChunk();
		
		$affectedRows = 0;
		$con = Propel::getConnection();
		$update = new Criteria();
		$update->add(BatchJobLockPeer::STATUS, $status);
	
		$start = 0;
		$end = sizeof($jobIds);
		while($start < $end) {
			$updateCond = new Criteria();
			$updateCond->add(BatchJobLockPeer::ID, array_slice($jobIds, $start, min($suspenderUpdateChunk, $end - $start)), Criteria::IN);
			$updateCond->add(BatchJobLockPeer::STATUS, $oldStatus, Criteria::EQUAL);
			if($addSchedulerCond)
				$updateCond->add(BatchJobLockPeer::SCHEDULER_ID, null, Criteria::ISNULL);
			
			$affectedRows += BasePeer::doUpdate($updateCond, $update, $con);
			$start += $suspenderUpdateChunk;
		}
		return $affectedRows;
	}
	
	private static function suspendRootJobs($jobIds) {
		if(empty($jobIds))
			return array();
		
		// Retrieve root jobs ids 
		$c = new Criteria();
		$c->addSelectColumn(BatchJobLockPeer::ROOT_JOB_ID);
		$c->add(BatchJobLockPeer::ID, $jobIds, Criteria::IN);
		$c->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_SUSPEND);
		$c->setDistinct();
		$stmt = BatchJobLockPeer::doSelectStmt($c);
		$rootIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
		
		// Update root jobs status to be almost done 
		$suspenderUpdateChunk = self::getSuspenderUpdateChunk();
		$update = new Criteria();
		$update->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_SUSPEND_ALMOST_DONE);
		
		$affectedRows = 0;
		$start = 0;
		$end = sizeof($rootIds);
		$con = Propel::getConnection();
		while($start < $end) {
			// Retrieve parentIds
			$updateCond = new Criteria();
			$updateCond->add(BatchJobLockPeer::ID, array_slice($rootIds, $start, min($suspenderUpdateChunk, $end - $start)), Criteria::IN);
			$updateCond->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_ALMOST_DONE, Criteria::EQUAL);
			$updateCond->add(BatchJobLockPeer::SCHEDULER_ID, null, Criteria::ISNULL);
			
			$affectedRows += BasePeer::doUpdate($updateCond, $update, $con);
			$start += $suspenderUpdateChunk;
		}
		return $rootIds;
	}
	
	
	private static function unsuspendRootJob($jobIds, $idsAreRoot)
	{
		if(empty($jobIds))
		{
			return array();
		}

		// Get possible root job ids
		// select root_job_id from batch_job_lock where id in (unsuspended jobs)
		$c = new Criteria();
		$c->addSelectColumn(BatchJobLockPeer::ROOT_JOB_ID);
		$c->setDistinct();
		$c->add(BatchJobLockPeer::ID, $jobIds, Criteria::IN);
		$stmt = BatchJobLockPeer::doSelectStmt($c);
		$rootIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

		if($idsAreRoot)
		{
			// jobsId are root Ids that we already moved from suspended jobs table
			$rootIds = array_diff($rootIds, $jobIds);
			if(empty($rootIds))
			{
				return array();
			}
		}

		// Select only root ids that has no other suspended descendants
		$c = new Criteria();
		$c->addSelectColumn(BatchJobLockSuspendPeer::ROOT_JOB_ID);
		$c->add(BatchJobLockSuspendPeer::ROOT_JOB_ID, $rootIds, Criteria::IN);
		$c->add(BatchJobLockSuspendPeer::ID, '(batch_job_lock_suspend.ID != batch_job_lock_suspend.ROOT_JOB_ID)', Criteria::CUSTOM);
		$c->addGroupByColumn(BatchJobLockSuspendPeer::ROOT_JOB_ID);
		$c->addHaving($c->getNewCriterion(BatchJobLockSuspendPeer::ROOT_JOB_ID, 'COUNT(batch_job_lock_suspend.ID)>0', Criteria::CUSTOM));
		$stmt = BatchJobLockSuspendPeer::doSelectStmt($c);
		$usedRootIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
		
		$unsuspendedRootJobs = array_diff($rootIds, $usedRootIds);
		self::moveFromSuspendedJobsTable($unsuspendedRootJobs);

		return array_merge(self::unsuspendRootJob($unsuspendedRootJobs, true), $unsuspendedRootJobs);
	}
	
	/**
	 * This function moves a known list of suspended job ids from batch_job_lock table to batch_job_lock_suspended table
	 * and deletes them from the batch_job_lock table
	 * @param array $jobIds The jobs we want to move from batch_job_lock table
	 */
	private static function moveToSuspendedJobsTable($jobIds) {
		
		$suspenderUpdateChunk = self::getSuspenderUpdateChunk();
		$con = Propel::getConnection();
		
		$start = 0;
		$end = sizeof($jobIds);
		while($start < $end) {
			
			// Insert into batch_job_lock_suspended table
			$insertQuery = "INSERT INTO " . BatchJobLockSuspendPeer::TABLE_NAME .
				" (SELECT * FROM " . BatchJobLockPeer::TABLE_NAME .
				" WHERE (" . 
				"(" . BatchJobLockPeer::STATUS . " = " . BatchJob::BATCHJOB_STATUS_SUSPEND . ") OR ".
				"(" . BatchJobLockPeer::STATUS . " = " . BatchJob::BATCHJOB_STATUS_SUSPEND_ALMOST_DONE . ") ". 
				") AND " . BatchJobLockPeer::ID . " IN ( " .  
				implode(",", array_slice($jobIds, $start, min($suspenderUpdateChunk, $end - $start))) . 
				"))";
			
			$con->exec($insertQuery);
			
			// Delete from batch_job_lock table
			$deleteQuery = "DELETE FROM " . BatchJobLockPeer::TABLE_NAME .
				" WHERE " . 
				BatchJobLockPeer::STATUS . " IN (" . BatchJob::BATCHJOB_STATUS_SUSPEND .  ", " . BatchJob::BATCHJOB_STATUS_SUSPEND_ALMOST_DONE . ") ". 
				" AND " . BatchJobLockPeer::ID . " IN ( " . implode(",", array_slice($jobIds, $start, min($suspenderUpdateChunk, $end - $start))) . ")";
				
			$con->exec($deleteQuery);
			$start += $suspenderUpdateChunk;
		}
	}
	
	/**
	 * This function returns a known list of suspended job ids from batch_job_lock_suspended table to batch_job_lock table
	 * and deletes them from the batch_job_lock_suspended table.
	 * @param array $jobIds The jobs we want to move from batch_job_lock_suspended table
	 */
	private static function moveFromSuspendedJobsTable($jobIds) {
	
		$suspenderUpdateChunk = self::getSuspenderUpdateChunk();
		$con = Propel::getConnection();
	
		$start = 0;
		$end = sizeof($jobIds);
		while($start < $end) {
				
			// Move job from batch_job_lock_suspended to batch_job_lock
			$insertQuery = "INSERT INTO " . BatchJobLockPeer::TABLE_NAME .
			" (SELECT * FROM " . BatchJobLockSuspendPeer::TABLE_NAME .
			" WHERE " . BatchJobLockSuspendPeer::ID . " IN ( " .
			implode(",", array_slice($jobIds, $start, min($suspenderUpdateChunk, $end - $start))) .
			"))";
				
			$con->exec($insertQuery);
				
			// Delete from batch_job_lock_suspended table
			$deleteQuery = "DELETE FROM " . BatchJobLockSuspendPeer::TABLE_NAME .
			" WHERE " . BatchJobLockSuspendPeer::ID . " IN ( " .
			implode(",", array_slice($jobIds, $start, min($suspenderUpdateChunk, $end - $start))) .
			")";
	
			$con->exec($deleteQuery);
			$start += $suspenderUpdateChunk;
		}
	}
	
	
	/**
	 * This function returns the 'suspender_update_chunk' from the configuration files.
	 * This value is a number that defines the chunk size in which we will suspend and unsuspend jobs
	 */
	private static function getSuspenderUpdateChunk()
	{
		return kConf::get('suspender_update_chunk');
	}
	
	/**
	 * This function returns the 'suspender_max_pending_jobs' from the configuration files.
	 * This value is a number that defines the value from which we will start suspending jobs
	 */
	private static function getSuspenderMaxPendingJobs()
	{
		return kConf::get('suspender_max_pending_jobs');
	}
	
	/**
	 * This function returns the 'suspender_min_pending_jobs' from the configuration files.
	 * This value is a number that defines the value from which we will start returning pending
	 * jobs to the queue
	 */
	private static function getSuspenderMinPendingJobs()
	{
		return kConf::get('suspender_min_pending_jobs');
	}
}

?>
<?php

/**
 * This class centralizes job suspension utilities
 * 
 * @package Core
 * @subpackage Batch
 *
 */
class kJobsSuspender {
	
	/**
	 * Entry point to job balancing.
	 * - Move jobs from status '0' to status '11' in case there are too many pending jobs
	 * - move jobs back from status '11' to status '0' in case the load is over.
	 * 
	 */
	public static function balanceJobsload() {
	
		$minPendingJobs = self::getSuspenderMinPendingJobs();
		$maxPendingJobs = self::getSuspenderMaxPendingJobs();
		
		$con = Propel::getConnection();
	
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
				
			if($jobCount > $maxPendingJobs)
				self::suspendJobs($con, ($jobCount - $maxPendingJobs), $dcId, $partnerId, $jobType, $jobSubType);
		}
	
		// Unsuspend jobs
		$c = self::createReturnBalancedJobsQuery($dcId);
		$stmt = BatchJobLockPeer::doSelectStmt($c);
		$rows= $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		foreach ($rows as $row) {
			$partnerId = $row['PARTNER_ID'];
			$jobType = $row['JOB_TYPE'];
			$jobSubType = $row['JOB_SUB_TYPE'];
			$key = $partnerId . "#" . $jobType . "#" . $jobSubType;
				
			if(!in_array($key, $loadedKeys))
				self::unsuspendJobs($con, ($maxPendingJobs - $minPendingJobs), $dcId, $partnerId, $jobType, $jobSubType);
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
	 */
	private static function createReturnBalancedJobsQuery($dc) {
		$c = new Criteria();
	
		// Where
		$c->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_DONT_PROCESS, Criteria::EQUAL);
		$c->add(BatchJobLockPeer::DC, $dc, Criteria::EQUAL);
		// Group by
		$c->addGroupByColumn(BatchJobLockPeer::DC);
		$c->addGroupByColumn(BatchJobLockPeer::PARTNER_ID);
		$c->addGroupByColumn(BatchJobLockPeer::JOB_TYPE);
		$c->addGroupByColumn(BatchJobLockPeer::JOB_SUB_TYPE);
		foreach($c->getGroupByColumns() as $column)
			$c->addSelectColumn($column);
		return $c;
	}
	
	/**
	 * This function suspends up to ($limit) jobs of a given ($dc, $partnerId, $jobType, $jobSubType) 
	 */
	private static function suspendJobs($con, $limit, $dc, $partnerId, $jobType, $jobSubType) {
	
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
		$rows= $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		$jobIds = array();
		foreach ($rows as $row)
			$jobIds[] = $row['ID'];
	
		// Suspend chosen ids
		$res = self::setJobsStatus($jobIds, BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		KalturaLog::info("$res jobs of partner ($partnerId) job type ($jobType / $jobSubType) on DC ($dc) were suspended");
	}
	
	/**
	 * This function unsuspends up to ($limit) jobs of a given ($dc, $partnerId, $jobType, $jobSubType)
	 */
	private static function unsuspendJobs($con, $limit, $dc, $partnerId, $jobType, $jobSubType) {
	
		// Find IDs
		$c = new Criteria();
		$c->addSelectColumn(BatchJobLockPeer::ID);
		$c->add( BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_DONT_PROCESS, Criteria::EQUAL);
		$c->add( BatchJobLockPeer::DC, $dc, Criteria::EQUAL);
		$c->add( BatchJobLockPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		$c->add( BatchJobLockPeer::JOB_TYPE, $jobType, Criteria::EQUAL);
		$c->add( BatchJobLockPeer::JOB_SUB_TYPE, $jobSubType, Criteria::EQUAL);
	
		$c->addAscendingOrderByColumn(BatchJobLockPeer::PRIORITY);
		$c->addAscendingOrderByColumn(BatchJobLockPeer::URGENCY);
		$c->addAscendingOrderByColumn(BatchJobLockPeer::ESTIMATED_EFFORT);
		$c->setLimit($limit);
	
		$stmt = BatchJobLockPeer::doSelectStmt($c);
		$rows= $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		$jobIds = array();
		foreach ($rows as $row)
			$jobIds[] = $row['ID'];
	
		// Do update
		$res = self::setJobsStatus($jobIds, BatchJob::BATCHJOB_STATUS_PENDING);
		KalturaLog::info("$res jobs of partner ($partnerId) job type ($jobType / $jobSubType) on DC ($dc) were unsuspended");
	}
	
	/**
	 * This function sets the status of the given $jobIds to the given $status.
	 * We're saved from the kFlowManager::updatedJob callback since we work directly through the DB. 
	 * No callbacks are called.
	 * Returns the number of the affected rows.
	 */
	private static function setJobsStatus($jobIds, $status) {
	
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
			$affectedRows += BasePeer::doUpdate($updateCond, $update, $con);
			$start += $suspenderUpdateChunk;
		}
		return $affectedRows;
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
<?php

require_once(dirname(__FILE__).'/../bootstrap.php');

// constants
$jobType = BatchJobType::CONVERT;
$jobStatus = BatchJob::BATCHJOB_STATUS_PENDING;
define('TEMP_JOB_STATUS', 5000);
define('CHUNK_SIZE', 100);

// auto mode constants
define('MAX_PARTNER_JOB_COUNT', 100);		// partners who have more than this number of jobs will not be moved
define('MIN_JOB_AGE', 300);
define('MAX_JOB_AGE', 604800);				// 7 days
define('AVAIL_DC_MAX_PARTNER_COUNT', 4);				// if there are less than this number of partners matching the criteria, the dc will accept jobs
define('BUSY_DC_MIN_PARTNER_COUNT', 10);			// if there are more than this number of partners matching the criteria, jobs will be pushed out
define('IDLE_DC_MAX_JOB_COUNT', 225);

function getAllReadyInternalFileSyncsForKey(FileSyncKey $key)
{
	$c = new Criteria();
	$c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
	$c->addAnd ( FileSyncPeer::FILE_TYPE , FileSync::FILE_SYNC_FILE_TYPE_FILE);
	$c->addAnd ( FileSyncPeer::STATUS , FileSync::FILE_SYNC_STATUS_READY );
	$results = FileSyncPeer::doSelect( $c );
	
	$assocResults = array();
	foreach ($results as $curResult)
	{
		$assocResults[$curResult->getDc()] = $curResult; 
	}
	return $assocResults;
}

function lockJob($object)
{
	global $jobStatus;
	
	$con = Propel::getConnection();
	
	$lock_version = $object->getVersion() ;
	$criteria_for_exclusive_update = new Criteria();
	$criteria_for_exclusive_update->add(BatchJobLockPeer::ID, $object->getId());
	$criteria_for_exclusive_update->add(BatchJobLockPeer::VERSION, $lock_version);
	$criteria_for_exclusive_update->add(BatchJobLockPeer::STATUS, $jobStatus);
	
	$update = new Criteria();
	
	// increment the lock_version - this will make sure it's exclusive
	$update->add(BatchJobLockPeer::VERSION, $lock_version + 1);
	$update->add(BatchJobLockPeer::STATUS, TEMP_JOB_STATUS);
	
	$affectedRows = BasePeer::doUpdate( $criteria_for_exclusive_update, $update, $con);	
	if ( $affectedRows != 1 )
	{
		return false;
	}
	
	// update $object with what is in the database
	$object->setVersion($lock_version + 1);
	$object->setStatus(TEMP_JOB_STATUS);
	return true;
}

function moveJob(BatchJob $job, BatchJobLock $jobLock, $sourceDc, $targetDc)
{
	global $jobStatus;
	
	// check whether the job can be moved
	$jobData = $job->getData();
	/* @var $jobData kConvartableJobData */
	$srcFileSyncs = $jobData->getSrcFileSyncs();
	if (count($srcFileSyncs) != 1)
	{
		return false;		// unexpected - multiple sources for convert
	}
	$srcFileSync = reset($srcFileSyncs);
	/* @var $srcFileSync kSourceFileSyncDescriptor */
	$sourceAsset = assetPeer::retrieveById($srcFileSync->getAssetId());
	if (!$sourceAsset)
	{
		return false;		// unexpected - source flavor asset not found
	}
	$sourceSyncKey = $sourceAsset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	$sourceFileSyncs = getAllReadyInternalFileSyncsForKey($sourceSyncKey);
	if (!isset($sourceFileSyncs[$sourceDc]) ||
		$sourceFileSyncs[$sourceDc]->getFullPath() != $srcFileSync->getFileSyncLocalPath())
	{
		return false;		// unexpected - no file sync for source dc, or the path does not match the job data
	}
	if (!isset($sourceFileSyncs[$targetDc]))
	{
		return false;		// source file was not synced to target dc yet
	}
	
	// lock the job to prevent any changes to it while it's being moved
	if (!lockJob($jobLock))
	{
		return false;		// failed to lock the job
	}
	
	// update batch job
	$srcFileSync->setPathAndKeyByFileSync($sourceFileSyncs[$targetDc]);
	$srcFileSync->setFileSyncRemoteUrl($sourceFileSyncs[$targetDc]->getExternalUrl($sourceAsset->getEntryId()));
	$jobData->setSrcFileSyncs(array($srcFileSync));
	$job->setData($jobData);
	$job->setDc($targetDc);
	$job->save();
	
	// update batch job lock
	$jobLock->setStatus($jobStatus);
	$jobLock->setDc($targetDc);
	$jobLock->save();
	
	return true;
}

function moveJobs($c, $maxMovedJobs, $sourceDc, $targetDc, $jobType, $jobSubType)
{
	global $jobStatus;
	
	// get candidates for move
	$c->add(BatchJobLockPeer::DC, $sourceDc);
	$c->add(BatchJobLockPeer::JOB_TYPE, $jobType);
	if (!is_null($jobSubType))
	{
		$c->add(BatchJobLockPeer::JOB_SUB_TYPE, $jobSubType);
	}
	
	// not locked
	$c->add(BatchJobLockPeer::SCHEDULER_ID, null, Criteria::ISNULL);
	$c->add(BatchJobLockPeer::WORKER_ID, null, Criteria::ISNULL);
	$c->add(BatchJobLockPeer::BATCH_INDEX, null, Criteria::ISNULL);
	$c->add(BatchJobLockPeer::STATUS, $jobStatus);
	
	$c->setLimit(CHUNK_SIZE);

	$movedJobsCount = 0;
	while ($movedJobsCount < $maxMovedJobs)
	{
		$jobLocks = BatchJobLockPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		if (!$jobLocks)
		{
			break;
		}

		$initialMovedJobsCount = $movedJobsCount;
		foreach ($jobLocks as $jobLock)
		{
			$job = $jobLock->getBatchJob(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
			if (!$job)
			{
				continue;
			}
				
			if (!moveJob($job, $jobLock, $sourceDc, $targetDc))
			{
				continue;
			}
				
			KalturaLog::log('Moved job '.$job->getId()." PartnerId ".$job->getPartnerId()." EntryId ".$job->getEntryId()." FlavorId ".$job->getObjectId()."\n");
			$movedJobsCount++;
			if ($movedJobsCount >= $maxMovedJobs)
				break;
		}

		if ($movedJobsCount - $initialMovedJobsCount < CHUNK_SIZE / 2)		// most of the page could not be moved, continue to the next page
		{
			$c->setOffset($c->getOffset() + CHUNK_SIZE / 2);
		}
		kMemoryManager::clearMemory();
	}
	
	return $movedJobsCount;
}

function getRunningJobsCount($jobType, $jobSubType)
{
	/*
	 * SELECT DC, COUNT(ID) FROM `batch_job_lock` WHERE 
	 * 		STATUS='1' AND 
	 * 		DC IN ('0','1') AND 
	 * 		JOB_TYPE='0' AND JOB_SUB_TYPE='2' AND 
	 * 		(CREATED_AT>='2016-02-19 05:24:43' AND CREATED_AT<='2016-02-26 05:19:43') 
	 * 		GROUP BY DC
	 */
	$c = new Criteria();
	
	// running
	$c->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_QUEUED);
	// job type + sub type
	$c->add(BatchJobLockPeer::DC, kDataCenterMgr::getDcIds(), Criteria::IN);
	$c->add(BatchJobLockPeer::JOB_TYPE, $jobType);
	if (!is_null($jobSubType))
	{
		$c->add(BatchJobLockPeer::JOB_SUB_TYPE, $jobSubType);
	}
	// not too new / too old
	$createdAtCriterion = $c->getNewCriterion(BatchJobLockPeer::CREATED_AT, time() - MAX_JOB_AGE, Criteria::GREATER_EQUAL);
	$createdAtCriterion->addAnd($c->getNewCriterion(BatchJobLockPeer::CREATED_AT, time() - MIN_JOB_AGE, Criteria::LESS_EQUAL));
	$c->addAnd($createdAtCriterion);
	// group by dc + partner
	$c->addGroupByColumn(BatchJobLockPeer::DC);
	// select count, partner, dc
	$c->addSelectColumn(BatchJobLockPeer::COUNT);
	$c->addSelectColumn(BatchJobLockPeer::DC);
	
	$stmt = BatchJobLockPeer::doSelectStmt($c);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$countByDc = array();
	foreach (kDataCenterMgr::getDcIds() as $dc)
	{
		$countByDc[$dc] = 0;
	}
	
	foreach ($rows as $row)
	{
		$dc = $row['DC'];
		$count = $row[BatchJobLockPeer::COUNT];
		$countByDc[$dc] = $count;
	}
	
	return $countByDc;
}

function getPendingJobsCount($jobType, $jobSubType, $maxJobsPerPartner)
{
	/* 
	* SELECT DC, PARTNER_ID, COUNT(1) FROM `batch_job_lock` WHERE
	* 		SCHEDULER_ID IS NULL AND WORKER_ID IS NULL AND BATCH_INDEX IS NULL AND STATUS = '0' AND
	* 		DC IN ('0', '1') AND
	* 		JOB_TYPE = '0' AND JOB_SUB_TYPE = '2' AND
	* 		(CREATED_AT >= '2016-02-17 04:23:42' AND CREATED_AT <= '2016-02-24 04:18:42')
	* 		GROUP BY DC, PARTNER_ID
	* 		HAVING COUNT(1) < 50
	*/
	
	$c = new Criteria();
	
	// not locked
	$c->add(BatchJobLockPeer::SCHEDULER_ID, null, Criteria::ISNULL);
	$c->add(BatchJobLockPeer::WORKER_ID, null, Criteria::ISNULL);
	$c->add(BatchJobLockPeer::BATCH_INDEX, null, Criteria::ISNULL);
	$c->add(BatchJobLockPeer::STATUS, BatchJob::BATCHJOB_STATUS_PENDING);
	// job type + sub type
	$c->add(BatchJobLockPeer::DC, kDataCenterMgr::getDcIds(), Criteria::IN);
	$c->add(BatchJobLockPeer::JOB_TYPE, $jobType);
	if (!is_null($jobSubType))
	{
		$c->add(BatchJobLockPeer::JOB_SUB_TYPE, $jobSubType);
	}
	// not too new / too old
	$createdAtCriterion = $c->getNewCriterion(BatchJobLockPeer::CREATED_AT, time() - MAX_JOB_AGE, Criteria::GREATER_EQUAL);
	$createdAtCriterion->addAnd($c->getNewCriterion(BatchJobLockPeer::CREATED_AT, time() - MIN_JOB_AGE, Criteria::LESS_EQUAL));
	$c->addAnd($createdAtCriterion);
	// group by dc + partner
	$c->addGroupByColumn(BatchJobLockPeer::DC);
	$c->addGroupByColumn(BatchJobLockPeer::PARTNER_ID);
	
	if ($maxJobsPerPartner)
	{
		// not having too many jobs
		$c->addHaving($c->getNewCriterion(BatchJobLockPeer::ID, BatchJobLockPeer::COUNT . '<' . $maxJobsPerPartner, Criteria::CUSTOM));
	}
	// select count, partner, dc
	$c->addSelectColumn(BatchJobLockPeer::COUNT);
	foreach($c->getGroupByColumns() as $column)
	{
		$c->addSelectColumn($column);
	}
	
	$stmt = BatchJobLockPeer::doSelectStmt($c);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	// build a map of dc, partner => job count
	$countByDcPartner = array();
	foreach (kDataCenterMgr::getDcIds() as $dc)
	{
		$countByDcPartner[$dc] = array();
	}
	
	foreach ($rows as $row)
	{
		$dc = $row['DC'];
		$partnerId = $row['PARTNER_ID'];
		$count = $row[BatchJobLockPeer::COUNT];
		$countByDcPartner[$dc][$partnerId] = $count;
	}
	
	return $countByDcPartner;
}

function autoMoveJobs($jobType, $jobSubType)
{
	/*
	 * Automatic balancing logic
	 * 
	 * The balancing logic is meant to optimize two scenarios:
	 * 1. One of the DCs if idle (=the number of running jobs is significantly lower 
	 * 		than the number of workers) - In this case, we want to balance all the jobs
	 * 		without any limitation so that the queue will be processed as fast as 
	 * 		possible. Since the situation can change, and the remote DC may start
	 * 		accumulating more jobs, we do not move any jobs that would raise the number
	 * 		of partner jobs above 100 in the target DC.
	 * 2. One DC is loaded (=many different partners waiting in queue) while the other
	 * 		DC is not (=few partners waiting in queue). In this case, we balance only
	 * 		the jobs of partners that don't have more than X jobs. If some partner
	 * 		has 1K jobs in queue, they are not likely to complete soon anyway, so it's
	 * 		better not to move its jobs and keep the remote DC focused on other partners.
	 */
	
	// get running jobs count and look for idle dcs
	$idleDcs = array();
	$runningJobs = getRunningJobsCount($jobType, $jobSubType);
	foreach ($runningJobs as $dc => $count)
	{
		if ($dc != kDataCenterMgr::getCurrentDcId() && $count < IDLE_DC_MAX_JOB_COUNT)
		{
			$idleDcs[] = $dc;
		}
	}

	KalturaLog::log('running jobs status '.print_r($runningJobs, true));
	KalturaLog::log('idle dcs ' . implode(',', $idleDcs));
	
	// get the pending jobs count
	$countByDcPartner = getPendingJobsCount(
			$jobType, $jobSubType, !$idleDcs ? MAX_PARTNER_JOB_COUNT : 0);
	
	KalturaLog::log('pending jobs status '.print_r($countByDcPartner, true));
	
	// Note: only move jobs away from current DC - can't safely lock a job belonging
	//		to another DC - a worker may lock the job at the same time on the master DB
	//		of the remote DC. the lock is atomic only when working with a single master
	$sourceDc = kDataCenterMgr::getCurrentDcId();

	// find a target DC to push the jobs to
	if ($idleDcs)
	{
		$targetDc = reset($idleDcs);
	}
	else
	{
		if (count($countByDcPartner[$sourceDc]) < BUSY_DC_MIN_PARTNER_COUNT)
		{
			KalturaLog::log('current dc has only '.count($countByDcPartner[$sourceDc]).' partners waiting');
			return 0;
		}
		
		$availDcs = array();
		foreach ($countByDcPartner as $dc => $countByPartner)
		{
			if (count($countByPartner) < AVAIL_DC_MAX_PARTNER_COUNT)
			{
				$availDcs[] = $dc;
			}
		}
		
		if (!$availDcs)
		{
			KalturaLog::log('no available dcs to push jobs to');
			return 0;
		}
		
		$targetDc = reset($availDcs);
	}
	
	// push the jobs
	$movedJobsCount = 0;
	foreach ($countByDcPartner[$sourceDc] as $partnerId => $srcCount)
	{
		$targetCount = isset($countByDcPartner[$targetDc][$partnerId]) ?
			$countByDcPartner[$targetDc][$partnerId] : 0;
		
		if ($targetCount >= $srcCount || $targetCount >= MAX_PARTNER_JOB_COUNT)
		{
			continue;
		}
		
		$maxMovedJobs = min(
				floor(($srcCount - $targetCount) / 2), 
				MAX_PARTNER_JOB_COUNT - $targetCount);
		
		$c = new Criteria();
		// partner
		$c->add(BatchJobLockPeer::PARTNER_ID, $partnerId);
		
		// not too new / too old
		$createdAtCriterion = $c->getNewCriterion(BatchJobLockPeer::CREATED_AT, time() - MAX_JOB_AGE, Criteria::GREATER_EQUAL);
		$createdAtCriterion->addAnd($c->getNewCriterion(BatchJobLockPeer::CREATED_AT, time() - MIN_JOB_AGE, Criteria::LESS_EQUAL));
		$c->addAnd($createdAtCriterion);
		
		KalturaLog::log("moving jobs: partnerId=$partnerId max=$maxMovedJobs, source=$sourceDc, target=$targetDc, type=$jobType, subType=$jobSubType");
	
		$movedJobsCount += moveJobs($c, $maxMovedJobs, $sourceDc, $targetDc, $jobType, $jobSubType);
	}
	
	return $movedJobsCount;
}

// parse command line
if ($argc < 3 || 
	!in_array($argv[1], array('auto', 'manual')) || 
	($argv[1] == 'manual' && $argc < 5))
{
	echo "Usage:\n";
	echo "\t" . basename(__FILE__) . " manual <max number of jobs to move> <source dc> <target dc> [<job sub type> [<partner id>]]\n";
	echo "\t" . basename(__FILE__) . " auto <job sub type>\n";
	die;
}

if ($argv[1] == 'manual')
{
	$maxMovedJobs = $argv[2];
	$sourceDc = $argv[3];
	$targetDc = $argv[4];
	
	$jobSubType = null;
	if ($argc > 5)
	{
		$jobSubType = $argv[5];
	}
	
	$partnerId = null;
	if ($argc > 6)
	{
		$partnerId = $argv[6];
	}
	
	$c = new Criteria();
	if (!is_null($partnerId))
	{
		if ($partnerId[0] == '!')
		{
			$c->add(BatchJobLockPeer::PARTNER_ID, explode(',', substr($partnerId, 1)), Criteria::NOT_IN);
		}
		else
		{
			$c->add(BatchJobLockPeer::PARTNER_ID, explode(',', $partnerId), Criteria::IN);
		}
	}

	KalturaLog::log("moving jobs: partnerId=$partnerId max=$maxMovedJobs, source=$sourceDc, target=$targetDc, type=$jobType, subType=$jobSubType");
	
	$movedJobsCount = moveJobs($c, $maxMovedJobs, $sourceDc, $targetDc, $jobType, $jobSubType);

	KalturaLog::log("Moved {$movedJobsCount} jobs");
}
else
{
	$jobSubTypes = $argv[2];
	foreach (explode(',', $jobSubTypes) as $jobSubType)
	{
		$movedJobsCount = autoMoveJobs($jobType, $jobSubType);
		KalturaLog::log("Moved jobs, subtype=$jobSubType count={$movedJobsCount}");
	}
}

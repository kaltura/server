<?php

require_once(dirname(__FILE__).'/../bootstrap.php');

if ($argc < 4)
	die("Usage: " . basename(__FILE__) . " <max number of jobs to move> <source dc> <target dc> [<job sub type> [<partner id>]]\n");

// input parameters
$maxMovedJobs = $argv[1];
$sourceDc = $argv[2];
$targetDc = $argv[3];

$jobSubType = null;
if ($argc > 4)
	$jobSubType = $argv[4];

$partnerId = null;
if ($argc > 5)
	$partnerId = $argv[5];

// constants
$jobType = BatchJobType::CONVERT;
$jobStatus = BatchJob::BATCHJOB_STATUS_PENDING;
define('TEMP_JOB_STATUS', 5000);
define('CHUNK_SIZE', 100);

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
		return false;
	
	// update $object with what is in the database
	$object->setVersion($lock_version + 1);
	$object->setStatus(TEMP_JOB_STATUS);
	return true;
}

// get candidates for move
$c = new Criteria();
$c->add(BatchJobLockPeer::DC, $sourceDc);
$c->add(BatchJobLockPeer::SCHEDULER_ID, null, Criteria::ISNULL);
$c->add(BatchJobLockPeer::WORKER_ID, null, Criteria::ISNULL);
$c->add(BatchJobLockPeer::BATCH_INDEX, null, Criteria::ISNULL);
$c->add(BatchJobLockPeer::STATUS, $jobStatus);
$c->add(BatchJobLockPeer::JOB_TYPE, $jobType);
if (!is_null($jobSubType))
	$c->add(BatchJobLockPeer::JOB_SUB_TYPE, $jobSubType);
if (!is_null($partnerId))
	if ($partnerId[0] == '!')
		$c->add(BatchJobLockPeer::PARTNER_ID, substr($partnerId, 1), Criteria::NOT_EQUAL);
	else
		$c->add(BatchJobLockPeer::PARTNER_ID, $partnerId);
$c->setLimit(CHUNK_SIZE);

$movedJobsCount = 0;
while ($movedJobsCount < $maxMovedJobs)
{
	$jobLocks = BatchJobLockPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
	if (!$jobLocks)
		break;
	
	$initialMovedJobsCount = $movedJobsCount;
	foreach ($jobLocks as $jobLock)
	{
		/* @var $jobLock BatchJobLock */
		/* @var $job BatchJob */
		$job = $jobLock->getBatchJob(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		
		// check whether the job can be moved
		$jobData = $job->getData();
		/* @var $jobData kConvartableJobData */
		$srcFileSyncs = $jobData->getSrcFileSyncs();
		if (count($srcFileSyncs) != 1)
			continue;		// unexpected - multiple sources for doc convert
		$srcFileSync = reset($srcFileSyncs);
		/* @var $srcFileSync kSourceFileSyncDescriptor */
		$sourceAsset = assetPeer::retrieveById($srcFileSync->getAssetId());
		if (!$sourceAsset)
			continue;		// unexpected - source flavor asset not found
		$sourceSyncKey = $sourceAsset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$sourceFileSyncs = getAllReadyInternalFileSyncsForKey($sourceSyncKey);
		if (!isset($sourceFileSyncs[$sourceDc]) || 
			$sourceFileSyncs[$sourceDc]->getFullPath() != $srcFileSync->getFileSyncLocalPath())
			continue;		// unexpected - no file sync for source dc, or the path does not match the job data
		if (!isset($sourceFileSyncs[$targetDc]))
			continue;		// source file was not synced to target dc yet
		
		// lock the job to prevent any changes to it while it's being moved
		if (!lockJob($jobLock))
			continue;		// failed to lock the job
		
		// update batch job
		$srcFileSync->setFileSyncLocalPath($sourceFileSyncs[$targetDc]->getFullPath());
		$srcFileSync->setFileSyncRemoteUrl($sourceFileSyncs[$targetDc]->getExternalUrl($sourceAsset->getEntryId()));
		$jobData->setSrcFileSyncs(array($srcFileSync));
		$job->setData($jobData);
		$job->setDc($targetDc);
		$job->save();
		
		// update batch job lock
		$jobLock->setStatus($jobStatus);
		$jobLock->setDc($targetDc);
		$jobLock->save();
		
		echo 'Moved job '.$job->getId()." PartnerId ".$job->getPartnerId()." EntryId ".$job->getEntryId()." FlavorId ".$job->getObjectId()."\n";
		$movedJobsCount++;
		if ($movedJobsCount >= $maxMovedJobs)
			break;
	}
	
	if ($movedJobsCount - $initialMovedJobsCount < CHUNK_SIZE / 2)		// most of the page could not be moved, continue to the next page
		$c->setOffset($c->getOffset() + CHUNK_SIZE / 2);
	kMemoryManager::clearMemory();
}

echo "Moved {$movedJobsCount} jobs\n";

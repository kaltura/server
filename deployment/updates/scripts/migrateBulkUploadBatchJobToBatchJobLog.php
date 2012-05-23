<?php
/**
 * Call flavor assete setters to migrate from old columns to new custom data fields.
 * After all flavors will be migrated we can remove the columns from the db.
 *
 * @package Deployment
 * @subpackage updates
 */ 

$countLimitEachLoop = 500;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
	
$c = new Criteria();

if (isset($argv[1]))
{
    $c->addAnd(BatchJobPeer::ID, $argv[1], Criteria::GREATER_EQUAL);
}
if (isset($argv[2]))
{
    $c->addAnd(BatchJobPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
}

$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD, Criteria::EQUAL);
$c->setLimit($countLimitEachLoop);
$batchJobResults = BatchJobPeer::doSelect($c, $con);

while($batchJobResults && count($batchJobResults))
{
	foreach($batchJobResults as $batchJob)
	{
		/* @var $batchJob BatchJob */
	    $batchJobLog = new BatchJobLog();
		
	    $batchJobLog->setAbort($batchJob->getAbort());
	    $batchJobLog->setBatchIndex($batchJob->getBatchIndex());
	    $batchJobLog->setBulkJobId($batchJob->getBulkJobId());
	    $batchJobLog->setCheckAgainTimeout($batchJob->getCheckAgainTimeout());
	    $batchJobLog->setCreatedAt($batchJob->getCreatedAt());
	    $batchJobLog->setCreatedBy($batchJob->getCreatedBy());
	    $batchJobLog->setData($batchJob->getData());
	    $batchJobLog->setDc($batchJob->getDc());
	    $batchJobLog->setDeletedAt($batchJob->getDeletedAt());
	    $batchJobLog->setDescription($batchJob->getDescription());
	    $batchJobLog->setDuplicationKey($batchJob->getDuplicationKey());
	    $batchJobLog->setEntryId($batchJob->getEntryId());
	    $batchJobLog->setErrNumber($batchJob->getErrNumber());
	    $batchJobLog->setErrType($batchJob->getErrType());
	    $batchJobLog->setExecutionAttempts($batchJob->getExecutionAttempts());
	    $batchJobLog->setFileSize($batchJob->getFileSize());
	    $batchJobLog->setFinishTime($batchJob->getFinishTime());
	    $batchJobLog->setJobId($batchJob->getId());
	    $batchJobLog->setJobSubType($batchJob->getJobSubType());
	    $batchJobLog->setJobType($batchJob->getJobType());
	    $batchJobLog->setLastSchedulerId($batchJob->getLastSchedulerId());
	    $batchJobLog->setLastWorkerId($batchJob->getLastWorkerId());
	    $batchJobLog->setLastWorkerRemote($batchJob->getLastWorkerRemote());
	    $batchJobLog->setLockVersion($batchJob->getLockVersion());
	    $batchJobLog->setMessage($batchJob->getMessage());
	    $batchJobLog->setOnStressDivertTo($batchJob->getOnStressDivertTo());
	    $batchJobLog->setParentJobId($batchJob->getParentJobId());
	    $batchJobLog->setPartnerId($batchJob->getPartnerId());
	    $batchJobLog->setPrimaryKey($batchJob->getPrimaryKey());
	    $batchJobLog->setPriority($batchJob->getPriority());
	    $batchJobLog->setProcessorExpiration($batchJob->getProcessorExpiration());
	    $batchJobLog->setProgress($batchJob->getProgress());
	    $batchJobLog->setQueueTime($batchJob->getQueueTime());
	    $batchJobLog->setRootJobId($batchJob->getRootJobId());
	    $batchJobLog->setSchedulerId($batchJob->getSchedulerId());
	    $batchJobLog->setJobStatus($batchJob->getStatus());
	    $batchJobLog->setSubpId($batchJob->getSubpId());
	    $batchJobLog->setTwinJobId($batchJob->getTwinJobId());
	    $batchJobLog->setUpdatedAt($batchJob->getUpdatedAt());
	    $batchJobLog->setUpdatedBy($batchJob->getUpdatedBy());
	    $batchJobLog->setUpdatesCount($batchJob->getUpdatesCount());
	    $batchJobLog->setWorkerId($batchJob->getWorkerId());
	    $batchJobLog->setWorkGroupId($batchJob->getWorkGroupId());
	    //set param_1 for the $batchJobLog
	    $batchJobData = $batchJob->getData();
	    /* @var $batchJobData kBulkUploadJobData */
	    $batchJobLog->setParam1($batchJobData->getBulkUploadObjectType());
	    
		$batchJobLog->save();
		
		var_dump("Last handled id: ".$batchJob->getId());
		
	}
	$countLimitEachLoop += $countLimitEachLoop;
	$c->setOffset($countLimitEachLoop);
	$batchJobResults = BatchJobPeer::doSelect($c, $con);
	usleep(100);
}

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

if (isset($argv[1]) && $argv[1])
{
    $c->addAnd(BatchJobPeer::ID, $argv[1], Criteria::GREATER_EQUAL);
}
else if (isset($argv[2]) && $argv[2])
{
    $c->addAnd(BatchJobPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
}

else if (isset($argv[3]) && $argv[3])
{
    $c->addAnd(BatchJobPeer::UPDATED_AT, $argv[3], Criteria::GREATER_EQUAL);
}

$c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::BULKUPLOAD, Criteria::EQUAL);
$c->setLimit($countLimitEachLoop);
$c->addAscendingOrderByColumn(BatchJobPeer::UPDATED_AT);
$batchJobResults = BatchJobPeer::doSelect($c, $con);

while($batchJobResults && count($batchJobResults))
{
	foreach($batchJobResults as $batchJob)
	{
		/* @var $batchJob BatchJob */
	    $batchJobLog = new BatchJobLog();
		
	    $batchJob->copyInto($batchJobLog, true);
	    $batchJobLog->setJobId($batchJob->getId());
	    //migrate jobData without unnecessary serialization
	    $batchJobLog->setData($batchJob->getData(true), true);
	    $batchJobData = $batchJob->getData();
	    //set param_1 for the $batchJobLog
	    $batchJobData = $batchJob->getData();
	    /* @var $batchJobData kBulkUploadJobData */
	    $batchJobLog->setParam1($batchJobData->getBulkUploadObjectType() ? $batchJobData->getBulkUploadObjectType() : 1);
	    
		$batchJobLog->save();
		
		var_dump("Last handled id: ".$batchJob->getId());
		
	}
	$countLimitEachLoop += $countLimitEachLoop;
	$c->setOffset($countLimitEachLoop);
	$batchJobResults = BatchJobPeer::doSelect($c, $con);
	usleep(100);
}

<?php
require_once("tests/bootstrapTests.php");

class BatchCleanupTests extends PHPUnit_Framework_TestCase 
{
	public function testCleanExclusiveJobs()
	{
		$batchJobService = KalturaTestsHelpers::getServiceInitializedForAction("batch", "cleanExclusiveJobs", Partner::BATCH_PARTNER_ID, null, BatchTestsHelpers::getBatchAdminKs());
		
		$ret = $batchJobService->cleanExclusiveJobsAction();
		echo $ret;
	}
}

?>
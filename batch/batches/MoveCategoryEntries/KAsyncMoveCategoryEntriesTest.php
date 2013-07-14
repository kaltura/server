<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */
chdir(dirname( __FILE__ ) . "/../../");
require_once(__DIR__ . "/../../bootstrap.php");

/**
 * @package Scheduler
 * @subpackage Debug
 */
class KAsyncMoveCategoryEntriesTest extends PHPUnit_Framework_TestCase
{
	const JOB_NAME = 'KAsyncMoveCategoryEntries';

	public function testCopyAll()
	{
		$partnerId = 101;
		$srcCategoryId = 1;
		$destCategoryId = 2;
		$moveFromChildren = true;
		$copyOnly = true;
		$this->doTest($partnerId, $srcCategoryId, $destCategoryId, $moveFromChildren, $copyOnly, KalturaBatchJobStatus::FINISHED);
	}
	
	public function testCopyCurrent()
	{
		$partnerId = 101;
		$srcCategoryId = 1;
		$destCategoryId = 2;
		$moveFromChildren = false;
		$copyOnly = true;
		$this->doTest($partnerId, $srcCategoryId, $destCategoryId, $moveFromChildren, $copyOnly, KalturaBatchJobStatus::FINISHED);
	}
	
	public function testMoveAll()
	{
		$partnerId = 101;
		$srcCategoryId = 1;
		$destCategoryId = 2;
		$moveFromChildren = true;
		$copyOnly = false;
		$this->doTest($partnerId, $srcCategoryId, $destCategoryId, $moveFromChildren, $copyOnly, KalturaBatchJobStatus::FINISHED);
	}
	
	public function testMoveCurrent()
	{
		$partnerId = 101;
		$srcCategoryId = 1;
		$destCategoryId = 2;
		$moveFromChildren = false;
		$copyOnly = false;
		$this->doTest($partnerId, $srcCategoryId, $destCategoryId, $moveFromChildren, $copyOnly, KalturaBatchJobStatus::FINISHED);
	}
	
	public function doTest($partnerId, $srcCategoryId, $destCategoryId, $moveFromChildren, $copyOnly, $expectedStatus)
	{
		$iniFile = "batch_config.ini";
		$schedulerConfig = new KSchedulerConfig($iniFile);
	
		$taskConfigs = $schedulerConfig->getTaskConfigList();
		$config = null;
		foreach($taskConfigs as $taskConfig)
		{
			if($taskConfig->name == self::JOB_NAME)
				$config = $taskConfig;
		}
		$this->assertNotNull($config);
		
		$jobs = $this->prepareJobs($partnerId, $srcCategoryId, $destCategoryId, $moveFromChildren, $copyOnly);
		
		$config->setTaskIndex(1);
		$instance = new $config->type($config);
		$instance->setUnitTest(true);
		$jobs = $instance->run($jobs); 
		$instance->done();
		
		foreach($jobs as $job)
			$this->assertEquals($expectedStatus, $job->status);
	}
	
	private function prepareJobs($partnerId, $srcCategoryId, $destCategoryId, $moveFromChildren, $copyOnly)
	{
		$data = new KalturaMoveCategoryEntriesJobData();
		$data->srcCategoryId = $srcCategoryId;
		$data->destCategoryId = $destCategoryId;
		$data->moveFromChildren = $moveFromChildren;
		$data->copyOnly = $copyOnly;
		
		$job = new KalturaBatchJob();
		$job->id = 1;
		$job->partnerId = $partnerId;
		$job->status = KalturaBatchJobStatus::PENDING;
		$job->data = $data;
		
		return array($job);
	}
}

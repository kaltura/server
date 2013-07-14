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
class KAsyncIndexTest extends PHPUnit_Framework_TestCase
{
	const JOB_NAME = 'KAsyncIndex';
	
	public function testMediaEntryFilter()
	{
		$filter = new KalturaMediaEntryFilter();
		// TODO define the filter
		
		$this->doTestEntry($filter, KalturaBatchJobStatus::FINISHED);
	}

	public function testDocumentEntryFilter()
	{
		$filter = new KalturaDocumentEntryFilter();
		// TODO define the filter
		
		$this->doTestEntry($filter, KalturaBatchJobStatus::FINISHED);
	}
	
	public function doTestEntry(KalturaBaseEntryFilter $filter, $expectedStatus)
	{
		$this->doTest(KalturaIndexObjectType::ENTRY, $filter, $expectedStatus);
	}
	
	public function doTest($objectType, KalturaFilter $filter, $expectedStatus)
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
		
		$jobs = $this->prepareJobs($objectType, $filter);
		
		$config->setTaskIndex(1);
		$instance = new $config->type($config);
		$instance->setUnitTest(true);
		$jobs = $instance->run($jobs); 
		$instance->done();
		
		foreach($jobs as $job)
			$this->assertEquals($expectedStatus, $job->status);
	}
	
	private function prepareJobs($objectType, KalturaFilter $filter)
	{
		$data = new KalturaIndexJobData();
		$data->filter = $filter;
		
		$job = new KalturaBatchJob();
		$job->id = 1;
		$job->jobSubType = $objectType;
		$job->status = KalturaBatchJobStatus::PENDING;
		$job->data = $data;
		
		return array($job);
	}
}

?>
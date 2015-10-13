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
class KAsyncCopyPartnerTest extends PHPUnit_Framework_TestCase
{
	const JOB_NAME = 'KAsyncCopyPartner';

	public function testCopyPartner()
	{
		$iniFile = realpath(__DIR__ . "/../../../configurations/batch" );
		$schedulerConfig = new KSchedulerConfig($iniFile);
	
		$taskConfigs = $schedulerConfig->getTaskConfigList();
		$config = null;
		
		foreach($taskConfigs as $taskConfig)	
		{
			if($taskConfig->name == self::JOB_NAME)
				$config = $taskConfig;
		}
		
		$this->assertNotNull($config);
		
		$jobs = $this->prepareJobs();
		
		$config->setTaskIndex(1);
		$instance = new $config->type($config);
		$instance->setUnitTest(true);

		echo "Starting to run...\n";
		$jobs = $instance->run($jobs);
		echo "Done running...\n";
		$instance->done();
		
		foreach($jobs as $job)
		{
			echo "Asserting job status is FINISHED...\n";				
			$this->assertEquals(KalturaBatchJobStatus::FINISHED, $job->status);
		}
	}

	
	private function prepareJobs()
	{
		$data = new KalturaCopyPartnerJobData();
		$data->fromPartnerId = 101;
		$data->toPartnerId = 104;
		
		$job = new KalturaBatchJob();
		$job->id = 1;
		$job->status = KalturaBatchJobStatus::PENDING;
		$job->data = $data;
		
		return array($job);
	}
}

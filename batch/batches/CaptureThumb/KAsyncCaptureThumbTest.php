<?php
chdir(dirname( __FILE__ ) . "/../../");
require_once("bootstrap.php");

/**
 * @package Scheduler
 * @subpackage Debug
 */
class KAsyncCaptureThumbTest extends PHPUnit_Framework_TestCase 
{
	const JOB_NAME = 'KAsyncCaptureThumb';
	
	public function setUp() 
	{
		parent::setUp();
	}
	
	public function tearDown() 
	{
		parent::tearDown();
	}
	
	public function testGoodFile()
	{
		$thumbParamsOutput = new KalturaThumbParamsOutput();
		$thumbParamsOutput->videoOffset = 6;
		$this->doTest('C:\web\content\entry\data\0\0\0_p2uga3jg_0_eol5gd3x_1.flv', $thumbParamsOutput, KalturaBatchJobStatus::FINISHED);
	}
	
	public function testMissingFile()
	{
		$thumbParamsOutput = new KalturaThumbParamsOutput();
		$this->doTest('aaa', $thumbParamsOutput, KalturaBatchJobStatus::RETRY);
	}
	
	public function doTest($filePath, KalturaThumbParamsOutput $thumbParamsOutput, $expectedStatus)
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
		
		$jobs = $this->prepareJobs($filePath, $thumbParamsOutput);
		
		$config->setTaskIndex(1);
		$instance = new $config->type($config);
		$instance->setUnitTest(true);
		$jobs = $instance->run($jobs); 
		$instance->done();
		
		foreach($jobs as $job)
			$this->assertEquals($expectedStatus, $job->status);
	}
	
	private function prepareJobs($filePath, KalturaThumbParamsOutput $thumbParamsOutput)
	{
		$data = new KalturaCaptureThumbJobData();
		$data->srcFileSyncLocalPath = $filePath;
		$data->thumbParamsOutput = $thumbParamsOutput;
		
		$job = new KalturaBatchJob();
		$job->id = 1;
		$job->status = KalturaBatchJobStatus::PENDING;
		$job->data = $data;
		
		return array($job);
	}
}


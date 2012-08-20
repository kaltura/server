<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */
chdir(dirname( __FILE__ ) . "/../../");
require_once(dirname( __FILE__ ) . "/../../bootstrap.php");

/**
 * @package Scheduler
 * @subpackage Debug
 */
class KAsyncConvertCollectionCloserTest extends PHPUnit_Framework_TestCase 
{
	const JOB_NAME = 'KAsyncConvertCollectionCloser';
	
	public function setUp() 
	{
		parent::setUp();
	}
	
	public function tearDown() 
	{
		parent::tearDown();
	}
	
	public function testEncodingCom()
	{
		$engineType = KalturaConversionEngineType::ENCODING_COM;
		$remoteMediaId = '845877';
		$this->doTest($engineType, $remoteMediaId, '', KalturaBatchJobStatus::FINISHED);
	}
	
	private function doTest($engineType, $remoteMediaId, $remoteUrl, $expectedStatus)
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
		
		$jobs = $this->prepareJobs($engineType, $remoteMediaId, $remoteUrl);
		
		$config->setTaskIndex(1);
		$instance = new $config->type($config);
		$instance->setUnitTest(true);
		$jobs = $instance->run($jobs); 
		$instance->done();
		
		foreach($jobs as $job)
			$this->assertEquals($expectedStatus, $job->status);
	}
	
	private function prepareJobs($engineType, $remoteMediaId, $remoteUrl)
	{
		$data = new KalturaConvertJobData();
		$data->remoteMediaId = $remoteMediaId;
		$data->destFileSyncRemoteUrl = $remoteUrl;
		
		$job = new KalturaBatchJob();
		$job->id = 1;
		$job->jobSubType = $engineType;
		$job->status = KalturaBatchJobStatus::ALMOST_DONE;
		$job->data = $data;
		$job->queueTime = time();
		
		return array($job);
	}
}

?>
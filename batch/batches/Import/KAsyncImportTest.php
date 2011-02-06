<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */
chdir(dirname( __FILE__ ) . "/../../");
require_once("bootstrap.php");

/**
 * @package Scheduler
 * @subpackage Debug
 */
class KAsyncImportTest extends PHPUnit_Framework_TestCase
{
	const JOB_NAME = 'KAsyncImport';
	
	public function setUp() 
	{
		parent::setUp();
	}
	
	public function tearDown() 
	{
		parent::tearDown();
	}
	
	public function testGoodUrl()
	{
		$this->doTest('http://kaldev.kaltura.com/content/zbale/9spkxiz8m4_100007.mp4', KalturaBatchJobStatus::FINISHED);
	}
	
//	public function testSpecialCharsUrl()
//	{
//		$this->doTest('http://kaldev.kaltura.com/content/zbale/trailer_480 ()p.mov', KalturaBatchJobStatus::FINISHED);
//	}
//	
//	public function testSpacedUrl()
//	{
//		$this->doTest(' http://kaldev.kaltura.com/content/zbale/9spkxiz8m4_100007.mp4', KalturaBatchJobStatus::FINISHED);
//	}
//	
//	public function testMissingFileUrl()
//	{
//		$this->doTest('http://localhost/api_v3/sample/xxx.avi', KalturaBatchJobStatus::FAILED);
//	}
//	
//	public function testInvalidServerUrl()
//	{
//		$this->doTest('http://xxx', KalturaBatchJobStatus::FAILED);
//	}
//	
//	public function testInvalidUrl()
//	{
//		$this->doTest('xxx', KalturaBatchJobStatus::FAILED);
//	}
//	
//	public function testEmptyUrl()
//	{
//		$this->doTest('', KalturaBatchJobStatus::FAILED);
//	}
	
	public function doTest($value, $expectedStatus)
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
		
		$jobs = $this->prepareJobs($value);
		
		$config->setTaskIndex(1);
		$instance = new $config->type($config);
		$instance->setUnitTest(true);
		$jobs = $instance->run($jobs); 
		$instance->done();
		
		foreach($jobs as $job)
			$this->assertEquals($expectedStatus, $job->status);
	}
	
	private function prepareJobs($value)
	{
		$data = new KalturaImportJobData();
		$data->srcFileUrl = $value;
		
		$job = new KalturaBatchJob();
		$job->id = 1;
		$job->status = KalturaBatchJobStatus::PENDING;
		$job->data = $data;
		
		return array($job);
	}
}

?>
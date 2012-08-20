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
class KAsyncExtractMediaTest extends PHPUnit_Framework_TestCase 
{
	const JOB_NAME = 'KAsyncExtractMedia';
	
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
		$this->doTest(realpath(dirname( __FILE__ ) . '/../../../tests/files/example.avi'), KalturaBatchJobStatus::FINISHED);
	}
	
	public function testSpacedFile()
	{
		$path = realpath(dirname( __FILE__ ) . '/../../../tests/files/example.avi');
		$this->doTest(" $path", KalturaBatchJobStatus::FINISHED);
	}
	
	public function testMissingFile()
	{
		$this->doTest('aaa', KalturaBatchJobStatus::FAILED);
	}
	
	public function testEmptyFile()
	{
		$this->doTest('aaa', KalturaBatchJobStatus::FAILED);
	}
	
	public function testImageFile()
	{
		$this->doTest(realpath('../tests/files/thumb.jpg'), KalturaBatchJobStatus::FAILED);
	}
	
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
		$data = new KalturaExtractMediaJobData();
		$data->srcFileSyncLocalPath = $value;
		
		$job = new KalturaBatchJob();
		$job->id = 1;
		$job->status = KalturaBatchJobStatus::PENDING;
		$job->data = $data;
		
		return array($job);
	}
}

?>
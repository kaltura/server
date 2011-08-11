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
class KAsyncCaptureThumbTest extends PHPUnit_Framework_TestCase 
{
	const JOB_NAME = 'KAsyncCaptureThumb';
	
	private $outputFolder;
	private $testsConfig;
	
	private static $thumbParamsAttributes = array(
		"cropType",
		"quality",
		"cropX",
		"cropY",
		"cropWidth",
		"cropHeight",
		"videoOffset",
		"width",
		"height",
		"backgroundColor",
	);
		
	public function setUp() 
	{
		parent::setUp();
		
		$config = new Zend_Config_Ini(dirname(__FILE__) . "/KAsyncCaptureThumbTest.ini");
		$testConfig = $config->get('config');
		$this->outputFolder = dirname(__FILE__) . '/' . $testConfig->outputFolder;
		
		$this->testsConfig = $config->get('tests');
	}
	
	public function tearDown() 
	{
		parent::tearDown();
	}
	
	public function test()
	{
		foreach($this->testsConfig as $testName => $config)
		{
			$thumbParamsOutput = new KalturaThumbParamsOutput();
			foreach(self::$thumbParamsAttributes as $attribute)
			{
				if(isset($config->$attribute))
				{
					$thumbParamsOutput->$attribute = $config->$attribute;
					if($attribute == 'backgroundColor' && !is_numeric($thumbParamsOutput->$attribute))
						$thumbParamsOutput->$attribute = hexdec($thumbParamsOutput->$attribute);
				}
			}
				
			$this->doTest($config->source, $thumbParamsOutput, $config->expectedStatus, $testName);
		}
	}
	
	public function doTest($filePath, KalturaThumbParamsOutput $thumbParamsOutput, $expectedStatus, $testName)
	{
		$outputFileName = "$testName.jpg";
		$finalPath = "$this->outputFolder/$outputFileName";
		if(file_exists($finalPath))
			unlink($finalPath);
				
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
		{
			$this->assertEquals($expectedStatus, $job->status, "test [$testName] expected status [$expectedStatus] actual status [$job->status] with message [$job->message]");
			if($job->status != KalturaBatchJobStatus::FINISHED)
				continue;
				
			$outPath = $job->data->thumbPath;
			$this->assertFileExists($outPath);
				
			rename($outPath, $finalPath);
		}
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


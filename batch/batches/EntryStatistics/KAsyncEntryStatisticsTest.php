<?php
/**
 * @package Scheduler
 * @subpackage Debug
 */

/*
 * 
 * This script should be executed as follows:
 *   phpunit KAsyncEntryStatisticsTest.php
 * 
 */

require_once __DIR__ . "/../../bootstrap.php";
require_once __DIR__ . '/KAsyncEntryStatistics.class.php';

class KAsyncEntryStatisticsTest extends PHPUnit_Framework_TestCase
{
	const JOB_NAME = 'KAsyncEntryStatistics';

	public function testEntryStatistics()
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

		$config->setTaskIndex(1);
		$instance = new $config->type($config);
		$instance->setUnitTest(true);

		echo "\nStarting to run...\n";
		$instance->run();
		echo "\nDone running...\n";
		$instance->done();
	}
}

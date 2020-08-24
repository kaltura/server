<?php
ob_start();
// Report simple running errors only(no notice!!)
error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once(__DIR__ . '/../bootstrap.php');

class Worker_Status_from_Ini extends Zend_Config_Ini
{

	protected function _processSection($iniArray, $section, $config = array())
	{
		try
		{
			return  parent::_processSection($iniArray,$section,$config);
		}
		catch (Zend_Config_Exception $ex)
		{
			return array();
		}
	}

}

/**
 * $argv[1] - path to files configuration folder
 * example: /opt/kaltura/app/configurations/batch
 * $argv[2] - name of candidates example workers.ini
 */
$files = glob($argv[1]."/*$argv[2]");
if (count($argv) < 2 )
	die(PHP_EOL .
		'* $argv[1] - path to files configuration folder
		 * example: /opt/kaltura/app/configurations/batch
         * $argv[2] - name of candidates example workers.ini' . PHP_EOL);
$answers = array();
foreach($files as $file)
{
	ob_start();
	try
	{
		$config = new Worker_Status_from_Ini($file);
	}
	catch (Zend_Config_Exception $e)
	{
		die($e);
	}
	$configArray = $config->toArray();
	$periodicWorkers = getPeriodicWorkers($configArray);
	foreach ($periodicWorkers as $worker)
	{
		$criteria = new Criteria(SchedulerWorkerPeer::DATABASE_NAME);
		$criteria->add(SchedulerWorkerPeer::CONFIGURED_ID, $worker['id']);
		try
		{
			$schedulerWorkers = SchedulerWorkerPeer::doSelect($criteria);
		}
		catch (PropelException $e)
		{
			die($e);
		}
		/** @var SchedulerWorker  $schedulerWorker */
		foreach ($schedulerWorkers as $schedulerWorker)
		{
			$status = $schedulerWorker->getStatuses();
			$lastExecutionTime = $status[SchedulerStatus::RUNNING_BATCHES_LAST_EXECUTION_TIME];
			$sleepBetweenStopStart = $worker['sleepBetweenStopStart'];
			$maximumExecutionTime = $worker['maximumExecutionTime'];
			if ($lastExecutionTime)
			{
				/** $lastExecutionTime + max($sleepBetweenStopStart , 300 )  we are letting a margin of twice the execution time meaning
				 *  if job a should run every hour
				 *  we will wait for two hours before printing it as error
				 * minimum is 5 minutes so if job should run every 60 sec will will only inform the error after 5 minutes
				 */
				if ($lastExecutionTime + max($sleepBetweenStopStart + $maximumExecutionTime , intval($argv[3]) ) < time() - $sleepBetweenStopStart)
					$answers[] =  $schedulerWorker->getSchedulerId() . ',' . $schedulerWorker->getConfiguredId() . ',' .
						$schedulerWorker->getName() . ',' . date('Y-m-d H:i:s', $lastExecutionTime) . ',' . $sleepBetweenStopStart . ',' . 'BAD' . PHP_EOL;
					//$answers[] = prettyPrintNotRun($schedulerWorker,$lastExecutionTime,$sleepBetweenStopStart);
				else
					$answers[] =  $schedulerWorker->getSchedulerId() . ',' . $schedulerWorker->getConfiguredId() . ',' .
						$schedulerWorker->getName() . ',' . date('Y-m-d H:i:s', $lastExecutionTime) . ',' . $sleepBetweenStopStart . ',' . 'GOOD' . PHP_EOL;
					//$answers[] = prettyPrintRun($schedulerWorker,$lastExecutionTime);

			}
		}
	}
	ob_end_clean();

	foreach ($answers as $answer)
		echo $answer;
}

/**
 * @param $configArray
 * @return array
 */
function getPeriodicWorkers($configArray)
{
	$periodicWorkers = array();
	foreach ($configArray as $configuration) {
		if ($configuration['id'] && $configuration['sleepBetweenStopStart'])
			$periodicWorkers[] = $configuration;
	}
	return $periodicWorkers;
}


/**
 * @param SchedulerWorker $schedulerWorker
 * @param int $lastExecutionTime
 * @param int $sleepBetweenStopStart
 * @return string
 */
function prettyPrintNotRun($schedulerWorker, $lastExecutionTime, $sleepBetweenStopStart)
{
	return  "Scheduler ID: " . $schedulerWorker->getSchedulerId(). " - Worker ID: " . $schedulerWorker->getConfiguredId() .
			" - Worker Name : " . $schedulerWorker->getName().
			" " . " - Last Execution Time: " . date('Y-m-d H:i:s', $lastExecutionTime) . " Not Working correctly ".
			"More than $sleepBetweenStopStart second had past from Last execution " . PHP_EOL;
}

/**
 * @param SchedulerWorker $schedulerWorker
 * @param int $lastExecutionTime
 * @return string
 */
function prettyPrintRun($schedulerWorker, $lastExecutionTime)
{
	return  "Scheduler ID: " . $schedulerWorker->getSchedulerId(). " - Worker ID: " . $schedulerWorker->getConfiguredId() .
			" - Worker Name : " . $schedulerWorker->getName() .
			" " . " - Last Execution Time: " . date('Y-m-d H:i:s', $lastExecutionTime) . " Working correctly" . PHP_EOL;
}

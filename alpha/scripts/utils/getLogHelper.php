<?php

if($argc < 2) {
	echo "Arguments missing.\n\n";
	echo "Usage: php getLogHelper.php {job id}\n";
	exit;
}
if ($argv[1] == '-help') {
	echo "Usage: php getLogHelper.php {job id} {path to batch ini file (optional)}\n";
	exit;
}

$jobId = $argv[1];
$iniFile = '/opt/kaltura/app/configurations/batch/workers.ini';
if (count($argv) > 2)
	$iniFile = $argv[2];

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');



$firstRecord = retrieveHistoryRecord($jobId);
$schedulerId = $firstRecord->getSchedulerId();
$workerId = $firstRecord->getWorkerId();
$batchIndex = $firstRecord->getBatchIndex();

$machineName = retrieveNameByScheduler($schedulerId);
//$logName = getName($iniFile, $workerId);
printData($machineName, $schedulerId, $workerId, $batchIndex, $jobId);
return;

function retrieveNameByScheduler($schedulerId) {
	$criteria = new Criteria(SchedulerPeer::DATABASE_NAME);
	$criteria->add(SchedulerPeer::CONFIGURED_ID, $schedulerId);
	$v = SchedulerPeer::doSelect($criteria);
	return $v[0]->getName();
}

function retrieveHistoryRecord($jobId) {
	/* @var BatchJob $batch*/
	$batch = BatchJobPeer::retrieveByPK($jobId);
	$history = $batch->getHistory();
	return $history[0];
}

function getName($iniPath, $workerId) {
	// load zend config classes
	if(!class_exists('Zend_Config_Ini'))
	{
		require_once 'Zend/Config/Exception.php';
		require_once 'Zend/Config/Ini.php';
	}
	$config = new Zend_Config_Ini($iniPath);
	$result = $config->toArray();
	foreach($result as $key=>$value) {
		if (isset($value['id']) && $value['id'] == $workerId)
			return substr($key, 6 ,strlen($key)); // len of 'KAsync' = 6
	}
}

function printData($machineName, $schedulerId, $workerId, $batchIndex, $jobId) {
	printInGreen("Connect to machine: [$machineName] \n");
	printInGreen("schedulerId: [$schedulerId], workerId: [$workerId], batchIndex: [$batchIndex] \n");
	printInGreen("exe in /opt/var/log:    zgrep -C30 $jobId @BATCH_NAME@-$batchIndex-@DATE@.log.gz \n");
}
function printInGreen($str) {
	echo "\033[32m$str\033[0m";
}



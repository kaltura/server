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
$iniFile = '/opt/kaltura/app/configurations/batch/worker.ini';
if ($argv[2])
	$iniFile = $argv[2];

chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');
// load zend config classes
if(!class_exists('Zend_Config_Ini'))
{
	require_once 'Zend/Config/Exception.php';
	require_once 'Zend/Config/Ini.php';
}


$firstRecord = retrieveHistoryRecord($jobId);
$schedulerId = $firstRecord->getSchedulerId();
$workerId = $firstRecord->getWorkerId();
$batchIndex = $firstRecord->getBatchIndex();

$name = retrieveNameByScheduler($schedulerId);
$logName = getName($iniFile, $workerId);

printCommand($name, $jobId, $logName, $batchIndex);

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
	$config = new Zend_Config_Ini($iniPath);
	$result = $config->toArray();
	foreach($result as $key=>$value) {
		if (isset($value['id']) && $value['id'] == $workerId)
			return substr($key, 6 ,strlen($key)); // len of 'KAsync' = 6
	}
}
function printCommand($machineName, $jobId, $logName, $batchIndex) {
	printInRed("Command to execute: \n");
	printInGreen("    ssh $machineName \n");
	printInGreen("    cd /opt/kalture/log \n");
	printInGreen("    zgrep -C30 $jobId $logName-$batchIndex-2016....log.gz \n");
}
function printInGreen($str) {
	echo "\033[32m$str\033[0m";
}
function printInRed($str) {
	echo "\033[31m$str\033[0m";
}


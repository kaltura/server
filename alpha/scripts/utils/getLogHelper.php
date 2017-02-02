<?php

if($argc < 2) {
	echo "Arguments missing.\n\n";
	echo "Usage: php getLogHelper.php {job id}\n";
	exit;
}

$jobId = $argv[1];
chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');
$batchConfigDir = KALTURA_ROOT_PATH .'/configurations/batch/';



$firstRecord = retrieveHistoryRecord($jobId);
exitIfNull($firstRecord, "Can't retrieve history record for job [$jobId]\n");

$schedulerId = $firstRecord->getSchedulerId();
$workerId = $firstRecord->getWorkerId();
$batchIndex = $firstRecord->getBatchIndex();

$machineName = retrieveNameByScheduler($schedulerId);
printData($machineName, $schedulerId, $workerId, $batchIndex);

$config = getAllConf($batchConfigDir);
exitIfNull($config, "Can't retrieve config files\n");

$logName = getNameFromConf($config, $workerId);
$logDir = $config['template']['logDir'];

printInGreen("exe in $logDir:    zgrep -C30 $jobId $logName-$batchIndex-@DATE@.log.gz \n");
return;

function exitIfNull($val, $error) {if (!$val) exit("\n$error\n");}

function retrieveNameByScheduler($schedulerId) {
	$criteria = new Criteria(SchedulerPeer::DATABASE_NAME);
	$criteria->add(SchedulerPeer::CONFIGURED_ID, $schedulerId);
	$v = SchedulerPeer::doSelect($criteria);
	return $v[0]->getHost();
}

function retrieveHistoryRecord($jobId) {
	/* @var BatchJob $batch*/
	$batch = BatchJobPeer::retrieveByPK($jobId);
	$history = $batch->getHistory();
	return $history[0];
}

function getAllConf($path) {
	$content = '';
	$files = glob($path. '*.ini');
	foreach($files as $file)
		$content .= file_get_contents($file) . "\n";
	return parse_ini_string($content, true);
}

function getNameFromConf($conf, $workerId) {
	foreach($conf as $key=>$value)
		if (isset($value['id']) && $value['id'] == $workerId)
			return getNameFromKey($key);
	return "@BATCH_NAME@";
}

function getNameFromKey($str) {
	$parts = explode(":", $str);
	$prefix = 'KAsync';
	if (!(substr($str, 0, strlen($prefix)) === $prefix))
		return null;
	$name = substr($parts[0], strlen($prefix));
	return strtolower(rtrim($name, " "));
}

function printData($machineName, $schedulerId, $workerId, $batchIndex){
	printInGreen("Connect to machine: [$machineName] \n");
	printInGreen("schedulerId: [$schedulerId], workerId: [$workerId], batchIndex: [$batchIndex] \n");
}

function printInGreen($str) {
	echo "\033[32m$str\033[0m";
}



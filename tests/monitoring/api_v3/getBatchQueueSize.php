<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaClient.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'service-url:',
	'job-type:',
	'job-sub-type:',
));

if(!isset($options['service-url']))
{
	echo "Argument service-url is required";
	exit(-1);
}

if(!isset($options['job-type']))
{
	echo "Arguments job-type is required";
	exit(-1);
}
$jobType = $options['job-type'];

 if (!defined("KalturaBatchJobType::$jobType"))
{
	echo "job-type $jobType is not defined";
	exit(-1);
}


class KalturaMonitorClientLogger implements IKalturaLogger
{
	function log($msg)
	{
		echo "Client: $msg\n";
	}
}

$serviceUrl = $options['service-url'];
$clientConfig = new KalturaConfiguration();
$clientConfig->partnerId = null;
$clientConfig->serviceUrl = $serviceUrl;

if(isset($options['debug']))
	$clientConfig->setLogger(new KalturaMonitorClientLogger());

$config = parse_ini_file(__DIR__ . '/../config.ini', true);

$client = new KalturaClient($clientConfig);
$monitorResult = new KalturaMonitorResult();
$apiCall = null;

try
{
	$apiCall = 'session.start';
	$start = microtime(true);
	$ks = $client->session->start($config['batch-partner']['adminSecret'], "",  KalturaSessionType::ADMIN, $config['batch-partner']['id']);
	$client->setKs($ks);
		
	
	$apiCall = 'batch.getQueueSize';
	$workerQueueFilter = new KalturaWorkerQueueFilter();
	$workerQueueFilter->jobType = constant("KalturaBatchJobType::$jobType");
	$batchJobFilter = new KalturaBatchJobFilter();
	if (isset($options['job-sub-type'])) {
		$batchJobFilter->jobSubTypeEqual = $options['job-sub-type'];
	}
	$workerQueueFilter->filter = $batchJobFilter;
	
	$start = microtime(true);
	$queueSize = $client->batch->getQueueSize($workerQueueFilter);
 	$requestEnd =  microtime(true);
	$monitorResult->executionTime = $requestEnd - $start;
	$monitorResult->value =  $queueSize;
	$monitorResult->description = "Scheduler Queue for $jobType is: $monitorResult->value";
}
catch(KalturaException $e)
{
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $e->getCode();
	$error->description = $e->getMessage();
	$error->level = KalturaMonitorError::ERR;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = "Exception: " . get_class($e) . ", API: $apiCall, Code: " . $e->getCode() . ", Message: " . $e->getMessage();
}
catch(KalturaClientException $ce)
{
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $ce->getCode();
	$error->description = $ce->getMessage();
	$error->level = KalturaMonitorError::CRIT;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = "Exception: " . get_class($ce) . ", API: $apiCall, Code: " . $ce->getCode() . ", Message: " . $ce->getMessage();
}

echo "$monitorResult";
exit(0);

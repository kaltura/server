<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaClient.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt("u:");
$serviceUrl = $options["u"];
$clientConfig = new KalturaConfiguration();
$clientConfig->partnerId = null;
$clientConfig->serviceUrl = $serviceUrl;

$client = new KalturaClient($clientConfig);
$start = microtime(true);
$monitorResult = new KalturaMonitorResult();
try
{
	$res = $client->system->pingDatabase();
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;
	
	if($res)
	{
		$monitorResult->value = $monitorResult->executionTime;
		$monitorResult->description = "Execution time: $monitorResult->value seconds";
	}
	else
	{
		$monitorResult->value = -1;
		$monitorResult->description = 'Database ping failed';
	}
}
catch(KalturaClientException $ex)
{
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = 0;
	$monitorResult->description = $ex->getMessage();;
	
	$error = new KalturaMonitorError();
	$error->level = "ERR";
	$error->description = $ex->getMessage();
	$error->code = $ex->getCode();
	$monitorResult->errors[] = $error;
}

echo "$monitorResult";


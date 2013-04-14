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
	$res = $client->system->ping();
	$end = microtime(true);

	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = $monitorResult->executionTime;
	$monitorResult->description = "Execution time: $monitorResult->value seconds";
}
catch(KalturaClientException $ex)
{
	$end = microtime(true);
	
	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = -1;
	$monitorResult->description = $ex->getMessage();
}
echo "$monitorResult";

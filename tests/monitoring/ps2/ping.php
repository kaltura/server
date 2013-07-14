<?php
$client = null;
/* @var $client KalturaMonitorClientPs2 */
require_once __DIR__  . '/common.php';

$config = parse_ini_file(__DIR__ . '/../config.ini', true);

$start = microtime(true);
$monitorResult = new KalturaMonitorResult();
try
{
	$response = $client->request('ping');
	if(!isset($response['result']) || !isset($response['result']['status']))
		throw new Exception("no ping status returned");
	if($response['result']['status'] != 'ok')
		throw new Exception("invalid ping status: " . $response['result']['status']);
	
	$monitorResult->executionTime = microtime(true) - $start;
	$monitorResult->value = $monitorResult->executionTime;
	$monitorResult->description = "Ping time: $monitorResult->value seconds";
}
catch(Exception $e)
{
	$monitorResult->executionTime = microtime(true) - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $e->getCode();
	$error->description = $e->getMessage();
	$error->level = KalturaMonitorError::CRIT;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = "Exception: " . get_class($e) . ", Code: " . $e->getCode() . ", Message: " . $e->getMessage();
}

echo "$monitorResult";
exit(0);

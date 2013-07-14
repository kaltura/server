<?php
$config = array();
$client = null;
/* @var $client KalturaMonitorClientPs2 */
require_once __DIR__  . '/common.php';

$start = microtime(true);
$monitorResult = new KalturaMonitorResult();
try
{
	$params = array(
		'partner_id' => $config['monitor-partner']['id'],
		'widget_id' => $config['monitor-partner']['widgetId'],
	);
	
	$response = $client->request('startwidgetsession', $params);
	if(!isset($response['result']) || !isset($response['result']['ks']))
		throw new Exception("no ks returned");

	$monitorResult->executionTime = microtime(true) - $start;
	$monitorResult->value = $monitorResult->executionTime;
	$monitorResult->description = "Start session execution time: $monitorResult->value seconds";
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

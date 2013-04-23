<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaClient.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'service-url:',
	'ks-type:',
	'debug',
));

if(!isset($options['service-url']))
{
	echo "Argument service-url is required";
	exit(-1);
}

class KalturaMonitorClientLogger implements IKalturaLogger
{
	function log($msg)
	{
		echo "Client: $msg\n";
	}
}

$secretField = 'secret';
if(isset($options['ks-type']) && $options['ks-type'] == 'admin')
	$secretField = 'adminSecret';

$serviceUrl = $options['service-url'];
$clientConfig = new KalturaConfiguration();
$clientConfig->partnerId = null;
$clientConfig->serviceUrl = $serviceUrl;

if(isset($options['debug']))
	$clientConfig->setLogger(new KalturaMonitorClientLogger());

$config = parse_ini_file(__DIR__ . '/../config.ini', true);

$client = new KalturaClient($clientConfig);
$start = microtime(true);
$monitorResult = new KalturaMonitorResult();
$apiCall = null;
try
{
	$apiCall = 'session.start';
	$ks = $client->session->start($config['monitor-partner'][$secretField], 'monitor-user', KalturaSessionType::USER, $config['monitor-partner']['id']);
	$end = microtime(true);
	
	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = $monitorResult->executionTime;
	$monitorResult->description = "Start session execution time: $monitorResult->value seconds";
}
catch(Exception $ex)
{
	$end = microtime(true);
	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = -1;
	$monitorResult->description = "Exception: " . get_class($ex) . ", API: $apiCall, Code: " . $ex->getCode() . ", Message: " . $ex->getMessage();
}

echo "$monitorResult";
exit(0);

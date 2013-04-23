<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaClient.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'service-url:',
	'entry-id:',
	'entry-reference-id:',
	'list-flavors',
	'list-cue-points',
	'list-metadata',
	'debug',
));

if(!isset($options['service-url']))
{
	echo "Argument service-url is required";
	exit(-1);
}

if(!isset($options['entry-id']) && !isset($options['entry-reference-id']))
{
	echo "One of arguments entry-id or entry-reference-id is required";
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
$start = microtime(true);
$monitorResult = new KalturaMonitorResult();
$apiCall = null;
try
{
	$apiCall = 'session.start';
	$ks = $client->session->start($config['monitor-partner']['secret'], 'monitor-user', KalturaSessionType::USER, $config['monitor-partner']['id']);
	$client->setKs($ks);
		
	$entryId = null;

	$contextDataParams = new KalturaEntryContextDataParams();
	$contextDataParams->streamerType = 'http';
	
	$client->startMultiRequest();

	if(isset($options['entry-id']))
	{
		$entry = $client->baseEntry->get($options['entry-id']);
		/* @var $entry KalturaMediaEntry */
	}
	elseif(isset($options['entry-reference-id']))
	{
		$baseEntryList = $client->baseEntry->listByReferenceId($options['entry-reference-id']);
		/* @var $baseEntryList KalturaBaseEntryListResponse */
		$entry = $baseEntryList->objects[0];
		/* @var $entry KalturaMediaEntry */
	}
	
	$client->baseEntry->getContextData($entry->id, $contextDataParams);
	
	if(isset($options['list-flavors']))
	{
		$flavorAssetFilter = new KalturaFlavorAssetFilter();
		$flavorAssetFilter->entryIdEqual = $entry->id;
		$flavorAssetFilter->statusEqual = KalturaFlavorAssetStatus::READY;
		$client->flavorAsset->listAction($flavorAssetFilter);
	}
	
	if(isset($options['list-cue-points']))
	{
		$cuePointFilter = new KalturaCuePointFilter();
		$cuePointFilter->entryIdEqual = $entry->id;
		$cuePointFilter->statusEqual = KalturaCuePointStatus::READY;
		$cuePointPlugin = KalturaCuePointClientPlugin::get($client);
		$cuePointPlugin->cuePoint->listAction($cuePointFilter);
	}
	
	if(isset($options['list-metadata']))
	{
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->entryIdEqual = $entry->id;
		$metadataFilter->statusEqual = KalturaMetadataStatus::VALID;
		$metadataPlugin = KalturaMetadataClientPlugin::get($client);
		$metadataPlugin->metadata->listAction($metadataFilter);
	}

	$requestStart = microtime(true);
	$apiCall = 'multi-request';
	$responses = $client->doMultiRequest();
	$requestEnd = microtime(true);
	
	foreach($responses as $response)
	{
		if(is_array($response) && isset($response['message']) && isset($response['code']))
			throw new KalturaException($response["message"], $response["code"]);
	}
	
	$monitorResult->executionTime = $requestEnd - $start;
	$monitorResult->value = $requestEnd - $requestStart;
	$monitorResult->description = "Multi-request execution time: $monitorResult->value seconds";
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

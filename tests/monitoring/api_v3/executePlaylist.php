<?php
require_once realpath(__DIR__ . '/../../') . '/lib/KalturaClient.php';
require_once realpath(__DIR__ . '/../') . '/KalturaMonitorResult.php';

$options = getopt('', array(
	'service-url:',
	'playlist-id:',
	'playlist-reference-id:',
	'debug',
));

if(!isset($options['service-url']))
{
	echo "Argument service-url is required";
	exit(-1);
}

if(!isset($options['playlist-id']) && !isset($options['playlist-reference-id']))
{
	echo "One of arguments playlist-id or playlist-reference-id is required";
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
		
	$playlistId = null;
	if(isset($options['playlist-id']))
	{
		$playlistId = $options['playlist-id'];
	}
	elseif(isset($options['playlist-reference-id']))
	{
		$apiCall = 'baseEntry.listByReferenceId';
		$baseEntryList = $client->baseEntry->listByReferenceId($options['playlist-reference-id']);
		/* @var $baseEntryList KalturaBaseEntryListResponse */
		if(count($baseEntryList->objects))
		{
			$playlist = reset($baseEntryList->objects);
			/* @var $playlist KalturaPlaylist */
			$playlistId = $playlist->id;
		}
		else
		{
			$error = new KalturaMonitorError();
			$error->level = "ERR";
			$error->description = "Playlist with reference id [" . $options['playlist-reference-id'] . "] not found";
			$error->level = KalturaMonitorError::CRIT;
			$monitorResult->errors[] = $error;
		}
	}

	if($playlistId)
	{
		$playlistStart = microtime(true);
		$apiCall = 'playlist.execute';
		$client->playlist->execute($playlistId);
		$playlistEnd = microtime(true);
		
		$monitorResult->executionTime = $playlistEnd - $start;
		$monitorResult->value = $playlistEnd - $playlistStart;
		$monitorResult->description = "Playlist execution time: $monitorResult->value seconds";
	}
	else
	{
		$end = microtime(true);
		$monitorResult->executionTime = $end - $start;
		$monitorResult->value = -1;
		$monitorResult->description = "Playlist not found";
	}
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

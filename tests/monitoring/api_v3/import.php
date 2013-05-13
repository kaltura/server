<?php
$config = array();
$client = null;
$serviceUrl = null;
/* @var $client KalturaClient */
require_once __DIR__  . '/common.php';

$options = getopt('', array(
	'service-url:',
	'debug',
	'timeout:',
	'media-url:',
));

if(!isset($options['timeout']))
{
	echo "Argument timeout is required";
	exit(-1);
}
$timeout = $options['timeout'];

$mediaUrl = $serviceUrl . '/content/templates/entry/data/kaltura_logo_animated_blue.flv';
if(isset($options['media-url']))
	$mediaUrl = $options['media-url'];

$start = microtime(true);
$monitorResult = new KalturaMonitorResult();
$apiCall = null;
try
{
	$apiCall = 'session.start';
	$ks = $client->session->start($config['monitor-partner']['secret'], 'monitor-user', KalturaSessionType::USER, $config['monitor-partner']['id']);
	$client->setKs($ks);
		
	 // Creates a new entry
	$entry = new KalturaMediaEntry();
	$entry->name = 'monitor-test';
	$entry->description = 'monitor-test';
	$entry->mediaType = KalturaMediaType::VIDEO;
	
	$resource = new KalturaUrlResource();
	$resource->url = $mediaUrl;
	
	$apiCall = 'multirequest';
	$client->startMultiRequest();
	$requestEntry = $client->media->add($entry);
	/* @var $requestEntry KalturaMediaEntry */
	$client->media->addContent($requestEntry->id, $resource);
	$client->media->get($requestEntry->id);
	
	$results = $client->doMultiRequest();
	foreach($results as $index => $result)
	{
		if ($client->isError($result))
			throw new KalturaException($result["message"], $result["code"]);
	}
		
	// Waits for the entry to start conversion
	$createdEntry = end($results);
	$timeoutTime = time() + $timeout;
	/* @var $createdEntry KalturaMediaEntry */
	while ($createdEntry)
	{
		if(time() > $timeoutTime)
			throw new Exception("timed out, entry id: $createdEntry->id");
			
		if($createdEntry->status == KalturaEntryStatus::IMPORT)
		{
			sleep(1);
			$apiCall = 'media.get';
			$createdEntry = $client->media->get($createdEntry->id);
			continue;
		}
		
		$monitorResult->executionTime = microtime(true) - $start;
		$monitorResult->value = $monitorResult->executionTime;
		
		if($createdEntry->status == KalturaEntryStatus::READY || $createdEntry->status == KalturaEntryStatus::PRECONVERT)
		{
			$monitorResult->description = "import time: $monitorResult->executionTime seconds";
		}
		elseif($createdEntry->status == KalturaEntryStatus::ERROR_IMPORTING)
		{
			$error = new KalturaMonitorError();
			$error->description = "import failed, entry id: $createdEntry->id";
			$error->level = KalturaMonitorError::CRIT;
			
			$monitorResult->errors[] = $error;
			$monitorResult->description = "import failed, entry id: $createdEntry->id";
		}
		else
		{
			$error = new KalturaMonitorError();
			$error->description = "unexpected entry status: $createdEntry->status, entry id: $createdEntry->id";
			$error->level = KalturaMonitorError::CRIT;
			
			$monitorResult->errors[] = $error;
			$monitorResult->description = "unexpected entry status: $createdEntry->status, entry id: $createdEntry->id";
		}
		
		break;
	}

	try
	{
		$apiCall = 'media.delete';
		$createdEntry = $client->media->delete($createdEntry->id);
	}
	catch(Exception $ex)
	{
		$error = new KalturaMonitorError();
		$error->code = $ex->getCode();
		$error->description = $ex->getMessage();
		$error->level = KalturaMonitorError::WARN;
		
		$monitorResult->errors[] = $error;
	}
}
catch(KalturaException $e)
{
	$monitorResult->executionTime = microtime(true) - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $e->getCode();
	$error->description = $e->getMessage();
	$error->level = KalturaMonitorError::ERR;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = "Exception: " . get_class($e) . ", API: $apiCall, Code: " . $e->getCode() . ", Message: " . $e->getMessage();
}
catch(KalturaClientException $ce)
{
	$monitorResult->executionTime = microtime(true) - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $ce->getCode();
	$error->description = $ce->getMessage();
	$error->level = KalturaMonitorError::CRIT;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = "Exception: " . get_class($ce) . ", API: $apiCall, Code: " . $ce->getCode() . ", Message: " . $ce->getMessage();
}
catch(Exception $ex)
{
	$monitorResult->executionTime = microtime(true) - $start;
	
	$error = new KalturaMonitorError();
	$error->code = $ex->getCode();
	$error->description = $ex->getMessage();
	$error->level = KalturaMonitorError::ERR;
	
	$monitorResult->errors[] = $error;
	$monitorResult->description = $ex->getMessage();
}

echo "$monitorResult";
exit(0);

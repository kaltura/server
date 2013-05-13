<?php
$config = array();
$client = null;
$serviceUrl = null;
/* @var $client KalturaClient */
require_once __DIR__  . '/common.php';

$options = getopt('', array(
	'service-url:',
	'debug',
	'entry-id:',
	'entry-reference-id:',
));

$start = microtime(true);
$monitorResult = new KalturaMonitorResult();
$apiCall = null;
try
{
	$apiCall = 'session.start';
	$ks = $client->session->start($config['monitor-partner']['adminSecret'], 'monitor-user', KalturaSessionType::ADMIN, $config['monitor-partner']['id']);
	$client->setKs($ks);
	
	$entry = null;
	/* @var $entry KalturaMediaEntry */
	if(isset($options['entry-id']))
	{
		$apiCall = 'media.get';
		$entry = $client->media->get($options['entry-id']);
	}
	elseif(isset($options['entry-reference-id']))
	{
		$apiCall = 'baseEntry.listByReferenceId';
		$baseEntryList = $client->baseEntry->listByReferenceId($options['entry-reference-id']);
		/* @var $baseEntryList KalturaBaseEntryListResponse */
		if(!count($baseEntryList->objects))
			throw new Exception("Entry with reference id [" . $options['entry-reference-id'] . "] not found");
			
		$entry = reset($baseEntryList->objects);
	}
	
	if($entry->status != KalturaEntryStatus::READY)
		throw new Exception("Entry id [$entry->id] is not ready for thumbnail capturing");
	
	$thumbParams = new KalturaThumbParams();
	$thumbParams->videoOffset = 3;
	
	$apiCall = 'thumbAsset.generate';
	$thumbAsset = $client->thumbAsset->generate($entry->id, $thumbParams);
	/* @var $thumbAsset KalturaThumbAsset */
	if(!$thumbAsset)
		throw new Exception("thumbnail asset not created");
	
	$monitorResult->executionTime = microtime(true) - $start;
	$monitorResult->value = $monitorResult->executionTime;
	
	if($thumbAsset->status == KalturaThumbAssetStatus::READY || $thumbAsset->status == KalturaThumbAssetStatus::EXPORTING)
	{
		$monitorResult->description = "capture time: $monitorResult->executionTime seconds";
	}
	elseif($thumbAsset->status == KalturaThumbAssetStatus::ERROR)
	{
		$error = new KalturaMonitorError();
		$error->description = "captura failed, asset id, $thumbAsset->id: $thumbAsset->description";
		$error->level = KalturaMonitorError::CRIT;
		
		$monitorResult->description = "captura failed, asset id, $thumbAsset->id";
	}
	else
	{
		$error = new KalturaMonitorError();
		$error->description = "unexpected thumbnail status, $thumbAsset->status, asset id, $thumbAsset->id: $thumbAsset->description";
		$error->level = KalturaMonitorError::CRIT;
		
		$monitorResult->errors[] = $error;
		$monitorResult->description = "unexpected thumbnail status, $thumbAsset->status, asset id, $thumbAsset->id: $thumbAsset->description";
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

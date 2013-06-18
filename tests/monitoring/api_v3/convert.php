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
	'entry-id:',
	'entry-reference-id:',
));

if(!isset($options['timeout']))
{
	echo "Argument timeout is required";
	exit(-1);
}
$timeout = $options['timeout'];

$start = microtime(true);
$monitorResult = new KalturaMonitorResult();
$apiCall = null;
try
{
	$apiCall = 'session.start';
	$ks = $client->session->start($config['monitor-partner']['secret'], 'monitor-user', KalturaSessionType::USER, $config['monitor-partner']['id']);
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
		throw new Exception("Entry id [$entry->id] is not ready for reconvert");
	
	$jobId = $client->media->convert($entry->id);
	
	$apiCall = 'session.start';
	$client->setKs(null);
	$ks = $client->session->start($config['batch-partner']['adminSecret'], 'monitor-user', KalturaSessionType::ADMIN, $config['batch-partner']['id']);
	$client->setKs($ks);
	
	$apiCall = 'jobs.getConvertProfileStatus';
	$job = $client->jobs->getConvertProfileStatus($jobId);
	/* @var $job KalturaBatchJobResponse */
	
	$timeoutTime = time() + $timeout;
	while ($job)
	{
		if(time() > $timeoutTime)
			throw new Exception("timed out, entry id: $entry->id");
			
		if($job->batchJob->status == KalturaBatchJobStatus::ALMOST_DONE)
		{
			sleep(1);
			$apiCall = 'jobs.getConvertProfileStatus';
			$job = $client->jobs->getConvertProfileStatus($jobId);
			continue;
		}
		
		$monitorResult->executionTime = microtime(true) - $start;
		$monitorResult->value = $monitorResult->executionTime;
		
		if($job->batchJob->status == KalturaBatchJobStatus::FINISHED || $job->batchJob->status == KalturaBatchJobStatus::FINISHED_PARTIALLY)
		{
			$monitorResult->description = "convert time: $monitorResult->executionTime seconds";
		}
		elseif($job->batchJob->status == KalturaBatchJobStatus::FAILED || $job->batchJob->status == KalturaBatchJobStatus::FATAL)
		{
			$error = new KalturaMonitorError();
			$error->description = "convert failed, entry id: $entry->id";
			$error->level = KalturaMonitorError::CRIT;
			
			$monitorResult->errors[] = $error;
			$monitorResult->description = "convert failed, entry id: $entry->id";
		}
		else
		{
			$error = new KalturaMonitorError();
			$error->description = "unexpected job status: {$job->batchJob->status}, entry id: $entry->id";
			$error->level = KalturaMonitorError::CRIT;
			
			$monitorResult->errors[] = $error;
			$monitorResult->description = "unexpected job status: {$job->batchJob->status}, entry id: $entry->id";
		}
		
		break;
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

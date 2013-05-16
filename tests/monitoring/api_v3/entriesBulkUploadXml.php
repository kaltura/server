<?php

define('JOB_STATUS_CODE_OK', 0);
define('JOB_STATUS_CODE_WARNING', 1);
define('JOB_STATUS_CODE_ERROR', 2);
define('TOKEN_CHAR', '@');
define('BULK_XML_FILE', __DIR__ . '/xml/entries_bulk_upload.xml');

// replaces all tokens in the given string with the configuration values and returns the new string
function replaceTokensInString($string, $values)
{
	foreach($values as $key => $var)
	{
		if(is_array($var))
			continue;

		$key = TOKEN_CHAR . $key . TOKEN_CHAR;
		$string = str_replace($key, $var, $string);
	}
	return $string;
}

$config = array();
$client = null;
/* @var $client KalturaClient */
require_once __DIR__  . '/common.php';

$options = getopt('', array(
	'service-url:',
	'debug',
));

$monitorResult = new KalturaMonitorResult();
$apiCall = null;

try
{
	$apiCall = 'session.start';
	$start = microtime(true);
	$ks = $client->session->start($config['monitor-partner']['secret'], 'monitor-user', KalturaSessionType::USER, $config['monitor-partner']['id']);
	$client->setKs($ks);
	$data = @file_get_contents(BULK_XML_FILE);
	$entries_data = array(
		'ENTRY1_URL' => $clientConfig->serviceUrl . '/content/templates/entry/data/kaltura_logo_animated_green.flv',
		'ENTRY2_URL' => $clientConfig->serviceUrl . '/content/templates/entry/data/kaltura_logo_animated_red.flv',
	);
	
	$xml = replaceTokensInString($data, $entries_data);
	$xmlPath = uniqid('bulk_upload') . '.xml';
	file_put_contents($xmlPath, $xml);

	$bulkError = null;
	$bulkStatus;
	$apiCall = 'media.bulkUploadAdd';
	$jobData = new KalturaBulkUploadXmlJobData();
	$bulkUpload = $client->media->bulkUploadAdd($xmlPath, $jobData);
	/* @var $bulkUpload KalturaBulkUpload */


	$bulkUploadPlugin = KalturaBulkUploadClientPlugin::get($client);
	while($bulkUpload)
	{
		if($bulkUpload->status == KalturaBatchJobStatus::FINISHED)
		{
			$bulkStatus = JOB_STATUS_CODE_OK;
			$monitorDescription = "Entries Bulk Upload Job was finished successfully";
			break;
		}
		if($bulkUpload->status == KalturaBatchJobStatus::FINISHED_PARTIALLY)
		{
			$bulkStatus = JOB_STATUS_CODE_WARNING;
			$monitorDescription = "Entries Bulk Upload Job was finished, but with some errors";
			break;
		}
		if($bulkUpload->status == KalturaBatchJobStatus::FAILED)
		{
			$bulkError =  "Bulk upload [$bulkUpload->id] failed";
			break;
		}
		if($bulkUpload->status == KalturaBatchJobStatus::ABORTED)
		{
			$bulkError = "Bulk upload [$bulkUpload->id] aborted";
			break;
		}
		if($bulkUpload->status == KalturaBatchJobStatus::FATAL)
		{
			$bulkError = "Bulk upload [$bulkUpload->id] failed fataly";
			break;
		}
			
		sleep(15);
		$bulkUpload = $bulkUploadPlugin->bulk->get($bulkUpload->id);
	}

	$end = microtime(true);
	if(!$bulkUpload)
	{
		 $bulkError = "Bulk upload not found";
	}
	
	if ($bulkError) {
		$bulkStatus = JOB_STATUS_CODE_ERROR;
		$error = new KalturaMonitorError();
		$error->description = $bulkError;
		$error->level = KalturaMonitorError::ERR;
	
		$monitorResult->errors[] = $error;
		$monitorDescription = $bulkError;
	}
	
	try
	{
		$apiCall = 'media.list';
		$entriesFilter = new KalturaMediaEntryFilter();
		$entriesFilter->categoriesFullNameIn = 'monitor>xml';
		$entriesPager = new KalturaFilterPager();
		$entriesPager->pageSize = 10;

		$entriesList = $client->media->listAction($entriesFilter, $entriesPager);
		foreach($entriesList->objects as $entry)
		/*KalturaMediaEntry*/
		{
			$apiCall = 'media.delete';
			$client->media->delete($entry->id);
		}
	}
	catch(Exception $ex)
	{
		$error = new KalturaMonitorError();
		$error->code = $ex->getCode();
		$error->description = $ex->getMessage();
		$error->level = KalturaMonitorError::WARN;
		
		$monitorResult->errors[] = $error;
	}

	$monitorResult->executionTime = $end - $start;
	$monitorResult->value = $bulkStatus;
	$monitorResult->description = $monitorDescription;
	
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


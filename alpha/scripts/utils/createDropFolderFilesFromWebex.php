<?php
require_once(__DIR__ . '/../bootstrap.php');
require_once('/opt/kaltura/web/content/clientlibs/batchClient/KalturaClient.php');
require_once('/opt/kaltura/web/content/clientlibs/batchClient/KalturaPlugins/KalturaDropFolderClientPlugin.php');
require_once('/opt/kaltura/app/batch/batches/KBatchBase.class.php');

const WEEK_IN_SECONDS = 604800;
if($argc < 7)
{
	echo "Missing arguments.\n";
	echo "php simulateDryRun.php {dropFolderId} {admin ks} {serviceUrl} {start date} {end date} {result filename}.\n";
	die;
}

$dropFolderId = $argv[1];
$ks =  $argv[2];
$url = $argv[3];
$startDate = $argv[4];
$endDate = $argv[5];
$fileName = $argv[6];
$config = new KalturaConfiguration(-2);
$config->serviceUrl = $url;
$client = new KalturaClient($config);
$client->setKs($ks);
$dropFolderPlugin = KalturaDropFolderClientPlugin::get($client);
KBatchBase::$kClient = $client;
$dropFolder = $dropFolderPlugin->dropFolder->get($dropFolderId);
KBatchBase::impersonate($dropFolder->partnerId);
$webexEngine = KWebexDropFolderEngine::withDropFolder($dropFolder);
$securityContext = $webexEngine::getWebexClientSecurityContext($dropFolder);
$dropFolderServiceTypes = $dropFolder->webexServiceType ? explode(',', $dropFolder->webexServiceType) :
	array(WebexXmlComServiceTypeType::_MEETINGCENTER);
$serviceTypes = webexWrapper::stringServicesTypesToWebexXmlArray($dropFolderServiceTypes);
$webexWrapper = new webexWrapper($dropFolder->webexServiceUrl . '/' . $dropFolder->path, $securityContext, array('KalturaLog', 'err'), array('KalturaLog', 'debug'));
for($i = $startDate; $i+WEEK_IN_SECONDS <= $endDate; $i=$i+WEEK_IN_SECONDS)
{
	$startTime = date('m/j/Y H:i:s', $i);
	$endTime = (date('m/j/Y H:i:s', $i+WEEK_IN_SECONDS));
	$result = $webexWrapper->listAllRecordings($serviceTypes, $startTime, $endTime);
	if($result)
	{
		$numOfFiles = count($result);
		$text = "Found {$numOfFiles} of files for {$startTime}-{$endTime}.";
		file_put_contents($fileName, $text, FILE_APPEND );
		KalturaLog::debug($text);
		$handleResult = $webexEngine->HandleNewFiles($result->getRecording());
		$string_data = serialize($handleResult);
		file_put_contents($fileName, $string_data, FILE_APPEND );
	}
	else
	{
		KalturaLog::debug("No files found for {$startTime}-{$endTime}.");
	}
}

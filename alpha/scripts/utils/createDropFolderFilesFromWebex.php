<?php
require_once(__DIR__ . '/../bootstrap.php');
require_once('/opt/kaltura/web/content/clientlibs/batchClient/KalturaClient.php');
require_once('/opt/kaltura/app/batch/batches/KBatchBase.class.php');

if($argc < 6)
{
	echo "Missing arguments.\n";
	echo "php simulateDryRun.php {dropFolderId} {admin ks} {serviceUrl} {start date} {end date}.\n";
	die;
}

$dropFolderId = $argv[1];
$ks =  $argv[2];
$url = $argv[3];
$startDate = $argv[4];
$endDate = $argv[5];

$config = new KalturaConfiguration(-2);
$config->serviceUrl = $url;
$client = new KalturaClient($config);
$client->setKs($ks);
$dropFolderPlugin = KalturaDropFolderClientPlugin::get($client);
KBatchBase::$kClient = $client;
$dropFolder = $dropFolderPlugin->dropFolder->get($dropFolderId);
$webexEngine = KWebexDropFolderEngine::withDropFolder($dropFolder);
$webexEngine->HandleNewFiles(null);

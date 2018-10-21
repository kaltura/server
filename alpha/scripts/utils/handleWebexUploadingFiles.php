<?php
require_once(__DIR__ . '/../bootstrap.php');
require_once('/opt/kaltura/web/content/clientlibs/batchClient/KalturaClient.php');
require_once('/opt/kaltura/web/content/clientlibs/batchClient/KalturaPlugins/KalturaDropFolderClientPlugin.php');
require_once('/opt/kaltura/app/batch/batches/KBatchBase.class.php');

if($argc < 5)
{
	echo "Missing arguments.\n";
	echo "php $argv[0] {dropFolderId} {admin ks} {serviceUrl} {log filename}.\n";
	die;
}


$dropFolderId = $argv[1];
$ks =  $argv[2];
$url = $argv[3];
$logFileName = $argv[4];
$config = new KalturaConfiguration(-2);
$config->serviceUrl = $url;
$client = new KalturaClient($config);
$client->setKs($ks);
$dropFolderPlugin = KalturaDropFolderClientPlugin::get($client);
KBatchBase::$kClient = $client;
$dropFolder = $dropFolderPlugin->dropFolder->get($dropFolderId);
KBatchBase::impersonate($dropFolder->partnerId);
$webexEngine = new KWebexDropFolderEngine();
$webexEngine->setDropFolder($dropFolder);
$webexEngine->handleUploadingFiles();



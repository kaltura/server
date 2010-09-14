<?php

// script to add storage export job from csv of flavor id

ini_set ( "memory_limit", "256M" );

define ( 'ROOT_DIR', realpath ( dirname ( __FILE__ ) . '/../../' ) );
require_once (ROOT_DIR . '/infra/bootstrap_base.php');
require_once (ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath ( KAutoloader::buildPath ( KALTURA_ROOT_PATH, "vendor", "propel", "*" ) );
KAutoloader::addClassPath ( KAutoloader::buildPath ( KALTURA_ROOT_PATH, "plugins", "metadata", "*" ) );
KAutoloader::setClassMapFilePath ( '../cache/classMap.cache' );
KAutoloader::register ();

error_reporting ( E_ALL );
//KalturaLog::setLogger(new KalturaStdoutLogger());


$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();
/*
$partnerId = $argv [1];
$sourceFile = $argv [2];
$flavorAssetIds = file ( $sourceFile ) or die ( 'Could not read file!' );

$counter = 0;
foreach ( $flavorAssetIds as $flavorAssetId ) {
	$counter ++;
	addStorageExportJob ( $partnerId, rtrim ( $flavorAssetId ) );
	if ($counter == 99) {
		sleep ( 180 );
		$counter = 0;
	}
}*/
addStorageExportJob(303812,'_egzz2rjh');

function addStorageExportJob($partnerId, $flavorAssetId) {
	$flavorAsset = flavorAssetPeer::retrieveById ( $flavorAssetId );
	if (! $flavorAsset) {
		echo 'could not load flavorasset ' . $flavorAssetId . PHP_EOL;
		return;
	}
	$entryId = $flavorAsset->getEntryId ();
	$syncKey = $flavorAsset->getSyncKey ( flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET );
	$fileSync = kFileSyncUtils::getReadyFileSyncForKey($syncKey);
	
	$srcFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
	$externalStorage = storageProfilePeer::retrieveByPK(1);
	
	$netStorageExportData = new kStorageExportJobData ();
	$netStorageExportData->setServerUrl ( $externalStorage->getStorageUrl () );
	$netStorageExportData->setServerUsername ( $externalStorage->getStorageUsername () );
	$netStorageExportData->setServerPassword ( $externalStorage->getStoragePassword () );
	$netStorageExportData->setFtpPassiveMode ( $externalStorage->getStorageFtpPassiveMode () );
	$netStorageExportData->setSrcFileSyncLocalPath ( $fileSync-> $srcFileSyncLocalPath );
	$netStorageExportData->setSrcFileSyncId ( $fileSync->getId () );
	$netStorageExportData->setForce ( false ); //false was the default 
	$netStorageExportData->setDestFileSyncStoredPath ( $externalStorage->getStorageBaseDir () . '/' . $fileSync->getFilePath () );
	
	$batchJob = new BatchJob ();
	$batchJob->setEntryId ( $entryId );
	$batchJob->setPartnerId ( $partnerId );
	
	echo 'created job with ID: ' . $batchJob->getId () . PHP_EOL;
	return kJobsManager::addJob ( $batchJob, $netStorageExportData, BatchJob::BATCHJOB_TYPE_STORAGE_EXPORT, $externalStorage->getProtocol () );
}

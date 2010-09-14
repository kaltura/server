<?php
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

$partnerId = $argv[1];
$sourceFile = $argv[2];
$flavorAssetIds = file ($sourceFile) or die ( 'Could not read file!' );

$counter = 0;
foreach ( $flavorAssetIds as $flavorAssetId ) {
	$counter++;
	addExtractMediaJob ( $partnerId, rtrim($flavorAssetId) );
	if($counter == 99)
	{
	   sleep(180);
	   $counter = 0;
	}
}
function addExtractMediaJob($partner_id, $flavorAssetId)
{
	$flavorAsset = flavorAssetPeer::retrieveById($flavorAssetId);
	if(!$flavorAsset)
	{
		echo 'could not load flavorasset '.$flavorAssetId.PHP_EOL;
		return;
	}
	$entryId = $flavorAsset->getEntryId();
	$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	$inputFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
	$extractMediaData = new kExtractMediaJobData();
	$extractMediaData->setSrcFileSyncLocalPath($inputFileSyncLocalPath);
	$extractMediaData->setFlavorAssetId($flavorAssetId);
	
	$batchJob = new BatchJob();
	
	$batchJob->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
	$batchJob->setParentJobId(0); //0
	$batchJob->setPartnerId($partner_id);
	$batchJob->setEntryId($entryId);
	$batchJob->setPriority(5); //default:3 more:2 less: 4 (bulks) 
	$batchJob->setWorkGroupId(0); //0
	$batchJob->setSubpId($partner_id*100); //$this->partner_id * 100 
	$batchJob->setBulkJobId(0); //0
	$batchJob->setDc(0); // 0 (pa)
	$batchJob->setRootJobId(0); // only this 0
	
	$batchJob->save();
	
//	kLog::log("Creating Extract Media job, with source file: " . $extractMediaData->getSrcFileSyncLocalPath()); 
	echo 'created job with ID: '.$batchJob->getId().PHP_EOL;
	return kJobsManager::addJob($batchJob, $extractMediaData, BatchJob::BATCHJOB_TYPE_EXTRACT_MEDIA, mediaInfo::ASSET_TYPE_ENTRY_INPUT);
}

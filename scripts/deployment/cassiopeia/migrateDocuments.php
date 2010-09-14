<?php

$dryRun = true; //TODO: change for real run
$stopFile = dirname(__FILE__).'/stop_migrate';
$srcFlavorParamsId = 0;
$swfFlavorParamsId = 0; // TODO set real value
$entryLimitEachLoop = 100;

if(!$swfFlavorParamsId)
{
	echo "\$swfFlavorParamsId must be set\n";
	exit;
}

set_time_limit(0);

ini_set("memory_limit","700M");

chdir(dirname(__FILE__));

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "document", "*"));
KAutoloader::setClassMapFilePath('../../cache/classMap.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

DbManager::setConfig(kConf::getDB());
DbManager::initialize();

// stores the last handled entry int id, helps to restore in case of crash
$lastEntryFile = 'last_entry';
$lastEntry = 0;
if(file_exists($lastEntryFile))
	$lastEntry = file_get_contents($lastEntryFile);
if(!$lastEntry)
	$lastEntry = 0;

$c = new Criteria();
$c->add(entryPeer::INT_ID, $lastEntry, Criteria::GREATER_THAN);
$c->add(entryPeer::TYPE, entry::ENTRY_TYPE_DOCUMENT);
$c->addAscendingOrderByColumn(entryPeer::INT_ID);
$c->setLimit($entryLimitEachLoop);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);

$entries = entryPeer::doSelect($c, $con);
while(count($entries))
{
	foreach($entries as $entry)
	{
		if (file_exists($stopFile)) {
			die('STOP FILE CREATED');
		}
		$lastEntry = $entry->getIntId();
		KalturaLog::log('-- entry id ' . $entry->getId() . " int id[$lastEntry]");
		
		// create the source flavor asset
		$srcSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		if(kFileSyncUtils::fileSync_exists($srcSyncKey))
		{	
			KalturaLog::log('-- DATA type file sync found for entry ['.$entry->getId().']');
			KalturaLog::log('-- Creating new flavor asset...');
			$flavorAsset = new flavorAsset();
			
			$flavorAsset->setPartnerId($entry->getPartnerId());
			$flavorAsset->setEntryId($entry->getId());
			$flavorAsset->setIsOriginal(true);
			$flavorAsset->setTags(flavorParams::TAG_SOURCE);
			$flavorAsset->setFlavorParamsId($srcFlavorParamsId);
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_READY);
			$flavorAsset->setVersion(1);
			$flavorAsset->setDescription('Auto migrated from document entry');
			
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($srcSyncKey, true, false);
			if($fileSync)
			{
				KalturaLog::log('-- Ready file sync found');
				$fileName = $fileSync->getFullPath();
				$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				
				$flavorAsset->setFileExt($ext);
				$flavorAsset->setSize($fileSync->getFileSize());
			}
			else
			{
				KalturaLog::err('-- Ready file sync NOT FOUND!');
			}
			
			if (is_null($flavorAsset->getSize())) {
				$flavorAsset->setSize(0);
			}
			
			if ($dryRun) {
				KalturaLog::log('-- DRY RUN - not saving flavor asset');
			}
			else {
				$flavorAsset->save();
			}
			$flavorSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			
			KalturaLog::log('-- Creating new file sync link...');
			if ($dryRun) {
				KalturaLog::log('-- DRY RUN - not creating file sync link');
			}
			else {
				kFileSyncUtils::createSyncFileLinkForKey($flavorSyncKey, $srcSyncKey, false);
			}
		}
		else
		{
			KalturaLog::log('-- DATA type file sync NOT FOUND for entry ['.$entry->getId().']');	
		}
		
		// creates the swf flavor asset
		$swfSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD, 'swf');
		if(kFileSyncUtils::fileSync_exists($swfSyncKey))
		{	
			KalturaLog::log('-- DOWNLOAD type file sync found for entry ['.$entry->getId().']');
			KalturaLog::log('-- Creating new flavor asset...');
			
			$flavorAsset = new flavorAsset();
			
			$flavorAsset->setPartnerId($entry->getPartnerId());
			$flavorAsset->setEntryId($entry->getId());
			$flavorAsset->setIsOriginal(false);
			$flavorAsset->setFlavorParamsId($swfFlavorParamsId);
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_READY);
			$flavorAsset->setVersion(1);
			$flavorAsset->setDescription('Auto migrated from document entry');
			
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($swfSyncKey, true, false);
			if($fileSync)
			{
				KalturaLog::log('-- Ready file sync found');
				$fileName = $fileSync->getFullPath();
				$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				
				$flavorAsset->setFileExt($ext);
				$flavorAsset->setSize($fileSync->getFileSize());
			}
			else
			{
				KalturaLog::err('-- Ready file sync NOT FOUND!');
			}
			
			if (is_null($flavorAsset->getSize())) {
				$flavorAsset->setSize(0);
			}
			
			if ($dryRun) {
				KalturaLog::log('-- DRY RUN - not saving flavor asset');
			}
			else {
				$flavorAsset->save();
			}
			$flavorSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			
			KalturaLog::log('-- Creating new file sync link...');
			KalturaLog::log('-- Creating new file sync link...');
			if ($dryRun) {
				KalturaLog::log('-- DRY RUN - not creating file sync link');
			}
			else {
				kFileSyncUtils::createSyncFileLinkForKey($flavorSyncKey, $swfSyncKey, false);
			}
		}
		else
		{
			KalturaLog::log('-- DOWNLOAD type file sync NOT FOUND for entry ['.$entry->getId().']');	
		}
		
		file_put_contents($lastEntryFile, $lastEntry);
	}
	
	entryPeer::clearInstancePool();
	FileSyncPeer::clearInstancePool();
	flavorAssetPeer::clearInstancePool();
	
	$c = new Criteria();
	$c->add(entryPeer::INT_ID, $lastEntry, Criteria::GREATER_THAN);
	$c->add(entryPeer::TYPE, entry::ENTRY_TYPE_DOCUMENT);
	$c->addAscendingOrderByColumn(entryPeer::INT_ID);
	$c->setLimit($entryLimitEachLoop);
	$entries = entryPeer::doSelect($c, $con);
}

KalturaLog::log('Done');

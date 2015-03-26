<?php
// this chdir can be changed according to environment
chdir(__DIR__ . '/../../');
require_once(__DIR__ . '/../../bootstrap.php');

$subTypes = array(entry::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_PRIMARY, entry::FILE_SYNC_ENTRY_SUB_TYPE_LIVE_SECONDARY);

function getFileSyncs($entryId)
{
	GLOBAL $subTypes;

	$fileSyncs = array();
	$fileSyncsResult = array();

	$fileSyncsCriteria=new Criteria();
	$fileSyncsCriteria->add(FileSyncPeer::PARTNER_ID, $GLOBALS['partnerId']);
	$fileSyncsCriteria->add(FileSyncPeer::OBJECT_ID, $entryId);
	$fileSyncsCriteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ENTRY);
	$fileSyncsCriteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_DELETED);
	
	foreach($subTypes as $type)
	{
		$fileSyncsCriteria->add(FileSyncPeer::OBJECT_SUB_TYPE , $type);
		
		FileSyncPeer::setUseCriteriaFilter(false);
		$fileSyncsResult = FileSyncPeer::doSelect($fileSyncsCriteria);
		FileSyncPeer::setUseCriteriaFilter(true);
		
		if (!empty($fileSyncsResult))
			$fileSyncs = array_merge ($fileSyncs , $fileSyncsResult);
	}
	return $fileSyncs;
}

function getAssets($entryId){
	
	$assetsCriteria = new Criteria();
	$assetsCriteria->add(assetPeer::PARTNER_ID , $GLOBALS['partnerId']);
	$assetsCriteria->add(assetPeer::STATUS , asset::FLAVOR_ASSET_STATUS_DELETED);
	$assetsCriteria->add(assetPeer::ENTRY_ID , $entryId);
	
	assetPeer::setUseCriteriaFilter(false);
	$assets = assetPeer::doSelect($assetsCriteria);
	assetPeer::setUseCriteriaFilter(true);

	return $assets;
}

if($argc != 4)
{
	KalturaLog::DEBUG("Usage: php restoreDeletedLiveEntriesByList.php [live entries file name] [partnerId] [realRun]");
	die("Not enough parameters" . PHP_EOL);
}

$liveEntriesIdsfile = $argv[1];


if(!file_exists($liveEntriesIdsfile))
	die('problems with file' . PHP_EOL);

//should the script save() ? by default will not save
$dryRun= $argv[3] !== 'realRun';
KalturaStatement::setDryRun($dryRun);
if ($dryRun)
		KalturaLog::debug('dry run --- in order to save, give real_run as a second parameter');

$liveEntriesIds = file($liveEntriesIdsfile);
$liveEntriesIds = array_map('trim',$liveEntriesIds);

$partnerId = $argv[2];

foreach ($liveEntriesIds as $liveEntriesId)
{
	entryPeer::setUseCriteriaFilter(false);
	$liveEntry = entryPeer::retrieveByPK($liveEntriesId);
	entryPeer::setUseCriteriaFilter(true);
	
	if (!$liveEntry)
	{
		KalturaLog::debug("ERR1 - live entry id " . $liveEntriesId . " not found");
		continue;
	}

	if ($liveEntry->getPartnerId() != $partnerId)
	{
		KalturaLog::debug("ERR2 - live entry id " . $liveEntriesId . " belongs to a different partner");
		continue;
	}
	
	$fileSyncs = getFileSyncs($liveEntriesId);

	foreach ( $fileSyncs as $fileSync)
	{
		$fileSyncId = $fileSync->getId();
		KalturaLog::debug('saving file sync ID: ' . $fileSyncId);
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$fileSync->save();
	}
	
	$assetsToRestore = getAssets($liveEntriesId);
	
	foreach($assetsToRestore as $asset)
	{
		$assetId = $asset->getId();
		KalturaLog::debug('saving asset ID: ' . $assetId);
		$asset->setStatus(asset::FLAVOR_ASSET_STATUS_READY);
		$asset->save();
	}
	
	KalturaLog::debug('saving live entry ID: ' . $liveEntriesId);
	$liveEntry->setStatus(entryStatus::READY);
	$liveEntry->save();
	
	KEventsManager::flushEvents();
}


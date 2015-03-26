<?php
// this chdir can be changed according to environment
chdir(__DIR__ . '/../');
require_once(__DIR__ . '/../bootstrap.php');

function getFileSyncs ($metadataProfilesId , $version)
{
	GLOBAL $partnerId;

	$c = new Criteria;
	$c->add(FileSyncPeer::PARTNER_ID, $partnerId);
	$c->add(FileSyncPeer::OBJECT_ID, $metadataProfilesId);
	$c->add(FileSyncPeer::VERSION, $version);
	$c->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::METADATA_PROFILE);
	$c->add(FileSyncPeer::STATUS , FileSync::FILE_SYNC_STATUS_DELETED);
	
	FileSyncPeer::setUseCriteriaFilter(false);
	$metadataProfileFileSyncs = FileSyncPeer::doSelect($c);
	FileSyncPeer::setUseCriteriaFilter(true);
	
	return $metadataProfileFileSyncs;
	
}

if($argc != 4)
{
	KalturaLog::DEBUG("Usage: php restoreDeletedMeatadataProfilesByList.php [metadata profiles ids file name] [partnerId] [realRun]");
	die("Not enough parameters" . PHP_EOL);
}

if(!file_exists($argv[1]))
	die('problems with file' . PHP_EOL);

//should the script save() ? by default will not save
$dryRun= $argv[3] !== 'realRun';
KalturaStatement::setDryRun($dryRun);
if ($dryRun)
		KalturaLog::debug('dry run --- in order to save, give real_run as a second parameter');

$metadataProfilesIdsFileName = $argv[1];
if (!file_exists($metadataProfilesIdsFileName))
	die("file does not exist!" . PHP_EOL);
$metadataProfilesIds = file($metadataProfilesIdsFileName);
$metadataProfilesIds = array_map('trim',$metadataProfilesIds);

$partnerId = $argv[2];

foreach ($metadataProfilesIds as $metadataProfilesId)
{
	MetadataProfilePeer::setUseCriteriaFilter(false);
	$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfilesId);
	MetadataProfilePeer::setUseCriteriaFilter(true);
	
	if (!$metadataProfile)
	{
		KalturaLog::debug("ERR1 - metadata profile id " . $metadataProfilesId . " not found");
		continue;
	}

	if ($metadataProfile->getPartnerId() != $partnerId)
	{
		KalturaLog::debug("ERR2 - metadata profile id " . $metadataProfilesId . " belongs to a different partner");
		continue;
	}
	
	$version = $metadataProfile->getVersion();
	
	$fileSyncs = getFileSyncs($metadataProfilesId , $version);

	foreach ( $fileSyncs as $fileSync)
	{
		$fileSyncId = $fileSync->getId();
		$dc = $fileSync->getDc();
		KalturaLog::debug('saving file sync ID: ' . $fileSyncId . ' - version - ' . $version . ' - dc - ' . $dc);
		$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		$fileSync->save();
	}
	
	KalturaLog::debug('saving metadata profile ID: ' . $metadataProfilesId);
	$metadataProfile->setStatus(MetadataProfile::STATUS_ACTIVE);
	$metadataProfile->save();
	KEventsManager::flushEvents();
}


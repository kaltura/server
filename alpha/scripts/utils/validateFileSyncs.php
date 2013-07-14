<?php
if($argc < 3)
	die("Usage: php " . __FILE__ . " dc objectType [dryRun/realRun] [startId]\n");

$dc = $argv[1];
$objectType = $argv[2];
$dryRun = (!isset($argv[3]) || strtolower($argv[3]) != 'realrun');
$startId = (isset($argv[4]) ? $argv[4] : null);

chdir(__DIR__ . '/../');
require_once(__DIR__ . '/../bootstrap.php');

function deleteObject(FileSync $fileSync)
{
	$object = kFileSyncUtils::retrieveObjectForFileSync($fileSync);
	$key = $object->getSyncKey($fileSync->getObjectSubType());
	if($key->version != $fileSync->getVersion())
		return;
	
	switch($fileSync->getObjectType())
	{
		case FileSyncObjectType::UICONF:
			$object->setStatus(uiConf::UI_CONF_STATUS_DELETED);
			$object->save();
			break;
			
		case FileSyncObjectType::ENTRY:
			myEntryUtils::deleteEntry($object);
			try
			{
				$wrapper = objectWrapperBase::getWrapperClass($object);
				$wrapper->removeFromCache("entry", $object->getId());
			}
			catch(Exception $e)
			{
				KalturaLog::err($e);
			}
			break;
			
		case FileSyncObjectType::ASSET:
			$object->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$object->setDeletedAt(time());
			$object->save();		
			break;
			
		case FileSyncObjectType::METADATA:
			$object->setStatus(Metadata::STATUS_DELETED);
			$object->save();
			break;
			
		default:
			return;
	}
	
	if($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_LINK)
		return;
	
	$criteria = new Criteria();
	$criteria->add(FileSyncPeer::DC, $fileSync->getDc());
	$criteria->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_LINK);
	$criteria->add(FileSyncPeer::LINKED_ID, $fileSync->getId());

	$links = FileSyncPeer::doSelect($criteria);
	foreach($links as $link)
		deleteObject($link);
}

KalturaStatement::setDryRun($dryRun);

$criteria = new Criteria();
$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);
$criteria->add(FileSyncPeer::FILE_TYPE, FileSync::FILE_SYNC_FILE_TYPE_FILE);
$criteria->add(FileSyncPeer::OBJECT_TYPE, $objectType);
$criteria->add(FileSyncPeer::DC, $dc);
if($startId)
	$criteria->add(FileSyncPeer::ID, $startId, Criteria::GREATER_THAN);

$fileSyncsCount = FileSyncPeer::doCount($criteria);
KalturaLog::debug("Found [$fileSyncsCount] file syncs");

$criteria->addAscendingOrderByColumn(FileSyncPeer::ID);
$criteria->setLimit(500);

$lastId = 0;
$index = 0;
$fileSyncs = FileSyncPeer::doSelect($criteria);
while(count($fileSyncs))
{
	foreach($fileSyncs as $fileSync)
	{
		/* @var $fileSync FileSync */
		
		$index++;
		$lastId = $fileSync->getId();
		
		if(!file_exists($fileSync->getFullPath()))
			deleteObject($fileSync);
			
		KalturaLog::debug("Handled [$index/$fileSyncsCount]");
	}
	kMemoryManager::clearMemory();
	
	$nextCriteria = clone $criteria;
	$nextCriteria->add(FileSyncPeer::ID, $lastId, Criteria::GREATER_THAN);
	$fileSyncs = FileSyncPeer::doSelect($nextCriteria);
}

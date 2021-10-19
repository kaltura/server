<?php

require_once(__DIR__ . '/../bootstrap.php');

define('ORIGINAL_ONLY', 0);


function partnerIdAllowed($partnerId)
{
	global $partnerIdsType, $partnerIds;

	switch ($partnerIdsType)
	{
	case 'exclude':
		return !in_array($partnerId, $partnerIds);

	case 'include':
		return in_array($partnerId, $partnerIds);

	default:		// all
		return true;
	}
}

function handleRegularFileSyncs($assetId, $fileSyncs)
{
	global $targetDcId;

	// find a ready file sync + target dc file sync
	$readyFileSync = null;
	$targetDcFileSync = null;
	foreach ($fileSyncs as $fileSync)
	{
		if (is_null($fileSync->getFileType()))
		{
			KalturaLog::log("XXX $assetId: NULL_FILE_TYPE - setting to FILE");
			$fileSync->setFileType(FileSync::FILE_SYNC_FILE_TYPE_FILE);
			$fileSync->save();
		}

		if (!in_array($fileSync->getFileType(), array(FileSync::FILE_SYNC_FILE_TYPE_FILE, FileSync::FILE_SYNC_FILE_TYPE_URL, FileSync::FILE_SYNC_FILE_TYPE_CACHE)))
		{
			KalturaLog::log("XXX $assetId: BAD_FILE_TYPE" . $fileSync->getFileType() . " - unexpected file type");
			return;
		}

		if ($fileSync->getDc() == $targetDcId)
		{
			$targetDcFileSync = $fileSync;
			continue;
		}

		if ($fileSync->getStatus() == FileSync::FILE_SYNC_STATUS_READY && !$readyFileSync)
		{
			$readyFileSync = $fileSync;
		}
	}

	if ($targetDcFileSync)
	{
		// target dc file sync exists, check the status
		switch ($targetDcFileSync->getStatus())
		{
		case FileSync::FILE_SYNC_STATUS_READY:
			break;

		case FileSync::FILE_SYNC_STATUS_PENDING:
			if (!$readyFileSync)
			{
				KalturaLog::log("XXX $assetId: PENDING_NO_PATH - pending file sync without src path");
				break;
			}

			if ($targetDcFileSync->getSrcPath() == $readyFileSync->getFullPath() && $targetDcFileSync->getFromCustomData('srcDc', null, -1) == $readyFileSync->getDc())
			{
				KalturaLog::log("XXX $assetId: PENDING_WITH_PATH - pending file sync with valid src path, " . $readyFileSync->getDc() . " " . $readyFileSync->getFullPath() . " " . $targetDcFileSync->getId());
			}
			else
			{
				KalturaLog::log("XXX $assetId: PENDING_PATH_ADDED - pending file sync with bad src path " . $targetDcFileSync->getSrcPath() . ", setting from " . $readyFileSync->getId());
				$targetDcFileSync->setSrcPath($readyFileSync->getFullPath());
				$targetDcFileSync->setSrcEncKey($readyFileSync->getSrcEncKey());
				$targetDcFileSync->putInCustomData('srcDc', $readyFileSync->getDc());
				$targetDcFileSync->save();
			}
			break;

		case FileSync::FILE_SYNC_STATUS_ERROR:
			KalturaLog::log("XXX $assetId: ERROR_STATUS_FIXED - file sync has error status moving to pending");
			$targetDcFileSync->setStatus(FileSync::FILE_SYNC_STATUS_PENDING);
			$targetDcFileSync->save();
			break;

		default:
			KalturaLog::log("XXX $assetId: BAD_STATUS" . $targetDcFileSync->getStatus() . " - non ready file sync");
			break;
		}

		return;
	}

	if (!$readyFileSync)
	{
		KalturaLog::log("XXX $assetId: NO_READY_FILE_SYNC - no ready file syncs, skipping");
		return;
	}

	// create missing file sync
	try
	{
		$newfileSync = $readyFileSync->cloneToAnotherStorage($targetDcId);
		$newfileSync->setLinkCount(0);
		$newfileSync->putInCustomData('srcDc', $readyFileSync->getDc());

		$syncKey = kFileSyncUtils::getKeyForFileSync($newfileSync);

		KalturaLog::log("XXX $assetId: CREATED - creating file sync in dc $targetDcId key $syncKey");
		$newfileSync->save();
	}
	catch (Exception $e)
	{
		KalturaLog::log("XXX $assetId: CREATE_FAILED - failed to create file sync");
		return;
	}
}

function getOriginalDc($syncKey)
{
	global $sourceDcIds;

	$c = FileSyncPeer::getCriteriaForFileSyncKey($syncKey);
	$c->add(FileSyncPeer::DC, $sourceDcIds, Criteria::IN);
	$fileSyncs = FileSyncPeer::doSelect($c);

	$resolved = array();
	foreach ($fileSyncs as $fileSync)
	{
		if ($fileSync->getLinkedId())
		{
			$fileSync = FileSyncPeer::retrieveByPK($fileSync->getLinkedId());
			if(!$fileSync || !in_array($fileSync->getDc(), $sourceDcIds))
			{
				continue;
			}
		}

		$resolved[] = $fileSync;
	}

	if (!count($resolved))
	{
		return false;
	}

	$best = reset($resolved);
	foreach ($resolved as $cur)
	{
		// prefer ready
		$isReadyBest = $best->getStatus() == FileSync::FILE_SYNC_STATUS_READY;
		$isReadyCur = $cur->getStatus() == FileSync::FILE_SYNC_STATUS_READY;
		if ($isReadyBest != $isReadyCur)
		{
			if ($isReadyCur)
			{
				$best = $cur;
			}
			continue;
		}

		// prefer original
		$isOriginalBest = $best->getOriginal();
		$isOriginalCur = $cur->getOriginal();
		if ($isOriginalBest != $isOriginalCur)
		{
			if ($isOriginalCur)
			{
				$best = $cur;
			}
			continue;
		}

		// prefer direct (non-link)
		$isDirectBest = $best->getObjectId() == $syncKey->getObjectId();
		$isDirectCur = $cur->getObjectId() == $syncKey->getObjectId();
		if ($isDirectBest != $isDirectCur)
		{
			if ($isDirectCur)
			{
				$best = $cur;
			}
			continue;
		}
	}

	return $best->getDc();
}

function handleSyncKey($assetId, $syncKey, $depth = 0)
{
	global $targetDcId, $allDcIds;

	KalturaLog::log("$assetId - handling file sync key " . $syncKey);

	if (ORIGINAL_ONLY)
	{
		$originalDcId = getOriginalDc($syncKey);
		if ($originalDcId === false)
		{
			KalturaLog::log("XXX $assetId: NO_ORIGINAL_DC - failed to get original dc");
			return;
		}

		KalturaLog::log("$assetId - using dc $originalDcId");
		$dcIds = array(strval($originalDcId), $targetDcId);
	}
	else
	{
		$dcIds = $allDcIds;
	}

	// get the file syncs
	$c = FileSyncPeer::getCriteriaForFileSyncKey($syncKey);
	$c->add(FileSyncPeer::DC, $dcIds, Criteria::IN);
	$c->addDescendingOrderByColumn(FileSyncPeer::ORIGINAL);
	$c->addAscendingOrderByColumn(FileSyncPeer::DC);
	$fileSyncs = FileSyncPeer::doSelect($c);
	if (!$fileSyncs)
	{
		KalturaLog::log("XXX $assetId: NO_FILE_SYNCS - no file syncs");
		return;
	}

	// resolve the file syncs
	$resolvedFileSyncKeys = array();
	$targetResolvedFileSyncKey = null;
	$targetFileSync = null;
	$resolvedKey = null;

	foreach ($fileSyncs as $fileSync)
	{
		if ($fileSync->getLinkedId())
		{
			$resolvedFileSync = FileSyncPeer::retrieveByPK($fileSync->getLinkedId());
			if(!$resolvedFileSync)
			{
				KalturaLog::log("XXX $assetId: BROKEN_LINK_DELETED - deleting broken link to " . $fileSync->getLinkedId());
				$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_DELETED);
				$fileSync->save();
				handleSyncKey($assetId, $syncKey);		// restart
				return;
			}
		}
		else
		{
			$resolvedFileSync = $fileSync;
		}

		if ($resolvedFileSync->getDc() != $fileSync->getDc())
		{
			if ($fileSync->getDc() == $targetDcId)
			{
				KalturaLog::log("XXX $assetId: CROSS_DC_LINK_DELETED - deleting cross dc link " . $fileSync->getDc() . ' -> ' . $resolvedFileSync->getDc());
				$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_DELETED);
				$fileSync->save();
				handleSyncKey($assetId, $syncKey);		// restart
			}
			else
			{
				KalturaLog::log("XXX $assetId: CROSS_DC_LINK - cross dc link " . $fileSync->getDc() . ' -> ' . $resolvedFileSync->getDc());
			}
			return;
		}

		$resolvedKey = kFileSyncUtils::getKeyForFileSync($resolvedFileSync);

		if ($fileSync->getDc() == $targetDcId)
		{
			$targetFileSync = $fileSync;
			$targetResolvedFileSyncKey = strval($resolvedKey);
		}
		else
		{
			$resolvedFileSyncKeys[] = strval($resolvedKey);
		}
	}

	if (count(array_unique($resolvedFileSyncKeys)) > 1)
	{
		KalturaLog::log("XXX $assetId: MULTIPLE_KEYS - resolved to multiple keys");
		return;
	}

	if ($resolvedFileSyncKeys)
	{
		$resolvedSyncKey = reset($resolvedFileSyncKeys);
	}
	else
	{
		$resolvedSyncKey = $targetResolvedFileSyncKey;
	}

	if ($targetFileSync)
	{
		if ($resolvedSyncKey != $targetResolvedFileSyncKey)
		{
			KalturaLog::log("XXX $assetId: LINK_DIFF_KEY_DELETED - target dc link resolves to different key $targetResolvedFileSyncKey");
			$targetFileSync->setStatus(FileSync::FILE_SYNC_STATUS_DELETED);
			$targetFileSync->save();
			handleSyncKey($assetId, $syncKey);		// restart
			return;
		}
	}

	if ($resolvedSyncKey == strval($syncKey))
	{
		// not using links
		handleRegularFileSyncs($assetId, $fileSyncs);
		return;
	}

	if ($depth)
	{
		KalturaLog::log("XXX $assetId: LINK_MULTI_LEVEL - two levels of links");
		return;
	}

	// handle the linked key first
	handleSyncKey($assetId, $resolvedKey, $depth + 1);

	// look for a link in the target dc
	foreach ($fileSyncs as $fileSync)
	{
		if ($fileSync->getDc() != $targetDcId)
		{
			continue;
		}

		$resolvedFileSync = kFileSyncUtils::resolve($fileSync);

		$status = $fileSync->getStatus();
		$resolvedStatus = $resolvedFileSync->getStatus();
		if ($status != $resolvedStatus)
		{
			if ($resolvedStatus == FileSync::FILE_SYNC_STATUS_READY && $status != FileSync::FILE_SYNC_STATUS_READY)
			{
				KalturaLog::log("XXX $assetId: NON_READY_LINK_FIXED - non ready link to ready file sync, setting link to ready");
				$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
				if (is_null($fileSync->getFileSize()))
				{
					$fileSync->setFileSize(-1);
				}
				$fileSync->save();
			}
			else
			{
				KalturaLog::log("XXX $assetId: LINK_STATUS_{$status}_{$resolvedStatus} - link status different than resolved status");
			}
		}

		return;
	}

	// no link in target dc - create one

	// get the file sync to link to
	$c = FileSyncPeer::getCriteriaForFileSyncKey($resolvedKey);
	$c->add(FileSyncPeer::DC, $targetDcId);
	$fileSyncs = FileSyncPeer::doSelect($c);
	if (!$fileSyncs)
	{
		KalturaLog::log("XXX $assetId: NO_LINK_TARGET - failed to get link target");
		return;
	}

	if (count($fileSyncs) > 1)
	{
		KalturaLog::log("XXX $assetId: MULTIPLE_TARGETS - more than one file sync in dc $targetDcId for key " . $resolvedKey);
		return;
	}

	$sourceFileSync = reset($fileSyncs);

	$sourceFileSync = kFileSyncUtils::resolve($sourceFileSync);

	// create the link
	$linkFileSync = FileSync::createForFileSyncKey($syncKey);
	$linkFileSync->setDc($sourceFileSync->getDc());
	$linkFileSync->setStatus($sourceFileSync->getStatus());
	$linkFileSync->setOriginal($sourceFileSync->getOriginal());
	$linkFileSync->setLinkedId($sourceFileSync->getId());
	$linkFileSync->setFileSize(-1);

	if($sourceFileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_URL)
	{
		$linkFileSync->setFileType(FileSync::FILE_SYNC_FILE_TYPE_URL);
		$linkFileSync->setFileRoot($sourceFileSync->getFileRoot());
		$linkFileSync->setFilePath($sourceFileSync->getFilePath());
	}
	else
	{
		$linkFileSync->setFileType(FileSync::FILE_SYNC_FILE_TYPE_LINK);
	}

	kFileSyncUtils::incrementLinkCountForFileSync($sourceFileSync);

	try
	{
		KalturaLog::log("XXX $assetId: CREATED_LINK - creating link $syncKey -> " . $sourceFileSync->getId());
		$linkFileSync->save();
	}
	catch (Exception $e)
	{
		KalturaLog::log("XXX $assetId: CREATE_LINK_FAILED - failed to create link");
		return;
	}
}

function handleFileSyncKeys($handle)
{
	$count = 0;
	while($line = fgets($handle))
	{
		$line = trim($line);
		if (!$line)
		{
			continue;
		}

		$split = explode(' ', $line);
		if (count($split) != 5)
		{
			continue;
		}

		$syncKey = new FileSyncKey();
		list($syncKey->object_id, $syncKey->object_type, $syncKey->object_sub_type, $syncKey->version, $syncKey->partner_id) = $split;

		$count++;
		if ($count % 100 == 0)
		{
			kMemoryManager::clearMemory();
		}

		// process
		handleSyncKey($syncKey->object_id, $syncKey);
	}
}

function printPaths($assetId, $syncKey)
{
	global $allDcIds;

	$c = FileSyncPeer::getCriteriaForFileSyncKey($syncKey);
	$c->add(FileSyncPeer::DC, $allDcIds, Criteria::IN);
	$fileSyncs = FileSyncPeer::doSelect($c);

	foreach ($fileSyncs as $fileSync)
	{
		if ($fileSync->getLinkedId())
		{
			$fileSync = FileSyncPeer::retrieveByPK($fileSync->getLinkedId());
			if(!$fileSync)
			{
				continue;
			}
		}

		if (!is_null($fileSync->getFileType()) && !in_array($fileSync->getFileType(), array(FileSync::FILE_SYNC_FILE_TYPE_FILE, FileSync::FILE_SYNC_FILE_TYPE_URL, FileSync::FILE_SYNC_FILE_TYPE_CACHE)))
		{
			continue;
		}

		$paths[$fileSync->getDc()] = array($fileSync->getId(), $fileSync->getFullPath());
	}

	$msg = 'YYY ' . $assetId;
	foreach ($allDcIds as $dc)
	{
		$path = isset($paths[$dc]) ? $paths[$dc] : array('', '');
		$msg .= "\t" . $path[0] . "\t" . $path[1];
	}

	KalturaLog::log($msg);
}

function handleAssets($handle)
{
	$count = 0;
	while($assetId = fgets($handle))
	{
		$assetId = trim($assetId);
		if (!$assetId)
		{
			continue;
		}

		$count++;
		if ($count % 100 == 0)
		{
			kMemoryManager::clearMemory();
		}

		// get the asset
		$c = new Criteria();
		$c->add(assetPeer::ID, $assetId);
		$c->add(assetPeer::STATUS, array(flavorAsset::FLAVOR_ASSET_STATUS_DELETED, flavorAsset::FLAVOR_ASSET_STATUS_ERROR), Criteria::NOT_IN);
		$asset = assetPeer::doSelectOne($c);
		if (!$asset)
		{
			KalturaLog::log("XXX $assetId: LOAD_ASSET_FAILED - failed to load asset");
			continue;
		}

		if (!partnerIdAllowed($asset->getPartnerId()))
		{
			KalturaLog::log("XXX $assetId: IGNORED_PID - ignored partner " . $asset->getPartnerId());
			continue;
		}

		$partner = PartnerPeer::retrieveByPK($asset->getPartnerId());
		if (!$partner)
		{
			KalturaLog::log("XXX $assetId: LOAD_PID_FAILED - failed to load pid " . $asset->getPartnerId());
			continue;
		}

		if ($partner->getStatus() != Partner::PARTNER_STATUS_ACTIVE)
		{
			KalturaLog::log("XXX $assetId: INACTIVE_PID - inactive pid " . $asset->getPartnerId());
			continue;
		}

		// process
		$syncKey = $asset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		handleSyncKey($assetId, $syncKey);
		printPaths($assetId, $syncKey);
	}
}

function handleEntries($handle)
{
	$count = 0;
	while($entryId = fgets($handle))
	{
		$entryId = trim($entryId);
		if (!$entryId)
		{
			continue;
		}

		$count++;
		if ($count % 100 == 0)
		{
			kMemoryManager::clearMemory();
		}

		// get the assets
		KalturaLog::debug('Retrieving assets for entry ' . $entryId);
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entryId);
		$c->add(assetPeer::TYPE, assetPeer::retrieveAllFlavorsTypes(), Criteria::IN);
		$c->add(assetPeer::STATUS, array(flavorAsset::FLAVOR_ASSET_STATUS_DELETED, flavorAsset::FLAVOR_ASSET_STATUS_ERROR), Criteria::NOT_IN);
		$assets = assetPeer::doSelect($c);

		// process
		foreach ($assets as $asset)
		{
			$assetId = $asset->getId();

			if (!partnerIdAllowed($asset->getPartnerId()))
			{
				KalturaLog::log("XXX $assetId: IGNORED_PID - ignored partner " . $asset->getPartnerId());
				continue;
			}

			$syncKey = $asset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
			handleSyncKey($assetId, $syncKey);
		}
	}
}


if ($argc != 7)
{
	echo "USAGE: <source storage ids> <target storage id> <file name> <type - entry/asset/filesynckey> <partner ids/!partner ids/all> <dryrun/realrun>\n";
	exit(1);
}

$sourceDcIds = explode(',', $argv[1]);
$targetDcId = $argv[2];
$fileName = $argv[3];
$fileType = $argv[4];

if ($argv[5] == 'all')
{
	$partnerIdsType = 'all';
	$partnerIds = null;
}
else if (substr($argv[5], 0, 1) == '!')
{
	$partnerIdsType = 'exclude';
	$partnerIds = explode(',', substr($argv[5], 1));
}
else
{
	$partnerIdsType = 'include';
	$partnerIds = explode(',', $argv[5]);
}

$dryRun = $argv[6] != 'realrun';

if ($dryRun)
{
	KalturaLog::debug('*************** In Dry run mode ***************');
}
else
{
	KalturaLog::debug('*************** In Real run mode ***************');
}
KalturaStatement::setDryRun($dryRun);

$allDcIds = array_merge($sourceDcIds, array($targetDcId));

if ($fileName != '-')
{
	$handle = fopen($fileName, 'r');
	if (!$handle)
	{
		echo 'Failed to open ' . $fileName . "\n";
		exit(1);
	}
}
else
{
	$handle = STDIN;
}

switch ($fileType)
{
	case 'entry':
		handleEntries($handle);
		break;

	case 'asset':
		handleAssets($handle);
		break;

	case 'filesynckey':
		handleFileSyncKeys($handle);
		break;

	default:
		echo "Invalid file type $fileType, must be entry/asset\n";
		exit(1);
}

KalturaLog::debug("done!");

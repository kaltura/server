<?php
// NOTE: the script does not restore metadata objects that are related to the deleted metadata profile id

if ($argc < 2)
{
	echo PHP_EOL . ' ---- Restore Deleted Metadata Profile ---- ' . PHP_EOL;
	echo ' Execute: php ' . $argv[0] . ' [ /path/to/metadata_profile_ids || MPID1,MPID2,.. || MDPID ] [realrun / dryrun]' . PHP_EOL;
	die(' Error: missing metadata_profile_id file, csv or single id ' . PHP_EOL . PHP_EOL);
}

if (is_file($argv[1]))
{
	$metadataProfileIds = file($argv[1]) or die (' Error: cannot open file at: "' . $argv[1] .'"' . PHP_EOL);
}
elseif (strpos($argv[1], ','))
{
	$metadataProfileIds = explode(',', $argv[1]);
}
elseif (is_numeric($argv[1]))
{
	$metadataProfileIds[] = $argv[1];
}
else
{
	die (' Error: invalid input supplied at: "' . $argv[1] . '"' . PHP_EOL);
}

require_once (dirname(__FILE__) . './../bootstrap.php');

$dryRun = true;

$dryRun = true;
if (isset($argv[2]) && $argv[2] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$totalCount = count($metadataProfileIds);
$successfullyRestored = 0;

foreach ($metadataProfileIds as $metadataProfileId)
{
	$metadataProfileId = trim($metadataProfileId);

	MetadataProfilePeer::setUseCriteriaFilter(false);
	$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
	MetadataProfilePeer::setUseCriteriaFilter(true);

	if (!$metadataProfile || $metadataProfile->getStatus() == MetadataProfile::STATUS_ACTIVE)
	{
		KalturaLog::debug('Metadata Profile ID: ' . $metadataProfileId . ' either not found or not DEPRECATED');
		continue;
	}

	$metadataProfileSyncKey = $metadataProfile->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);

	KalturaLog::debug('File Sync Key: ' . $metadataProfileSyncKey);

	FileSyncPeer::setUseCriteriaFilter(false);
	$fileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($metadataProfileSyncKey);
	FileSyncPeer::setUseCriteriaFilter(true);

	// restore MP file syncs, need at least 1 successful file sync to be restored otherwise can't restore MP
	$fileSyncRestored = false;
	foreach ($fileSyncs as $fileSync)
	{
		/* @var FileSync $fileSync */
		if ($fileSync->getStatus() == FileSync::FILE_SYNC_STATUS_DELETED)
		{
			if ($fileSync->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_FILE)
			{
				if (kFile::checkFileExists($fileSync->getFullPath()))
				{
					$fileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
					$fileSync->save();
					KalturaLog::debug('File Sync ID: ' . $fileSync->getId() . ' have been restored');
					$fileSyncRestored = true;
				}
			}
			else
			{
				KalturaLog::debug('File Sync ID: ' . $fileSync->getId() . ' cannot be restored');
			}
		}
	}

	MetadataProfileFieldPeer::setUseCriteriaFilter(false);
	$metadataProfileFields = MetadataProfileFieldPeer::retrieveByMetadataProfileId($metadataProfile->getId());
	MetadataProfileFieldPeer::setUseCriteriaFilter(true);

	// restore MP fields , need at least 1 successful MP field otherwise can't restore MP
	$metadataProfileFieldRestored = false;
	foreach ($metadataProfileFields as $metadataProfileField)
	{
		/* @var MetadataProfileField $metadataProfileField */
		if ($metadataProfileField->getStatus() == MetadataProfileField::STATUS_DEPRECATED)
		{
			$metadataProfileField->setStatus(MetadataProfileField::STATUS_ACTIVE);
			$metadataProfileField->save();
			KalturaLog::debug('Metadata Profile Field ID: ' . $metadataProfileField->getId() . ' have been restored');
			$metadataProfileFieldRestored = true;
		}
		else
		{
			KalturaLog::debug('Metadata Profile Field ID: ' . $metadataProfileField->getId() . ' cannot be restored');
		}
	}

	if ($fileSyncRestored && $metadataProfileFieldRestored)
	{
		$metadataProfile->setStatus(MetadataProfile::STATUS_ACTIVE);
		$metadataProfile->save();
		KalturaLog::debug('Metadata Profile ID: ' . $metadataProfile->getId() . ' status set ACTIVE');
		$successfullyRestored++;
	}
	else
	{
		KalturaLog::debug('Metadata Profile ID: ' . $metadataProfile->getId() . ' could not be restored');

		if (!$fileSyncRestored)
		{
			KalturaLog::debug('Metadata Profile ID: ' . $metadataProfile->getId() . ' could not restore file_sync: either not found or not in status 3 (DELETED)');
		}

		if (!$metadataProfileFieldRestored)
		{
			KalturaLog::debug('Metadata Profile ID: ' . $metadataProfile->getId() . ' could not restore metadata_profile_field: either not found or not in status 2 (DEPRECATED)');
		}
	}

	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();

	if ($successfullyRestored > 0 && $successfullyRestored % 10 == 0)
	{
		KalturaLog::debug('Currently restored ' . $successfullyRestored . ' out of: ' . $totalCount);
		KalturaLog::debug('Sleeping for 15 sec');
		sleep(15);
	}
}

KalturaLog::debug('Script finished');

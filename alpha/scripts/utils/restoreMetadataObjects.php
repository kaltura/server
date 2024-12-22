<?php
ini_set('memory_limit','1024M');
if ($argc < 3)
{
	echo PHP_EOL . ' ---- Restore Deleted Metadata Profile && Objects ---- ' . PHP_EOL;
	echo ' Execute: php ' . $argv[0] . ' partnerId, metadata profile ID, [ /path/to/metadata object IDs list || metadataObjectId_1,metadataObjectId_2,.. || metadataObject ] [realrun / dryrun]' . PHP_EOL;
	die(' Error: missing metadataObjectIds file, csv or single entry ' . PHP_EOL . PHP_EOL);
}

$partnerId = $argv[1];
$metadataProfileId = $argv[2];
$metadataObjectIds = explode(',', $argv[3]);
$totalMetadataObjectIds = count($metadataObjectIds);


require("/opt/kaltura/app/alpha/scripts/bootstrap.php");

$dryRun = true;
if (isset($argv[3]) && $argv[3] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$mdVersion = restoreMDProfile($metadataProfileId ,$partnerId);
restoreMDObjects($metadataObjectIds, $mdVersion, $partnerId);

function restoreMDProfile($metadataProfileId ,$partnerId)
{
	MetadataProfilePeer::setUseCriteriaFilter(false);
	$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
	MetadataProfilePeer::setUseCriteriaFilter(true);

	if (!$metadataProfile)
	{
		KalturaLog::debug("ERR1 - metadata profile id " . $metadataProfileId . " not found");
		exit();
	}

	$mdVersion = $metadataProfile->getVersion();
	$fileSyncs = getFileSyncs($metadataProfileId, $mdVersion, $partnerId);

	foreach ($fileSyncs as $fileSync)
	{
		$fileSyncId = $fileSync->getId();
		$dc = $fileSync->getDc();
		KalturaLog::debug('Saving file sync ID: ' . $fileSyncId . ' - version - ' . $mdVersion . ' - dc - ' . $dc);
		$fileSync->setStatus(2);
		$fileSync->save();
	}

	KalturaLog::debug('Saving metadata profile ID: ' . $metadataProfileId);
	$metadataProfile->setStatus(1);
	$metadataProfile->save();

	return $mdVersion;
}

function restoreMDObjects($metadataObjectIds, $mdVersion, $partnerId)
{
	foreach ($metadataObjectIds as $metadataObjectId)
	{
		$metadataObjectId = trim($metadataObjectId);
		KalturaLog::debug("=== Metadata Object Id: " . $metadataObjectId);
		MetadataPeer::setUseCriteriaFilter(false);
		$metadataObject = MetadataPeer::retrieveByPK($metadataObjectId);
		MetadataPeer::setUseCriteriaFilter(true);

		/* @var $metadataObjectId metadata */

		if (!$metadataObjectId)
		{
			KalturaLog::debug("ERR3 - metadata object id " . $metadataObjectId . " not found");
			continue;
		}

		KalturaLog::debug('=== Restoring metadata object id [' . $metadataObject->getID() . ']');
		$mdOProfileVersion = $metadataObject->getMetadataProfileVersion();

		if ($mdOProfileVersion <> $mdVersion)
		{
			KalturaLog::debug("ERR4 - metadata object version $mdOProfileVersion <> metadata profile version $mdVersion");
			continue;
		}

		if ($metadataObject->getStatus() == 3)
		{
			$mdObjectversion = $metadataObject->getVersion();
			$fileSyncs = getFileSyncs($metadataObjectId, $mdObjectversion, $partnerId);

			foreach ($fileSyncs as $fileSync)
			{
				$fileSyncId = $fileSync->getId();
				$dc = $fileSync->getDc();
				KalturaLog::debug("Saving file sync ID:  $fileSyncId  - version -  $mdObjectversion  - dc -  $dc");
				$fileSync->setStatus(2);
				$fileSync->save();
			}

			KalturaLog::debug("Saving metadata object ID: $metadataObjectId");
			$metadataObject->setStatus(1);
			$metadataObject->save();
		}
	}
}

function getFileSyncs ($objectId , $version, $partnerId)
{
	$c = new Criteria;
	$c->add(FileSyncPeer::PARTNER_ID, $partnerId);
	$c->add(FileSyncPeer::OBJECT_ID, $objectId);
	$c->add(FileSyncPeer::VERSION, $version);
	$c->add(FileSyncPeer::OBJECT_TYPE, 6);
	$c->add(FileSyncPeer::STATUS , 3);

	FileSyncPeer::setUseCriteriaFilter(false);
	$objectFileSyncs = FileSyncPeer::doSelect($c);
	FileSyncPeer::setUseCriteriaFilter(true);

	return $objectFileSyncs;
}

KalturaLog::debug('Script Finished Successfully');

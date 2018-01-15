<?php

require_once(__DIR__ . '/../bootstrap.php');

define('CHUNK_SIZE', 100);		// number of objects to process each time
define('TIME_DELAY', 3600);		// seconds behind real time, in order to handle events that arrive out of order

function getMaxEntryUpdatedAt()
{
	$c = new Criteria(); 
	$c->addDescendingOrderByColumn(entryPeer::UPDATED_AT);
	$entry = entryPeer::doSelectOne($c);
	return $entry->getUpdatedAt(null);
}

function getMaxAssetUpdatedAt()
{
	$c = new Criteria();
	$c->addDescendingOrderByColumn(assetPeer::UPDATED_AT);
	$asset = assetPeer::doSelectOne($c);
	return $asset->getUpdatedAt(null);
}

function getDeletedEntries($minUpdatedAt, $maxUpdatedAt)
{
	$c = new Criteria(); 
	$c->addSelectColumn(entryPeer::ID);
	$c->addSelectColumn(entryPeer::UPDATED_AT);
	$criterion = $c->getNewCriterion(entryPeer::UPDATED_AT, $minUpdatedAt, Criteria::GREATER_EQUAL);
	$criterion->addAnd($c->getNewCriterion(entryPeer::UPDATED_AT, $maxUpdatedAt, Criteria::LESS_THAN));
	$c->addAnd($criterion);
	$c->add(entryPeer::STATUS, entryStatus::DELETED);
	$c->addAscendingOrderByColumn(entryPeer::UPDATED_AT);
	$c->setLimit(CHUNK_SIZE);
		
	entryPeer::setUseCriteriaFilter(false);
	$stmt = entryPeer::doSelectStmt($c);
	entryPeer::setUseCriteriaFilter(true);

	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$ids = array();
	foreach ($rows as $row)
	{
		$ids[] = $row['ID'];
	}
	$lastUpdatedAt = null;
	$lastEntry = end($rows);
	if ($lastEntry)
	{
		$dt = new DateTime($lastEntry['UPDATED_AT']);
		$lastUpdatedAt = (int) $dt->format('U');
	}
	return array($ids, $lastUpdatedAt);
}

function getDeletedAssets($minUpdatedAt, $maxUpdatedAt)
{
	$c = new Criteria();
	$c->addSelectColumn(assetPeer::ID);
	$c->addSelectColumn(assetPeer::VERSION);
	$c->addSelectColumn(assetPeer::UPDATED_AT);
	$criterion = $c->getNewCriterion(assetPeer::UPDATED_AT, $minUpdatedAt, Criteria::GREATER_EQUAL);
	$criterion->addAnd($c->getNewCriterion(assetPeer::UPDATED_AT, $maxUpdatedAt, Criteria::LESS_THAN));
	$c->addAnd($criterion);
	$c->add(assetPeer::STATUS, asset::ASSET_STATUS_DELETED);
	$c->addAscendingOrderByColumn(assetPeer::UPDATED_AT);
	$c->setLimit(CHUNK_SIZE);

	assetPeer::setUseCriteriaFilter(false);
	$stmt = assetPeer::doSelectStmt($c);
	assetPeer::setUseCriteriaFilter(true);

	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$groupedIds = array();
	foreach ($rows as $row)
	{
		$id = $row['ID'];
		$version = $row['VERSION'];
		if (!isset($groupedIds[$version]))
		{
			$groupedIds[$version] = array();
		}
		$groupedIds[$version][] = $id;
	}
	$lastUpdatedAt = null;
	$lastAsset = end($rows);
	if ($lastAsset)
	{
		$dt = new DateTime($lastAsset['UPDATED_AT']);
		$lastUpdatedAt = (int) $dt->format('U');
	}
	return array($groupedIds, $lastUpdatedAt);
}

function getNonDeletedAssets($entryIds)
{
	$c = new Criteria(); 
	$c->add(assetPeer::ENTRY_ID, $entryIds, Criteria::IN); 
	return assetPeer::doSelect($c);	
}

function getNonDeletedFileSyncs($assetIds, $version)
{
	$c = new Criteria(); 
	$c->addAnd(FileSyncPeer::OBJECT_ID, $assetIds, Criteria::IN);
	$c->addAnd(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
	$c->addAnd(FileSyncPeer::OBJECT_SUB_TYPE, asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	$c->addAnd(FileSyncPeer::VERSION, $version, is_null($version) ? Criteria::ISNULL : Criteria::EQUAL);
	return FileSyncPeer::doSelect($c);
}

// parse the command line
if ($argc < 2)
{
	die("Usage:\n\t" . basename(__file__) . " <state file> [real-run]\n");
}

$stateFile = $argv[1];
if ($argc < 3 || $argv[2] != 'real-run')
{
	KalturaStatement::setDryRun(true);
}

// get last updated at
$maxEntryUpdatedAt = getMaxEntryUpdatedAt();
if (!$maxEntryUpdatedAt)
{
	die("Failed to get last entry updated at\n");
}

$maxAssetUpdatedAt = getMaxAssetUpdatedAt();
if (!$maxAssetUpdatedAt)
{
	die("Failed to get last asset updated at\n");
}

$maxEntryUpdatedAt -= TIME_DELAY;
$maxAssetUpdatedAt -= TIME_DELAY;

// load state
$state = null;
if (file_exists($stateFile))
{
	$state = file_get_contents($stateFile);
	$state = json_decode($state, true);
}

if (!$state)
{
	$state = array();
}

// assets of deleted entries
$lastUpdatedAt = isset($state['entry']) ? $state['entry'] : $maxEntryUpdatedAt - 3600;

for (;;)
{
	list($entryIds, $curUpdatedAt) = getDeletedEntries($lastUpdatedAt, $maxEntryUpdatedAt);
	if (!$entryIds)
	{
		break;
	}

	$assets = getNonDeletedAssets($entryIds);

	foreach ($assets as $asset)
	{
		$asset->setStatus(asset::ASSET_STATUS_DELETED);
		$asset->setDeletedAt(time());
		$asset->save();
	}

	$lastUpdatedAt = $curUpdatedAt;

	if (count($entryIds) < CHUNK_SIZE)
	{
		break;
	}
}

$state['entry'] = $lastUpdatedAt;

// file syncs of deleted assets
$lastUpdatedAt = isset($state['asset']) ? $state['asset'] : $maxAssetUpdatedAt - 3600;

for (;;)
{
	list($groupedIds, $curUpdatedAt) = getDeletedAssets($lastUpdatedAt, $maxAssetUpdatedAt);
	if (!$groupedIds)
	{
		break;
	}

	$assetCount = 0;
	foreach ($groupedIds as $version => $assetIds)
	{
		$assetCount += count($assetIds);
		
		$fileSyncs = getNonDeletedFileSyncs($assetIds, $version);
		
		foreach ($fileSyncs as $fileSync)
		{
			$fileSyncKey = new FileSyncKey();
			$fileSyncKey->object_type = $fileSync->getObjectType();
			$fileSyncKey->object_id = $fileSync->getObjectId();
			$fileSyncKey->version = $fileSync->getVersion();
			$fileSyncKey->object_sub_type = $fileSync->getObjectSubType();
			$fileSyncKey->partner_id = $fileSync->getPartnerId();					
			kFileSyncUtils::deleteSyncFileForKey($fileSyncKey);
		}
	}

	$lastUpdatedAt = $curUpdatedAt;
	
	if ($assetCount < CHUNK_SIZE)
	{
		break;
	}
}

$state['asset'] = $lastUpdatedAt;

// save the state
$state = json_encode($state);
$tempStateFile = $stateFile . '.tmp';
file_put_contents($tempStateFile, $state);
rename($tempStateFile, $stateFile);

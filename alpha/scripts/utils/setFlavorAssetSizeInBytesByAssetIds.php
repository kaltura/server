<?php
// this script only sets 'sizeInBytes' and not 'size' as it was written to update DOCUMENT entries type which
// in production do not get 'size' (which is being set in postConvert) and DOCUMENT entries does not have postConvert job

require_once (dirname(__FILE__) . '/../bootstrap.php');

if ($argc < 2)
{
    echo PHP_EOL . ' ---- Set sizeInBytes for Flavor Assets By Asset IDs ---- ' . PHP_EOL;
    echo ' Execute: php ' . $argv[0] . ' [ /path/to/assets_ids_list || assetId_1,assetId_2,assetId_3,.. ] [realrun / dryrun]' . PHP_EOL;
    die(' Error: missing asset_id file or csv list ' . PHP_EOL . PHP_EOL);
}

if (is_file($argv[1]))
{
    $assetIds = file($argv[1]) or die (' Error: cannot open file at: "' . $argv[1] .'"' . PHP_EOL);
}
elseif (strpos($argv[1], ','))
{
    $assetIds = explode(',', $argv[1]);
}
elseif (strpos($argv[1],'_'))
{
    $assetIds[] = $argv[1];
}
else
{
    die (' Error: invalid input supplied at: "' . $argv[1] . '"' . PHP_EOL);
}

$dryRun = true;
if (isset($argv[2]) && $argv[2] == 'realrun')
{
    $dryRun = false;
}
KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$totalAssets = count($assetIds);
$assetCount = 0;
$sleepTime = 15;

foreach ($assetIds as $assetId)
{
    $assetId = trim($assetId);

    /* @var  asset $flavorAsset */
    $flavorAsset = assetPeer::retrieveById($assetId);
    if (!$flavorAsset)
    {
        KalturaLog::debug('SCRIPT - entry_id: null asset_id: not-found no-asset-for-entry');
        continue;
    }

    $sizeInBytes = $flavorAsset->getSizeInBytes();
    if ($sizeInBytes)
    {
        KalturaLog::debug('SCRIPT - entry_id: ' . $flavorAsset->getEntryId() . ' asset_id: ' . $flavorAsset->getId() . ' sizeInBytes-already-set = ' . $sizeInBytes);
        continue;
    }

    /* @var fileSync $fileSync */
    $fileSync = getFileSync($flavorAsset);
    if (!$fileSync)
    {
        KalturaLog::debug('SCRIPT - entry_id: ' . $flavorAsset->getEntryId() . ' asset_id: ' . $flavorAsset->getId() . ' fileSync-not-found');
        continue;
    }

    $sizeInBytes = $fileSync->getFileSize();
    $flavorAsset->setSizeInBytes($sizeInBytes);
    try
    {
        $flavorAsset->save();
        KalturaLog::debug('SCRIPT - entry_id: ' . $flavorAsset->getEntryId() . ' asset_id: ' . $flavorAsset->getId() . ' successfully-updated-sizeInBytes = ' . $flavorAsset->getSizeInBytes());
    }
    catch (PropelException $e)
    {
        KalturaLog::debug('SCRIPT - entry_id: ' . $flavorAsset->getEntryId() . ' asset_id: ' . $flavorAsset->getId() . ' failed-to-save-asset');
    }
    
    $assetCount++;
    if ($assetCount % 1000 === 0)
    {
        KalturaLog::debug('SCRIPT - sleeping for ' . $sleepTime . ' sec (asset-count / total-assets): ' . $assetCount . '/'. $totalAssets);
        kMemoryManager::clearMemory();
        sleep($sleepTime);
    }
}
KalturaLog::debug(' Script Finished');

/* ===================== FUNCTIONS ===================== */

function getFileSync($flavorAsset)
{
    /* @var asset $flavorAsset */
    $syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
    $c = FileSyncPeer::getCriteriaForFileSyncKey($syncKey);
    $c->addAnd(FileSyncPeer::FILE_SIZE, 0, Criteria::GREATER_THAN);
    $c->addAscendingOrderByColumn(FileSyncPeer::DC);
    return FileSyncPeer::doSelectOne($c);
}

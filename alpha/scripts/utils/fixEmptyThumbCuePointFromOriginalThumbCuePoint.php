<?php
// This script handles a case where copyCuePoints did not work as expected
// If cue_points where created on destination entry (clip / trim / live_vod) but do not have the thumb_asset content.
// Meaning, thumb_asset does not have a file sync - this script will fetch the original thumb_asset linked to the original
// cue point from source entry (Live / VOD) and will update it on the matching cue_point in the destination entry

// TODO: switch
require_once ('/opt/kaltura/app/alpha/scripts/bootstrap.php');
//require_once (__DIR__ . '/../bootstrap.php');

if ($argc < 2)
{
    echo PHP_EOL . '    ---- Fix Empty Thumb Cue Points ---- ' . PHP_EOL;
    echo '  Execute: php ' . $argv[0] . ' [Clipped entry_id / Live VOD entry_id] [realrun / dryrun]' . PHP_EOL;
    die(' Missing arguments: please provide the clip or live vod entry id' . PHP_EOL);
}

if (preg_match('/[10]_/', $argv[1]))
{
    $entryId = trim($argv[1]);
}
else
{
    die (' Error: entry_id should start with either \'0_\' or \'1_\'' . PHP_EOL);
}

$dryRun = true;
if (isset($argv[2]) && $argv[2] === 'realrun')
{
    $dryRun = false;
}
KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$entry = entryPeer::retrieveByPK($entryId);
if (!$entry)
{
    die(' Error: could not find entry Id: ' . $entryId . ' - maybe it was deleted?');
}

// note: init kCorrentContext to keep correlation with plugins/cue_points/sphinx/lib/SphinxCuePointCriteria.php:94
// function: translateSphinxCriterion() when building criterion for CuePointPeer::TYPE
kCurrentContext::initPartnerByEntryId($entryId);

$criteria = KalturaCriteria::create(CuePointPeer::OM_CLASS);
$criteria->add(CuePointPeer::PARTNER_ID, $entry->getPartnerId(), Criteria::EQUAL);
$criteria->add(CuePointPeer::STATUS, CuePointStatus::READY, Criteria::EQUAL);
$criteria->add(CuePointPeer::ENTRY_ID, $entry->getId(), Criteria::EQUAL);
$criteria->add(CuePointPeer::TYPE, ThumbCuePointPlugin::getCuePointTypeCoreValue(ThumbCuePointType::THUMB), Criteria::EQUAL);
$criteria->addAscendingOrderByColumn(CuePointPeer::START_TIME);
$criteria->addAscendingOrderByColumn(CuePointPeer::CREATED_AT);
$thumbCuePoints = CuePointPeer::doSelect($criteria);

foreach ($thumbCuePoints as $thumbCuePoint)
{
    /* @var ThumbCuePoint $thumbCuePoint */
    // this is the VOD / Clipped / Trimmed entry thumbnail asset that needs to be updated with original thumb content
    $destinationThumbAssetId = $thumbCuePoint->getAssetId();
    $destinationThumbAsset = assetPeer::retrieveById($destinationThumbAssetId);
    if (!$destinationThumbAsset)
    {
        KalturaLog::debug(' SCRIPT - retrieveDestinationAsset - thumbCuePointId: ' . $thumbCuePoint->getId() . ' destinationThumbAssetId: ' . $destinationThumbAssetId . ' cannot_be_found skipping');
        continue;
    }

    $originalThumbAssetId = null;

    // this is the cue point from which the current $thumbCuePoint has been copied from
    $copiedFromCuePointId = $thumbCuePoint->getCopiedFrom();
    $originalCuePointId = $copiedFromCuePointId;

    // trace all 'copiedFrom' until finding the root / original cue point we copied from and grab the thumbAssetId
    while ($copiedFromCuePointId)
    {
        /* @var ThumbCuePoint $copiedFromCuePoint */
        $copiedFromCuePoint = CuePointPeer::retrieveByPKNoFilter($copiedFromCuePointId);
        if ($copiedFromCuePoint)
        {
            $copiedFromCuePointId = $copiedFromCuePoint->getCopiedFrom();
            if ($copiedFromCuePointId)
            {
                $originalCuePointId = $copiedFromCuePointId;
            }
            else
            {
                // when we got to the original cue point (no 'copiedFrom' on custom_data) get the original thumbAssetId
                $originalThumbAssetId = $copiedFromCuePoint->getAssetId();
            }
        }
    }

    if (!$originalThumbAssetId)
    {
        KalturaLog::debug(' SCRIPT - originalThumbAssetId - thumbCuePointId: ' . $thumbCuePoint->getId() . ' originalThumbAssetId: null originalThumbAssetId_cannot_be_found skipping');
        continue;
    }

    $originalThumbAsset = assetPeer::retrieveById($originalThumbAssetId);
    if (!$originalThumbAsset)
    {
        KalturaLog::debug(' SCRIPT - retrieveOriginalAsset - thumbCuePointId: ' . $thumbCuePoint->getId() . ' originalThumbAssetId: ' . $originalThumbAssetId . ' cannot_be_found skipping');
        continue;
    }

    // if we got here - we have the data we need to update file sync of destinationThumbAsset from originalThumbAsset
    KalturaLog::debug(' SCRIPT - setContent - thumbCuePointId: ' . $thumbCuePoint->getId() . ' destinationThumbAssetId: ' . $destinationThumbAssetId . ' originalThumbAssetId: ' . $originalThumbAssetId);

    $syncable = kFileSyncObjectManager::retrieveObject(FileSyncObjectType::ASSET, $originalThumbAssetId);
    $srcSyncKey = $syncable->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET, $originalThumbAsset->getVersion());

    $destinationThumbAsset->incrementVersion();
    $destinationThumbAsset->save();

    $newSyncKey = $destinationThumbAsset->getSyncKey(thumbAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
    kFileSyncUtils::createSyncFileLinkForKey($newSyncKey, $srcSyncKey);

    list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($newSyncKey, false, false);

    if (!$fileSync)
    {
        KalturaLog::debug(' SCRIPT - newFileSyncNotFound - thumbCuePointId: ' . $thumbCuePoint->getId() . ' destinationThumbAssetId: ' . $destinationThumbAssetId . ' originalThumbAssetId: ' . $originalThumbAssetId);
        continue;
    }

    list($width, $height, $type, $attr) = kImageUtils::getImageSize($fileSync);

    $destinationThumbAsset->setWidth($width);
    $destinationThumbAsset->setHeight($height);
    $destinationThumbAsset->setSize($fileSync->getFileSize());

    $destinationThumbAsset->setStatusLocalReady();
    $destinationThumbAsset->save();

    kEventsManager::flushEvents();
    kMemoryManager::clearMemory();
}
?>



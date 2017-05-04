<?php

if($argc != 3)
{
    echo "Arguments missing.\n\n";
    echo "Usage: php 2017_04_27_clear_rtsp_broadcast_url_from_custom_data.php {entry list file path} {dryRun/realRun}\n";
    exit;
}

function clearCustomDataByEntryIds(array $entryIds, $dryRun)
{
    $c = new Criteria();
    entryPeer::setUseCriteriaFilter(false);
    $c->add(entryPeer::TYPE, entryType::LIVE_STREAM);
    $c->add(entryPeer::STATUS, entryStatus::DELETED, KalturaCriteria::NOT_EQUAL);
    $c->add(entryPeer::ID, $entryIds, Criteria::IN);
    $entries = entryPeer::doSelect($c);

    foreach($entries as $entry)
    {
        /** @var LiveStreamEntry $entry */
        $primaryRtspBroadcastingUrl = $entry->getFromCustomData('primaryRtspBroadcastingUrl');
        $shouldSave = false;
        if($primaryRtspBroadcastingUrl)
        {
            echo 'entry id['.$entry->getId().'] primaryRtspBroadcastingUrl ['.$primaryRtspBroadcastingUrl."]\n";
            $entry->removeFromCustomData('primaryRtspBroadcastingUrl');
            $shouldSave = true;
            echo 'entry id['.$entry->getId().'] new primaryRtspBroadcastingUrl ['.$entry->getFromCustomData('primaryRtspBroadcastingUrl')."]\n";
        }
        $secondaryRtspBroadcastingUrl = $entry->getFromCustomData('secondaryRtspBroadcastingUrl');
        if($secondaryRtspBroadcastingUrl)
        {
            echo 'entry id['.$entry->getId().'] secondaryRtspBroadcastingUrl ['.$secondaryRtspBroadcastingUrl."]\n";
            $entry->removeFromCustomData('secondaryRtspBroadcastingUrl');
            $shouldSave = true;
            echo 'entry id['.$entry->getId().'] new secondaryRtspBroadcastingUrl ['.$entry->getFromCustomData('secondaryRtspBroadcastingUrl')."]\n";
        }

        if($dryRun == 'realRun' && $shouldSave)
        {
            try{
                $entry->save();
                echo 'updated entry id ['.$entry->getId()."] custom data\n";
            }
            catch(Exception $e){
                echo 'could not save entry['.$entry->getId().'] exception: ',  $e->getMessage(), "\n";
            }
        }
    }
    entryPeer::clearInstancePool();
}

$entriesFilePath = $argv[1];
$dryRun = $argv[2];

$entryIds = file ( $entriesFilePath ) or die ( 'Could not read file!' );

require_once(__DIR__ . '/../../bootstrap.php');

$entryBulkSize = 100;
$currentEntryIds = array();
$entryCount = 0;

foreach ($entryIds as $liveEntryId)
{
    $liveEntryId = trim($liveEntryId);
    $currentEntryIds[] = $liveEntryId;
    $entryCount++;

    if($entryCount % $entryBulkSize == 0)
    {
        clearCustomDataByEntryIds($currentEntryIds, $dryRun);
        $currentEntryIds = array();
    }
}
if(count($currentEntryIds) > 0)
    clearCustomDataByEntryIds($currentEntryIds, $dryRun);

echo "DONE\n";

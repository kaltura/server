<?php

if($argc != 2)
{
    echo "Arguments missing.\n\n";
    echo "Usage: php 2017_04_27_clear_rtsp_broadcast_url_from_custom_data.php {dryRun/realRun}\n";
    exit;
}

$dryRun = $argv[1];

require_once(__DIR__ . '/../../bootstrap.php');
ini_set("memory_limit","1024M");

$lastEntry = 0;
while(1)
{
    $c = new Criteria();
    entryPeer::setUseCriteriaFilter(false);
    $c->add(entryPeer::TYPE, entryType::LIVE_STREAM);
    $c->add(entryPeer::STATUS, entryStatus::DELETED, KalturaCriteria::NOT_EQUAL);
    $c->add(entryPeer::INT_ID, $lastEntry, Criteria::GREATER_THAN);
    $c->addAscendingOrderByColumn(entryPeer::INT_ID);
    $c->setLimit(100);
    $entries = entryPeer::doSelect($c);

    if (!count($entries))
    {
        echo "DONE!\n";
        break;
    }

    $currentEntryIntId = null;
    foreach($entries as $entry)
    {
        /** @var LiveStreamEntry $entry */
        $currentEntryIntId = $entry->getIntId();
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
            $entry->save();
            echo 'updated entry id ['.$entry->getId()."] custom data\n";
        }
    }
    $lastEntry = $currentEntryIntId;
    entryPeer::clearInstancePool();
}


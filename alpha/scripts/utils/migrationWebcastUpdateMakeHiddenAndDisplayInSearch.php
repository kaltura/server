<?php

require_once(__DIR__ . '/../bootstrap.php');

const UPDATE_VOD_CHUNK_SIZE = 50;

if (count($argv) < 3)
    die ("usage: php migrationWebcastUpdateMakeHiddenAndDisplayInSearch.php input_file debug|realrun");

$partnersFilePath = $argv[1];
$partners = file($partnersFilePath) or die('Could not read file [$partnersFilePath]!');

if (!in_array($argv[2], array("debug","realrun")))
    die ("usage: php migrationWebcastUpdateMakeHiddenAndDisplayInSearch.php input_file debug|realrun");

$dryRun = ($argv[2] == "debug");
if ($dryRun)
    KalturaLog::debug('this is a dry run!');

foreach ($partners as $partnerId)
{
    $partnerId = trim($partnerId);
    KalturaLog::debug("running script for partner [" . $partnerId . "]");
    $partner = PartnerPeer::retrieveByPK($partnerId);
    if(!$partner)
    {
        KalturaLog::warning("partner [ " . $partnerId . "] does not exist. Skipping it");
        continue;
    }

    $vodEntriesToUpdate = array();

    $c = new Criteria();
    $c->addAnd(entryPeer::PARTNER_ID, $partnerId);
    $c->addAnd(entryPeer::SOURCE, EntrySourceType::LIVE_STREAM);
    $c->addAnd(entryPeer::STATUS, entryStatus::DELETED, Criteria::NOT_EQUAL);

    $entries = entryPeer::doSelect($c);

    KalturaLog::debug("got " . count($entries) . " live entries for partner " . $partnerId);
    foreach ($entries as $liveEntry)
    {
        KalturaLog::debug("Starting to work on Entry [" . $liveEntry->getId() . "]");
        $recordingOptions = $liveEntry->getRecordingOptions();
        if ($recordingOptions)
        {
            $tmpRecordedEntryId =  $liveEntry->getRecordedEntryId();
            $tmpRedirectEntryId =  $liveEntry->getRedirectEntryId();

            KalturaLog::debug("calling setShouldMakeHidden with true on Entry [" . $liveEntry->getId() . "]");
            if (!$dryRun){

                $recordingOptions->setShouldMakeHidden(true);
                $liveEntry->setRecordingOptions($recordingOptions);
                $liveEntry->save();

                // setting the recording options will clear the RecordedEntryId && RedirectEntryId.
                // Set them back since we want this connection and also need it for the 2nd part of the script
                $liveEntry->setRecordedEntryId($tmpRecordedEntryId);
                $liveEntry->setRedirectEntryId($tmpRedirectEntryId);
                $liveEntry->save();
            }

            // if we have a vod store it for later. we will want to set it's display_in_search to EntryDisplayInSearchType::SYSTEM
            if ($tmpRecordedEntryId)
            {
                $vodEntriesToUpdate[] = $tmpRecordedEntryId;
            }
        }
        else
        {
            KalturaLog::debug("recordingOptions for entry [" . $liveEntry->getId() . "] is null - not changing it");
        }
    }

    KalturaLog::debug("need to update " . count($vodEntriesToUpdate) . " VOD entries. Using chunk size of " . UPDATE_VOD_CHUNK_SIZE);
    $vodEntriesChunks = array_chunk($vodEntriesToUpdate, UPDATE_VOD_CHUNK_SIZE);
    foreach($vodEntriesChunks as $chunk)
    {
        updateVodEntriesDisplayInSearchToSYSTEM($partnerId, $chunk, $dryRun);
    }
}

function updateVodEntriesDisplayInSearchToSYSTEM($partnerId, $entryIds, $dryRun)
{
    KalturaLog::debug("going to work on entries: " . implode($entryIds,","));

    $c1 = new Criteria();
    $c1->addAnd(entryPeer::PARTNER_ID, $partnerId);
    $c1->addAnd(entryPeer::ID, $entryIds, Criteria::IN);
    $c1->addAnd(entryPeer::SOURCE, EntrySourceType::RECORDED_LIVE);
    $c1->addAnd(entryPeer::STATUS, entryStatus::DELETED, Criteria::NOT_EQUAL);

    $vodEntries = entryPeer::doSelect($c1);

    foreach ($vodEntries as $vodEntry)
    {
        KalturaLog::debug("Starting to work on VOD Entry [" . $vodEntry->getId() . "]");
        if (!$dryRun)
        {
            $vodEntry->setDisplayInSearch(EntryDisplayInSearchType::SYSTEM);
            $vodEntry->save();
        }
    }
}
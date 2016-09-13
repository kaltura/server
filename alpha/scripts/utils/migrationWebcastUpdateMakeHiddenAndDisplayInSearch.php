<?php

require_once(__DIR__ . '/../bootstrap.php');

if (count($argv) < 2)
{
    die('pleas provide partner id as input' . PHP_EOL .
        'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL .
        'whereas X is the path to the partner ids file' . PHP_EOL);
}

$dryRun = !(count($argv) == 3 && @$argv[2] == "realrun");
if ($dryRun)
{
    KalturaLog::debug('this is a dry run. pass "realrun" as 2nd param to make it actually do stuff');
}

$partners = file(@$argv[1]);
foreach ($partners as $partnerId)
{
    $partnerId = trim($partnerId);
    KalturaLog::debug("running script for partner [" . $partnerId . "]");
    $partner = PartnerPeer::retrieveByPK($partnerId);
    if(!$partner)
    {
        die('no such partner.'.PHP_EOL);
    }

    // entryId => Entry
    $liveEntries = array();

    $c = new Criteria();
    $c->add(entryPeer::PARTNER_ID, $partnerId);
    $c->add(entryPeer::SOURCE, EntrySourceType::LIVE_STREAM);

    $con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
    $entries = entryPeer::doSelect($c, $con);

    KalturaLog::debug("got " . count($entries) . " live entries for partner " . $partnerId);
    foreach ($entries as $liveEntry)
    {
        KalturaLog::debug("Starting to work on Entry [" . $liveEntry->getId() . "]");
        $recordingOptions = $liveEntry->getRecordingOptions();
        if ($recordingOptions)
        {
            KalturaLog::debug("calling setShouldMakeHidden with true");
            if (!$dryRun){

                $tmpRecordedEntryId =  $liveEntry->getRecordedEntryId();
                $tmpRedirectEntryId =  $liveEntry->getRedirectEntryId();

                $recordingOptions->setShouldMakeHidden(true);
                $liveEntry->setRecordingOptions($recordingOptions);
                $liveEntry->save();

                // setting the recording options will clear the RecordedEntryId && RedirectEntryId.
                // Set them back since we want this connection and also need it for the 2nd part of the script
                $liveEntry->setRecordedEntryId($tmpRecordedEntryId);
                $liveEntry->setRedirectEntryId($tmpRedirectEntryId);
                $liveEntry->save();
            }
            $liveEntries[$liveEntry->getId()] = $liveEntry;
        }
        else
        {
            KalturaLog::debug("recordingOptions for entry [" . $liveEntry->getId() . "] is null - not changing it");
        }
    }

    $c1 = new Criteria();
    $c1->add(entryPeer::PARTNER_ID, $partnerId);
    $c1->add(entryPeer::SOURCE, EntrySourceType::RECORDED_LIVE);

    $con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
    $vodEntries = entryPeer::doSelect($c1, $con);

    KalturaLog::debug("got " . count($vodEntries) . " VOD entries for partner " . $partnerId);
    foreach ($vodEntries as $vodEntry)
    {
        KalturaLog::debug("Starting to work on VOD Entry [" . $vodEntry->getId() . "]");
        $rootEntryId = $vodEntry->getRootEntryId();
        KalturaLog::debug("rootEntryId is [" . $rootEntryId . "]");

        if (!empty($rootEntryId))
        {
            $rootEntry = array_key_exists($rootEntryId, $liveEntries) ? $liveEntries[$rootEntryId] : null;
            if ($rootEntry)
            {
                $rootEntryRecordedEntryId = $rootEntry->getRecordedEntryId();
                KalturaLog::debug("rootEntryRecordedEntryId is [" . $rootEntryRecordedEntryId . "]");

                if ($rootEntryRecordedEntryId == $vodEntry->getId()) {
                    KalturaLog::debug("calling setDisplayInSearch with EntryDisplayInSearchType::SYSTEM");
                    if (!$dryRun) {
                        $vodEntry->setDisplayInSearch(EntryDisplayInSearchType::SYSTEM);
                        $vodEntry->save();
                    }
                }
            }
            else
            {
                KalturaLog::debug("rootEntryId [" . $rootEntryId . "] does not exist in liveEntries array");
            }
        }
        else
        {
            KalturaLog::debug("No rootEntyId or it does not point to this entry");
        }
    }
}

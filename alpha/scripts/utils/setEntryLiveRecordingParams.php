<?php

if($argc < 2)
{
    echo "Arguments missing.\n\n";
    echo "Usage: php " . __FILE__ . " {mapping} <realrun / dryrun> \n";
    exit;
}
$mapping = $argv[1];
$dryRun = ($argv[2] === "dryrun");

require_once(__DIR__ . '/../bootstrap.php');

KalturaStatement::setDryRun($dryRun);

$handle = fopen($mapping, 'r');

$entryMapping = fgetcsv($handle);
while ($entryMapping !== false)
{
    list ($entryId,$liveEntryId) = $entryMapping;

    $entry = entryPeer::retrieveByPK($entryId);
    if(!$entry)
    {
        echo "Entry id [$entryId] not found\n";
        continue;
    }

    $entry->setIsRecordedEntry(true);
    $entry->setRootEntryId($liveEntryId);
    $entry->save();

    kEventsManager::flushEvents();
    kMemoryManager::clearMemory();

    $entryMapping = fgetcsv($handle);
}

fclose($handle);
KalturaLog::debug('Done');

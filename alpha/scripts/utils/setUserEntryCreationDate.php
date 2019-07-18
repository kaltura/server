<?php

if($argc < 3)
{
    echo "Arguments missing.\n\n";
    echo "Usage: php " . __FILE__ . " {mapping} {isDateString} <realrun / dryrun> \n";
    exit;
}
$mapping = $argv[1];
$isDateString = ($argv[2] === "true");
$dryRun = ($argv[3] === "dryrun");

require_once(__DIR__ . '/../bootstrap.php');

KalturaStatement::setDryRun($dryRun);

$userEntryMappings = file($mapping, FILE_IGNORE_NEW_LINES);

foreach ($userEntryMappings as $userEntryMapping)
{
    list ($userEntryId,$createdAt) = explode(",", $userEntryMapping);
    $createdAt = $isDateString ? strtotime($createdAt) : $createdAt;

    $userEntry = UserEntryPeer::retrieveByPK($userEntryId);


    if(!$userEntry)
    {
        echo "UserEntry id [$userEntryId] not found\n";
        continue;
    }
    try
    {
        $userEntry->setOriginalCreationDate($userEntry->getCreatedAt());
        $userEntry->setCreatedAt($createdAt);
        $userEntry->setUpdatedAt($createdAt);
        $userEntry->save();
    }
    catch(Exception $e)
    {
        echo "<br>ERROR userEntryId: ".$userEntryId ."  -  ". $e->getMessage()."<br>";

    }

    kEventsManager::flushEvents();
    kMemoryManager::clearMemory();
}

KalturaLog::debug('Done');

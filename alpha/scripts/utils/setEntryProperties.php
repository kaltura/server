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

$entryMappings = file($mapping, FILE_IGNORE_NEW_LINES);

$properties = [""];
$counter = 0;
entry::setAllowOverrideReadOnlyFields(true);
foreach ($entryMappings as $entryMapping)
{
    if($counter++ == 0){
        $entryColumns = explode(",", $entryMapping);
        foreach($entryColumns as $property){
            if($property == 'id' || $property == 'entryId'){
                continue;
            }
            if(!method_exists('entry', "set".ucfirst($property))){
                echo "Property $property does not have the set function \n";
                exit;
            }
            $properties[] = $property;
        }
        continue;
    }
    var_dump($properties);
    $entryValues= explode(",", $entryMapping);

    $z = 0;
    foreach($entryValues as $entryValue){
        if($z === 0){
            $z++;
            $entry = entryPeer::retrieveByPK($entryValue);
            if(!$entry) {
                echo "Entry id [$entryValue] not found\n";
                break;
            }
            continue;
        }
        $isDateValue = false;
        if($isDateString){
            $needles = ["createdAt", "updatedAt"];
            foreach ($needles as $needle) {
                if (strpos($properties[$z], $needle) === false) {
                    $isDateValue = true;
                    break;
                }
            }
        }
        $entryValue = $isDateValue ? strtotime($entryValue) : $entryValue;
        /** @var $entry entry */
        $entry->{"set".ucfirst($properties[$z])}($entryValue);
        $entry->save();
        kEventsManager::flushEvents();
        kMemoryManager::clearMemory();
        $z++;
    }
}

KalturaLog::debug('Done');

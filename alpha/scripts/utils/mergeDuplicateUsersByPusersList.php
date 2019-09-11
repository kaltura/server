<?php
require_once(__DIR__ . '/../bootstrap.php');
require_once(__DIR__ . '/mergeDuplicateUsersUtils.php');
if ($argc < 3)
    die("Usage: php mergeDuplicateUsersByPusersList.php partnerId pusersIdsFilePath <realrun | dryrun>"."\n");

$partnerId = $argv[1] ;
$pusersFilePath = $argv[2];
$dryrun = true;
if($argc == 4 && $argv[3] == 'realrun')
    $dryrun = false;
KalturaStatement::setDryRun($dryrun);
KalturaLog::debug('dryrun value: ['.$dryrun.']');
$pusers = file ($pusersFilePath) or die ('Could not read file'."\n");

foreach ($pusers as $puserId) {
    $puserId = trim($puserId);
    $kusersArray = getAllDuplicatedKusersForPuser ($puserId, $partnerId);
    if (!$kusersArray){
        KalturaLog::debug('ERROR: couldn\'t find kusers with puser id ['.$puserId.']');
        continue;
    }
    $baseKuser = findKuserWithMaxEntries($kusersArray, $partnerId);
    mergeUsersToBaseUser($kusersArray, $baseKuser, $partnerId);
    KalturaLog::debug('finished handling puserId ['.$puserId.']');
}


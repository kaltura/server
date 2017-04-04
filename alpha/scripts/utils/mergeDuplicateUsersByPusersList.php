<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
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

function mergeUsersToBaseUser($kusersArray, $baseKuser, $partnerId){
    foreach ($kusersArray as $kuser){
        if($kuser->getId() != $baseKuser->getId()){
            changeKuserForEntries($kuser, $baseKuser, $partnerId);
            changeKuserForCuePoints($kuser, $baseKuser, $partnerId);
            changeKuserForCategoryKusers($kuser, $baseKuser, $partnerId);
            changeKuserForUserEntries($kuser, $baseKuser, $partnerId);
            changeKuserForKuserKgroup($kuser, $baseKuser, $partnerId);
            changeKuserForKvote($kuser, $baseKuser, $partnerId);
            deleteKuser($kuser);
            KalturaLog::debug('finished handling kuserId ['.$kuser->getId().']');
        }
    }
}

function getAllDuplicatedKusersForPuser ($puserId, $partnerId){
    KalturaLog::debug('retriving the kusers for partnerId ['.$partnerId.'] with puserId ['.$puserId.']');
    $Critiria = new Criteria();
    $Critiria->add(kuserPeer::PUSER_ID, $puserId);
    $Critiria->add(kuserPeer::PARTNER_ID, $partnerId);
    return kuserPeer::doSelect($Critiria);
}

function findKuserWithMaxEntries ($kusersArray, $partnerId){
    $baseKuser=null;
    $maxEntriesNum=0;
    foreach ($kusersArray as $kuser){
        $Critiria = KalturaCriteria::create(entryPeer::OM_CLASS);
        $Critiria->add(entryPeer::KUSER_ID, $kuser->getId());
        $Critiria->add(entryPeer::PARTNER_ID, $partnerId);
        $entries = entryPeer::doSelect($Critiria);
        $entriesNum = $Critiria->getRecordsCount();
        KalturaLog::debug('kuserId: ['.$kuser->getId().'] entries num: ['.$entriesNum.']');
        if($entriesNum >= $maxEntriesNum){
            $baseKuser = $kuser;
            $maxEntriesNum = $entriesNum;
        }
    }
    KalturaLog::debug('kuserId: ['.$baseKuser->getId().'] entries num: ['.$maxEntriesNum.'] - max value');
    return $baseKuser;
}

function changeKuserForEntries ($kuser, $baseKuser, $partnerId) {
    $Critiria = KalturaCriteria::create(entryPeer::OM_CLASS);
    $Critiria->add(entryPeer::KUSER_ID, $kuser->getId());
    $Critiria->add(entryPeer::PARTNER_ID, $partnerId);
    $entriesArray = entryPeer::doSelect($Critiria);
    foreach ($entriesArray as $entry) {
        KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$entry->getKuser()->getId().'] for entryId ['.$entry->getId().']');
        $entry->setKuserId($baseKuser->getId());
        $entry->save();
    }
}

function changeKuserForCuePoints ($kuser, $baseKuser, $partnerId)
{
    $Critiria = KalturaCriteria::create(CuePointPeer::OM_CLASS);
    $Critiria->add(CuePointPeer::KUSER_ID, $kuser->getId());
    $Critiria->add(CuePointPeer::PARTNER_ID, $partnerId);
    $cuePointsArray = CuePointPeer::doSelect($Critiria);
    foreach ($cuePointsArray as $cuePoint) {
        KalturaLog::debug('set KuserId [' . $baseKuser->getId() . '] instead of [' . $cuePoint->getKuserId() . '] for cuePointId [' . $cuePoint->getId() . ']');
        $cuePoint->setkuserId($baseKuser->getId());
        $cuePoint->save();
    }
}

function changeKuserForCategoryKusers ($kuser, $baseKuser, $partnerId) {
    $Critiria = new Criteria();
    $Critiria->add(categoryKuserPeer::KUSER_ID, $kuser->getId());
    $Critiria->add(categoryKuserPeer::PARTNER_ID, $partnerId);
    $categoryUserArray = categoryKuserPeer::doSelect($Critiria);
    foreach ($categoryUserArray as $categoryUser) {
        KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$categoryUser->getKuser()->getId().'] for categoryUserId ['.$categoryUser->getId().']');
        $categoryUser->setkuserId($baseKuser->getId());
        $categoryUser->save();
    }
}

function changeKuserForUserEntries($kuser, $baseKuser, $partnerId){
    $Critiria = new Criteria();
    $Critiria->add(UserEntryPeer::KUSER_ID, $kuser->getId());
    $Critiria->add(UserEntryPeer::PARTNER_ID, $partnerId);
    $Critiria->add(UserEntryPeer::STATUS, UserEntryStatus::ACTIVE);
    $userEntryArray = UserEntryPeer::doSelect($Critiria);
    foreach ($userEntryArray as $userEntry) {
        KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$userEntry->getKuser()->getId().'] for userEntry ['.$userEntry->getId().']');
        $userEntry->setkuserId($baseKuser->getId());
        $userEntry->save();
    }
}

function changeKuserForKvote($kuser, $baseKuser, $partnerId){
    $Critiria = new Criteria();
    $Critiria->add(kvotePeer::KUSER_ID, $kuser->getId());
    $Critiria->add(kvotePeer::PARTNER_ID, $partnerId);
    $kvotesArray = kvotePeer::doSelect($Critiria);
    foreach ($kvotesArray as $kvote) {
        KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$kvote->getKuserId().'] for kvote ['.$kvote->getId().']');
        $kvote->setkuserId($baseKuser->getId());
        $kvote->save();
    }
}

function changeKuserForKuserKgroup($kuser, $baseKuser, $partnerId){
    kCurrentContext::$partner_id = $partnerId;
    $Critiria = new Criteria();
    $Critiria->add(KuserKgroupPeer::KUSER_ID, $kuser->getId());
    $Critiria->add(KuserKgroupPeer::PARTNER_ID, $partnerId);
    $kuserKgroups = KuserKgroupPeer::doSelect($Critiria);

    //if we have a row for the kuser_kgroup for the base puser we are deleting the row for the deleted kuser, else we are changing the kuser in the relevant row
    foreach ($kuserKgroups as $kuserKgroup) {
        $C = new Criteria();
        $C->add(KuserKgroupPeer::KUSER_ID, $baseKuser->getId());
        $C->add(KuserKgroupPeer::PARTNER_ID, $partnerId);
        $C->add(KuserKgroupPeer::KGROUP_ID, $kuserKgroup->getKgroupId());
        $sameKgroupForKusers = KuserKgroupPeer::doSelectOne($C);
        if (!$sameKgroupForKusers){
            KalturaLog::debug('couldn\'t find kgroup with id ['.$kuserKgroup->getKgroupId().'] need to associate to one');
            KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$kuserKgroup->getKuserId().'] for kuser_kgroup ['.$kuserKgroup->getId().']');
            $kuserKgroup->setKuserId($baseKuser->getId());
        }
        else{
            KalturaLog::debug('set status ['.KuserKgroupStatus::DELETED.'] instead of ['.$kuserKgroup->getStatus().'] for kuser_kgroup ['.$kuserKgroup->getId().']');
            $kuserKgroup->setStatus(KuserKgroupStatus::DELETED);
        }
        $kuserKgroup->save();
    }
}

function deleteKuser ($kuser){
    KalturaLog::debug('set KuserId ['.$kuser->getId().'] status from ['.$kuser->getStatus().'] to ['.KuserStatus::DELETED.']');
    $kuser->setStatus(KuserStatus::DELETED);
    $kuser->save();
}

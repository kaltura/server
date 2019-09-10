<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');

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
	KalturaLog::debug('retrieving the kusers for partnerId ['.$partnerId.'] with puserId ['.$puserId.']');
	$Criteria = new Criteria();
	$Criteria->add(kuserPeer::PUSER_ID, $puserId);
	$Criteria->add(kuserPeer::PARTNER_ID, $partnerId);
	$Criteria->addAscendingOrderByColumn(kuserPeer::ID);
	$ret = kuserPeer::doSelect($Criteria);
	foreach($ret as $user)
	{
		if($user->getIsAdmin())
		{
			KalturaLog::debug("User $puserId is admin user, script will not handle it");
			return array();
		}
	}

	return $ret;
}

function findKuserWithMaxEntries ($kusersArray, $partnerId){
	$baseKuser=null;
	$maxEntriesNum=0;
	foreach ($kusersArray as $kuser){
		$Criteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$Criteria->add(entryPeer::KUSER_ID, $kuser->getId());
		$Criteria->add(entryPeer::PARTNER_ID, $partnerId);
		$entries = entryPeer::doSelect($Criteria);
		$entriesNum = $Criteria->getRecordsCount();
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
	$Criteria = KalturaCriteria::create(entryPeer::OM_CLASS);
	$Criteria->add(entryPeer::KUSER_ID, $kuser->getId());
	$Criteria->add(entryPeer::PARTNER_ID, $partnerId);
	$entriesArray = entryPeer::doSelect($Criteria);
	foreach ($entriesArray as $entry) {
		KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$entry->getKuser()->getId().'] for entryId ['.$entry->getId().']');
		$entry->setKuserId($baseKuser->getId());
		$entry->save();
	}
}

function changeKuserForCuePoints ($kuser, $baseKuser, $partnerId)
{
	$Criteria = KalturaCriteria::create(CuePointPeer::OM_CLASS);
	$Criteria->add(CuePointPeer::KUSER_ID, $kuser->getId());
	$Criteria->add(CuePointPeer::PARTNER_ID, $partnerId);
	$cuePointsArray = CuePointPeer::doSelect($Criteria);
	foreach ($cuePointsArray as $cuePoint) {
		KalturaLog::debug('set KuserId [' . $baseKuser->getId() . '] instead of [' . $cuePoint->getKuserId() . '] for cuePointId [' . $cuePoint->getId() . ']');
		$cuePoint->setkuserId($baseKuser->getId());
		$cuePoint->save();
	}
}

function changeKuserForCategoryKusers ($kuser, $baseKuser, $partnerId) {
	$Criteria = new Criteria();
	$Criteria->add(categoryKuserPeer::KUSER_ID, $kuser->getId());
	$Criteria->add(categoryKuserPeer::PARTNER_ID, $partnerId);
	$categoryUserArray = categoryKuserPeer::doSelect($Criteria);
	foreach ($categoryUserArray as $categoryUser) {
		KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$categoryUser->getKuser()->getId().'] for categoryUserId ['.$categoryUser->getId().']');
		$categoryUser->setkuserId($baseKuser->getId());
		$categoryUser->save();
	}
}

function changeKuserForUserEntries($kuser, $baseKuser, $partnerId){
	$Criteria = new Criteria();
	$Criteria->add(UserEntryPeer::KUSER_ID, $kuser->getId());
	$Criteria->add(UserEntryPeer::PARTNER_ID, $partnerId);
	$Criteria->add(UserEntryPeer::STATUS, UserEntryStatus::ACTIVE);
	$userEntryArray = UserEntryPeer::doSelect($Criteria);
	foreach ($userEntryArray as $userEntry) {
		KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$userEntry->getKuser()->getId().'] for userEntry ['.$userEntry->getId().']');
		$userEntry->setkuserId($baseKuser->getId());
		$userEntry->save();
	}
}

function changeKuserForKvote($kuser, $baseKuser, $partnerId){
	$Criteria = new Criteria();
	$Criteria->add(kvotePeer::KUSER_ID, $kuser->getId());
	$Criteria->add(kvotePeer::PARTNER_ID, $partnerId);
	$kvotesArray = kvotePeer::doSelect($Criteria);
	foreach ($kvotesArray as $kvote) {
		KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$kvote->getKuserId().'] for kvote ['.$kvote->getId().']');
		$kvote->setkuserId($baseKuser->getId());
		$kvote->save();
	}
}

function changeKuserForKuserKgroup($kuser, $baseKuser, $partnerId){
	kCurrentContext::$partner_id = $partnerId;
	$Criteria = new Criteria();
	$Criteria->add(KuserKgroupPeer::KUSER_ID, $kuser->getId());
	$Criteria->add(KuserKgroupPeer::PARTNER_ID, $partnerId);
	$kuserKgroups = KuserKgroupPeer::doSelect($Criteria);

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
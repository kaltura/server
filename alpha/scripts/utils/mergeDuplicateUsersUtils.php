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
	$Critiria = new Criteria();
	$Critiria->add(kuserPeer::PUSER_ID, $puserId);
	$Critiria->add(kuserPeer::PARTNER_ID, $partnerId);
	$Critiria->addAscendingOrderByColumn(kuserPeer::ID);
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
	KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$kuser->getId().'] for entries');
	$Critiria = KalturaCriteria::create(entryPeer::OM_CLASS);
	$Critiria->add(entryPeer::KUSER_ID, $kuser->getId());
	$Critiria->add(entryPeer::PARTNER_ID, $partnerId);

	$update = new Criteria();
	$update->add(entryPeer::KUSER_ID, $baseKuser->getId());
	$con = Propel::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
	BasePeer::doUpdate($Critiria, $update, $con);
}

function changeKuserForCuePoints ($kuser, $baseKuser, $partnerId)
{
	KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$kuser->getId().'] for cue points');
	$Critiria = KalturaCriteria::create(CuePointPeer::OM_CLASS);
	$Critiria->add(CuePointPeer::KUSER_ID, $kuser->getId());
	$Critiria->add(CuePointPeer::PARTNER_ID, $partnerId);

	$update = new Criteria();
	$update->add(CuePointPeer::KUSER_ID, $baseKuser->getId());
	$con = Propel::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
	BasePeer::doUpdate($Critiria, $update, $con);
}

function changeKuserForCategoryKusers ($kuser, $baseKuser, $partnerId) {
	KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$kuser->getId().'] for categoryKusers');
	$Critiria = new Criteria();
	$Critiria->add(categoryKuserPeer::KUSER_ID, $kuser->getId());
	$Critiria->add(categoryKuserPeer::PARTNER_ID, $partnerId);

	$update = new Criteria();
	$update->add(categoryKuserPeer::KUSER_ID, $baseKuser->getId());
	$con = Propel::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
	BasePeer::doUpdate($Critiria, $update, $con);
}

function changeKuserForUserEntries($kuser, $baseKuser, $partnerId){
	KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$kuser->getId().'] for userEntries');
	$Critiria = new Criteria();
	$Critiria->add(UserEntryPeer::KUSER_ID, $kuser->getId());
	$Critiria->add(UserEntryPeer::PARTNER_ID, $partnerId);
	$Critiria->add(UserEntryPeer::STATUS, UserEntryStatus::ACTIVE);

	$update = new Criteria();
	$update->add(UserEntryPeer::KUSER_ID, $baseKuser->getId());
	$con = Propel::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
	BasePeer::doUpdate($Critiria, $update, $con);
}

function changeKuserForKvote($kuser, $baseKuser, $partnerId){
	KalturaLog::debug('set KuserId ['.$baseKuser->getId().'] instead of ['.$kuser->getId().'] for kVotes');
	$Critiria = new Criteria();
	$Critiria->add(kvotePeer::KUSER_ID, $kuser->getId());
	$Critiria->add(kvotePeer::PARTNER_ID, $partnerId);

	$update = new Criteria();
	$update->add(kvotePeer::KUSER_ID, $baseKuser->getId());
	$con = Propel::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
	BasePeer::doUpdate($Critiria, $update, $con);
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
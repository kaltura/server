<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
echo "Starting script to update membersCount for group\n";




if($argc < 3)
	die("Usage: php $argv[0] <groupID> [<realrun | dryrun>] \n");

$groupId = $argv[1];

$dryrun = true;
if ($argc == 3 && $argv[2] == 'realrun') {
	$dryrun = false;
}

KalturaStatement::setDryRun($dryrun);
KalturaLog::debug("dryrun value: [$dryrun]\n");

$kuser = kuser::getKuserById($groupId);
$currentMembersCount = $kuser->getMembersCount();
KalturaLog::debug("Current members count for group with ID $groupId is $currentMembersCount\n");

$c = new Criteria();
$c->add(BaseKuserKgroupPeer::PARTNER_ID, $kuser->getPartnerId());
$c->add(BaseKuserKgroupPeer::KGROUP_ID, $groupId);
$c->add(BaseKuserKgroupPeer::STATUS,KuserKgroupStatus::ACTIVE, Criteria::EQUAL);
$numOfUsersMembers = BaseKuserKgroupPeer::doCount($c);
KalturaLog::debug("Current user members for group with ID $groupId is $numOfUsersMembers\n");

if ($currentMembersCount == $numOfUsersMembers)
{
	KalturaLog::debug("No update needed, current members count is already $currentMembersCount\n");
	exit(0);
}

$kuser->setMembersCount($numOfUsersMembers);
$kuser->save();

KalturaLog::debug("Updated membersCount from $currentMembersCount to $numOfUsersMembers for group with ID $groupId\n");

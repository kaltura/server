<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
echo "Starting script to update membersCount for group\n";


if ($argc < 2) {
	echo ' Execute: php ' . $argv[0] . ' [ /path/to/group_id_list || groupId_1,groupID_2,.. || group_id ] [realrun / dryrun]' . PHP_EOL;
	die(' Error: missing group_ids file, csv or single group ' . PHP_EOL . PHP_EOL);
}

if (is_file($argv[1])) {
	$groupIds = file($argv[1]) or die (' Error: cannot open file at: "' . $argv[1] . '"' . PHP_EOL);
} elseif (strpos($argv[1], ',')) {
	$groupIds = explode(',', $argv[1]);
} elseif (strpos($argv[1], '_')) {
	$groupIds[] = $argv[1];
} else {
	die (' Error: invalid input supplied at: "' . $argv[1] . '"' . PHP_EOL);
}

$dryRun = true;
if (isset($argv[2]) && $argv[2] == 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$count = 0;
$totalGroups = count($groupIds);

foreach ($groupIds as $groupId) {
	$groupId = trim($groupId);
	$kuser = kuserPeer::retrieveByPK($groupId);
	$currentMembersCount = $kuser->getMembersCount();
	KalturaLog::debug("Current members count for group with ID $groupId is $currentMembersCount\n");

	$c = new Criteria();
	$c->add(BaseKuserKgroupPeer::PARTNER_ID, $kuser->getPartnerId());
	$c->add(BaseKuserKgroupPeer::KGROUP_ID, $groupId);
	$c->add(BaseKuserKgroupPeer::STATUS, KuserKgroupStatus::ACTIVE, Criteria::EQUAL);
	$numOfUsersMembers = BaseKuserKgroupPeer::doCount($c);
	KalturaLog::debug("Current user members for group with ID $groupId is $numOfUsersMembers\n");

	if ($currentMembersCount == $numOfUsersMembers) {
		KalturaLog::debug("No update needed for group with ID $groupId, current members count is already $currentMembersCount\n");
		continue;
	}

	$kuser->setMembersCount($numOfUsersMembers);
	$kuser->save();

	KalturaLog::debug("Updated membersCount from $currentMembersCount to $numOfUsersMembers for group with ID $groupId\n");

	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();

	$count++;
	if ($count % 1000 === 0) {
		KalturaLog::debug('Currently at: ' . $count . ' out of: ' . $totalGroups);
		KalturaLog::debug('Sleeping for 30 seconds');
		sleep(30);
	}
}

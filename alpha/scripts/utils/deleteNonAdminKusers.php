<?php

require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
if ($argc < 2)
	die("Usage: php $argv[0] partnerId <realrun | dryrun>"."\n");

$partnerId = $argv[1] ;
$dryrun = true;
if($argc == 3 && $argv[2] == 'realrun')
{
	$dryrun = false;
}
KalturaStatement::setDryRun($dryrun);
KalturaLog::debug('dryrun value: ['.$dryrun.']');

$kuserIdsArr = getKuserIds($partnerId);
$start = 0;
$length = 100;
while($start < count($kuserIdsArr))
{
	$kuserIdsToUpdate = array_slice($kuserIdsArr, $start , $length);
	updateStatuses($kuserIdsToUpdate);
	$start += $length;
	sleep(1);
}

KalturaLog::debug('DONE!');

function getKuserIds($partnerId)
{
	$c = new Criteria();
	$c->add(kuserPeer::PARTNER_ID, $partnerId);
	$c->add(kuserPeer::IS_ADMIN, 0);
	$c->addSelectColumn(kuserPeer::ID);
	$stmt = kuserPeer::doSelectStmt($c);
	return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function updateStatuses($kuserIds)
{
	$c = new Criteria();
	$c->add(kuserPeer::ID, $kuserIds, Criteria::IN);
	$kusers = kuserPeer::doSelect($c);

	foreach ($kusers as $kuser)
	{
		KalturaLog::debug('Updating status deleted on kuser id ' . $kuser->getId());
		$kuser->setStatus(KuserStatus::DELETED);
		$kuser->save();
	}
	kEventsManager::flushEvents();
}
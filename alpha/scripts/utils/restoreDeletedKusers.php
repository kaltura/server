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
KalturaLog::debug("total deleted users: " . count($kuserIdsArr));

$start = 0;
$length = 100;
while($start < count($kuserIdsArr))
{
	$kuserIdsToUpdate = array_slice($kuserIdsArr, $start , $length);
	updateStatuses($kuserIdsToUpdate ,$dryrun);
	$start += $length;
	sleep(1);
}

KalturaLog::debug('DONE!');

function getKuserIds($partnerId)
{
	$c = new Criteria();
	$c->add(kuserPeer::PARTNER_ID, $partnerId);
	$c->add(kuserPeer::STATUS, KuserStatus::DELETED);
	$c->addSelectColumn(kuserPeer::ID);
	// to avoid filter out deleted users
	kuserPeer::setUseCriteriaFilter(false);
	$stmt = kuserPeer::doSelectStmt($c);
	return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function updateStatuses($kuserIds, $dryrun)
{
	$c = new Criteria();
	$c->add(kuserPeer::ID, $kuserIds, Criteria::IN);
	$kusers = kuserPeer::doSelect($c);

	foreach ($kusers as $kuser)
	{
		$kuser->setStatus(KuserStatus::ACTIVE);
		if (!$dryrun)
		{
			KalturaLog::debug('Updating status to active on kuser id ' . $kuser->getId());
			$kuser->save();
		}
	}
	if ($dryrun)
	{
		KalturaLog::debug('Dry run, effected user ids  ' . implode(',', $kuserIds));
	}
	kEventsManager::flushEvents();
}
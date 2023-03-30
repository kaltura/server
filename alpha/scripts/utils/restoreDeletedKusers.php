<?php

require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
if ($argc < 2)
	die("Usage: php $argv[0] kuserIdsFile <realrun | dryrun>"."\n");

$kuserIdsFilePath = $argv[1];
$kuserIdsArr = file_get_contents($kuserIdsFilePath);
$kuserIdsArr = array_filter(explode("\n", $kuserIdsArr));

$dryrun = true;
if($argc == 3 && $argv[2] == 'realrun')
{
	$dryrun = false;
}
KalturaStatement::setDryRun($dryrun);
KalturaLog::debug('dryrun value: ['.$dryrun.']');

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

function updateStatuses($kuserIds, $dryrun)
{
	$c = new Criteria();
	$c->add(kuserPeer::ID, $kuserIds, Criteria::IN);
	// to avoid filter out deleted users
	kuserPeer::setUseCriteriaFilter(false);
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
<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');

if ($argc < 2)
{
	die("Usage: php $argv[0] groupId <realrun | dryrun> \n");
}

$groupId = $argv[1];
$dryrun = true;
if($argc == 3 && $argv[2] == 'realrun')
{
	$dryrun = false;
}

$kuserKgroupList = KuserKgroupPeer::retrieveKuserKgroupByKgroupId($groupId);
foreach ($kuserKgroupList as $kuserKgroup)
{
	$kuser = kuserPeer::retrieveByPK($kuserKgroup->getKuserId());
	if ($kuser)
	{
		$registerInfo = $kuser->getRegistrationInfo();
		$registerInfoDecoded = json_decode($registerInfo, true);
		if ($registerInfoDecoded && !$registerInfoDecoded['groupId'])
		{
			$registerInfoDecoded['groupId'] = $groupId;
			$kuser->setRegistrationInfo($registerInfoDecoded);
			$kuser->save();
			print_r('Adding groupId: ' . $groupId . ' to RegistrationInfo on kuserId: '. $kuser->getId() . "\n");
		}
	}
}
print_r("Done! \n");
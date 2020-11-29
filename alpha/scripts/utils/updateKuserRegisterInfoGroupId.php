<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');

if ($argc < 3)
{
	die("Usage: php $argv[0] groupId kuserIdsFile <realrun | dryrun> \n");
}

$groupId = $argv[1];
$kuserIdsFile = $argv[2];
$dryrun = true;
if($argc == 4 && $argv[3] == 'realrun')
{
	$dryrun = false;
}

$kuserIds = file ($kuserIdsFile) or die ('Could not read file'."\n");

foreach ($kuserIds as $kuserId)
{
	$kuserId = trim($kuserId);
	$kuser = kuserPeer::retrieveByPK($kuserId);
	if ($kuser)
	{
		$registerInfo = $kuser->getRegistrationInfo();
		if ($registerInfo)
		{
			$registerInfoDecoded = json_decode($registerInfo, true);
			if ($registerInfoDecoded && !$registerInfoDecoded['groupId'])
			{
				$registerInfoDecoded['groupId'] = $groupId;
				$registerInfoEncoded = str_replace('\/', '/', json_encode($registerInfoDecoded));
				$kuser->setRegistrationInfo($registerInfoEncoded);
				$kuser->save();
				print_r('Adding groupId: ' . $groupId . ' to RegistrationInfo on kuserId: '. $kuserId . "\n");
			}
		}
	}
}
print_r("Done! \n");
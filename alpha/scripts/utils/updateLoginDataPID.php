<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
echo "Going to update Login email\n";

$userLoginDataId = 11;

$userLOginData = UserLoginDataPeer::retrieveByPK($userLoginDataId);

$newPID = "101";
$oldConfigPID = $userLOginData->getConfigPartnerId();
echo $oldConfigPID;

if ($oldConfigPID)
{
	$userLOginData->setConfigPartnerId($newPID);
	echo "New config PID is:";
	echo $userLOginData->getConfigPartnerId();
//  	$userLOginData->save();
}

echo "Finished running\n";

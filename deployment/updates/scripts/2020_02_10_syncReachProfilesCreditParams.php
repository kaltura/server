<?php
define("BASE_DIR", dirname(__FILE__));
require_once(BASE_DIR.'/../../../alpha/scripts/bootstrap.php');

$realRun = isset($argv[1]) && $argv[1] == 'realrun';
if ($realRun)
{
	KalturaLog::debug("*************** In Realrun mode ***************");
}
else
{
	KalturaLog::debug("*************** In Dry Run mode ***************");
}

{
	KalturaLog::debug('Start update Reach Profiles Credit');
	$reachProfiles = getReachProfilesToUpdate();
	if (!count($reachProfiles))
	{
		KalturaLog::debug('No Reach Profiles to process');
		return;
	}
	KalturaLog::debug('Found ' . count($reachProfiles) . ' reach profiles to process');
	foreach ($reachProfiles as $reachProfile)
	{
		/* @var $reachProfile ReachProfile */
		KalturaLog::debug('Handling Reach Profile with ID: [' . $reachProfile->getId() . '] for partnerId [' . $reachProfile->getPartnerId() . ']');

			$credit = $reachProfile->getCredit();
			if ($credit)
			{
				if ($realRun)
				{
					$addon = $credit->getAddOn();
					if($addon)
					{
						$reachProfile->setAddOn($addon);
					}
					$lastSyncTime = $credit->getLastSyncTime();
					if($lastSyncTime)
					{
						$reachProfile->setLastSyncTime($lastSyncTime);
					}
					$syncedCredit = $credit->getSyncedCredit();
					if($syncedCredit)
					{
						$reachProfile->setSyncedCredit($syncedCredit);
					}
					$reachProfile->save();
				}
				else
				{
					KalturaLog::debug('Reach Profile with ID: [' . $reachProfile->getId() . '] Will be set to: Addon = ' . $credit->getAddOn(). ' syncedCredit = '
						. $credit->getLastSyncTime() . ' lastSyncTime = ' . $credit->getLastSyncTime());
				}
			}
	}
}

/**
 * @return array
 */
function getReachProfilesToUpdate()
{
	$c = new Criteria ();
//		$c->add(ReachProfilePeer::ID, array(9682,9692), Criteria::EQUAL);
	$res = ReachProfilePeer::doSelect($c);
	return $res;
}

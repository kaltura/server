<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
require_once('/opt/kaltura/app/alpha/scripts/utils/mergeDuplicateUsersUtils.php');

define("BASE_DIR", dirname(__FILE__));
define("LAST_RUN_TIME_FILE_NAME", "merge_duplicated_users_last_run_time.txt");
define("MAX_RECORDS", 100);

$dryrun = false;
if($argc == 2 && $argv[1] == 'dryrun')
{
	$dryrun = true;
}
KalturaStatement::setDryRun($dryrun);
KalturaLog::debug('dryrun value: ['.$dryrun.']');

mergeNewDuplicatedUsers();

function mergeNewDuplicatedUsers()
{
	KalturaLog::debug("Start merging duplicated users");

	$lastRunTime = getAndUpdateLastRunTime();
	$newKusers = getNewUsersCreated($lastRunTime);
	if(!count($newKusers))
	{
		KalturaLog::debug("No users to process");
		return;
	}

	while(count($newKusers))
	{
		foreach ($newKusers as $kuser)
		{
			$currentPuserId = $kuser->getPuserId();
			$currentPartnerId = $kuser->getPartnerId();

			$kusersArray = getAllDuplicatedKusersForPuser($currentPuserId, $currentPartnerId);
			if (!$kusersArray)
			{
				KalturaLog::debug('ERROR: couldn\'t find kusers with puser id ['.$currentPuserId.']');
				continue;
			}

			// if we already merged this user skip to next one
			if($kusersArray[0]->getId() < $kuser->getId())
			{
				KalturaLog::debug('puserId ['.$currentPuserId.'] was already handled - skipping');
				continue;
			}

			KalturaLog::debug('Started handling puserId ['.$currentPuserId.'] for partnerId [' . $currentPartnerId .']');
			$baseKuser = findKuserWithMaxEntries($kusersArray, $currentPartnerId);
			mergeUsersToBaseUser($kusersArray, $baseKuser, $currentPartnerId);
			KalturaLog::debug('finished handling puserId ['.$currentPuserId.']');
		}

		$newKusers = getNewUsersCreated($lastRunTime, $kuser);
	}

	KalturaLog::debug("Done merging duplicated users");
}


function getNewUsersCreated($lastRunTime, $lastUser = null)
{
	$c = new Criteria ();
	$c->add(kuserPeer::CREATED_AT, $lastRunTime, Criteria::GREATER_EQUAL);
	$c->addAscendingOrderByColumn(kuserPeer::ID);
	if($lastUser)
	{
		$c->add(kuserPeer::ID, $lastUser->getId(), Criteria::GREATER_THAN);
	}
	$c->setLimit(MAX_RECORDS);
	$res = kuserPeer::doSelect($c);

	if(!count($res))
	{
		KalturaLog::debug("No new users created from last run time [$lastRunTime]");
		if($lastUser)
		{
			KalturaLog::debug("No new users created from last handled user creation time [{$lastUser->getCreatedAt(null)}]");
		}
		return array();
	}

	return $res;
}


function getAndUpdateLastRunTime()
{
	$currentTime = time();
	$lastSyncTime = trim(file_get_contents(BASE_DIR . "/" . LAST_RUN_TIME_FILE_NAME));
	if(!$lastSyncTime)
	{
		$lastSyncTime = $currentTime - dateUtils::HOUR;
	}

	file_put_contents(BASE_DIR . "/" . LAST_RUN_TIME_FILE_NAME, $currentTime);
	return $lastSyncTime;
}
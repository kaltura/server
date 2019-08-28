<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
require_once('/opt/kaltura/app/alpha/scripts/utils/mergeDuplicateUsersUtils.php');

define("BASE_DIR", '/tmp/');
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

	$currentTime = time();
	$lastRunTime = getAndUpdateLastRunTime($currentTime);
	$newKusers = getNewUsersCreated($lastRunTime, $currentTime);
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
			$currentKuserId = $kuser->getId();

			$kusersArray = getAllDuplicatedKusersForPuser($currentPuserId, $currentPartnerId);
			if (!$kusersArray || count($kusersArray) == 1)
			{
				KalturaLog::debug('ERROR: couldn\'t find duplicated kusers with puser id ['.$currentPuserId.']');
				continue;
			}

			KalturaLog::debug('Started handling puserId ['.$currentPuserId.'] with kuser id [' . $currentKuserId . '] for partnerId [' . $currentPartnerId .']');
			$baseKuser = findKuserWithMaxEntries($kusersArray, $currentPartnerId);
			mergeUsersToBaseUser($kusersArray, $baseKuser, $currentPartnerId);
			KalturaLog::debug('finished handling puserId ['.$currentPuserId.']');
		}

		$newKusers = getNewUsersCreated($lastRunTime, $currentTime, $kuser);
	}

	KalturaLog::debug("Done merging duplicated users");
}


function getNewUsersCreated($lastRunTime, $currentTime, $lastUser = null)
{
	$c = new Criteria ();
	$c->add(kuserPeer::CREATED_AT, $lastRunTime, Criteria::GREATER_EQUAL);
	$c->addAnd(kuserPeer::CREATED_AT, $currentTime, Criteria::LESS_THAN);
	$c->add(kuserPeer::UPDATED_AT, $lastRunTime, Criteria::GREATER_EQUAL);
	$c->addAscendingOrderByColumn(kuserPeer::ID);
	if($lastUser)
	{
		$c->add(kuserPeer::ID, $lastUser->getId(), Criteria::GREATER_THAN);
	}
	$c->setLimit(MAX_RECORDS);
	$res = kuserPeer::doSelect($c);

	if(!count($res))
	{
		if($lastUser)
		{
			KalturaLog::debug("No new users created from last handled user creation time [{$lastUser->getCreatedAt(null)}] until [$currentTime]");
		}
		else
		{
			KalturaLog::debug("No new users created from last run time [$lastRunTime] until [$currentTime]");
		}
		return array();
	}

	return $res;
}


function getAndUpdateLastRunTime($currentTime)
{
	$lastSyncTime = trim(file_get_contents(BASE_DIR . "/" . LAST_RUN_TIME_FILE_NAME));
	if(!$lastSyncTime)
	{
		$lastSyncTime = $currentTime - dateUtils::HOUR;
	}

	file_put_contents(BASE_DIR . "/" . LAST_RUN_TIME_FILE_NAME, $currentTime);
	return $lastSyncTime;
}
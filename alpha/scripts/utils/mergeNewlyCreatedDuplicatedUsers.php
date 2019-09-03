<?php
require_once(__DIR__ . '/../bootstrap.php');
require_once(__DIR__ . '/mergeDuplicateUsersUtils.php');

define('MAX_RECORDS', 100);
define('K1_KUSER', 'k1');
define('K2_KUSER', 'k2');
define ('MAX_USERS_TO_HANDLE', 10000);

$fp = fopen(__DIR__ . '/mergeNewlyCreatedDuplicatedUsers.php', "r+");
if (!flock($fp, LOCK_EX|LOCK_NB))
{
	KalturaLog::debug('Could not lock file. Exiting.');
	return;
}
$lastRunFilePath = $argv[1];

$dryrun = false;
if($argc == 3 && $argv[2] == 'dryrun')
{
	$dryrun = true;
}
KalturaStatement::setDryRun($dryrun);
KalturaLog::debug('dryrun value: ['.$dryrun.']');

mergeNewDuplicatedUsers($lastRunFilePath);

flock($fp, LOCK_UN);

function mergeNewDuplicatedUsers($lastRunFilePath)
{
	$usersHandled = 0;
	KalturaLog::debug("Start merging duplicated users");

	$currentTime = time();
	$startId = getStartId($currentTime, $lastRunFilePath);
	$lastId = getLastId($currentTime, $startId);
	if(!$startId || !$lastId)
	{
		KalturaLog::debug("Could not extract ids range for query");
		return;
	}

	$newPusers = getNewDuplicatedUsersCreated($startId, $lastId, $currentTime);

	if(!count($newPusers))
	{
		file_put_contents($lastRunFilePath, $lastId);
		KalturaLog::debug("No users to process");
		return;
	}

	while(count($newPusers))
	{
		foreach ($newPusers as $user)
		{
			$currentPuserId = $user['PUSER_ID'];
			$currentPartnerId = $user['PARTNER_ID'];

			$kusersArray = getAllDuplicatedKusersForPuser($currentPuserId, $currentPartnerId);
			if (count($kusersArray) < 2)
			{
				KalturaLog::debug('couldn\'t find duplicated kusers with puser id ['.$currentPuserId.'] partner id ['.$currentPartnerId.']');
				continue;
			}

			KalturaLog::debug('Started handling puserId ['.$currentPuserId.'] for partnerId [' . $currentPartnerId .']');
			$baseKuser = findKuserWithMaxEntries($kusersArray, $currentPartnerId);
			$currentKuserId = $baseKuser->getId();
			mergeUsersToBaseUser($kusersArray, $baseKuser, $currentPartnerId);
			KalturaLog::debug('finished handling puserId ['.$currentPuserId.']');
			kEventsManager::flushEvents();
			$usersHandled++;

			if($usersHandled > MAX_USERS_TO_HANDLE)
			{
				file_put_contents($lastRunFilePath, $currentKuserId);
				return;
			}
		}

		$newPusers = getNewDuplicatedUsersCreated($currentKuserId, $lastId, $currentTime);
	}

	file_put_contents($lastRunFilePath, $lastId);
	KalturaLog::debug("Done merging duplicated users");
}


function getNewDuplicatedUsersCreated($startId, $lastId, $currentTime)
{
	$c = new Criteria();
	kuserPeer::setUseCriteriaFilter(false);

	$c->addSelectColumn(kuserPeer::alias(K1_KUSER, kuserPeer::PUSER_ID));
	$c->addSelectColumn(kuserPeer::alias(K1_KUSER, kuserPeer::PARTNER_ID));
	$c->addAlias(K1_KUSER, kuserPeer::TABLE_NAME);
	$c->addAlias(K2_KUSER, kuserPeer::TABLE_NAME);
	$c->addMultipleJoin(array(array(kuserPeer::alias(K1_KUSER, kuserPeer::PUSER_ID), kuserPeer::alias(K2_KUSER, kuserPeer::PUSER_ID)),
		array(kuserPeer::alias(K1_KUSER, kuserPeer::PARTNER_ID), kuserPeer::alias(K2_KUSER, kuserPeer::PARTNER_ID)),
		array(kuserPeer::alias(K1_KUSER, kuserPeer::ID), kuserPeer::alias(K2_KUSER, kuserPeer::ID), Criteria::NOT_EQUAL)), Criteria::INNER_JOIN);
	$c->add(kuserPeer::alias(K1_KUSER, kuserPeer::ID), $startId, Criteria::GREATER_THAN);
	$c->addAnd(kuserPeer::alias(K1_KUSER, kuserPeer::ID), $lastId, Criteria::LESS_EQUAL);
	$c->add(kuserPeer::alias(K1_KUSER, kuserPeer::STATUS), kuserStatus::DELETED, Criteria::NOT_EQUAL);
	$c->add(kuserPeer::alias(K2_KUSER, kuserPeer::STATUS), kuserStatus::DELETED, Criteria::NOT_EQUAL);

	$c->addAscendingOrderByColumn(kuserPeer::alias(K1_KUSER, kuserPeer::ID));
	$c->setLimit(MAX_RECORDS);
	$c->setDistinct();
	$stmt = kuserPeer::doSelectStmt($c);
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	kuserPeer::setUseCriteriaFilter(true);

	if(!count($res))
	{
		KalturaLog::debug("No new duplicated users created from last handled user with id: [$startId] until time: [$currentTime]");
		return array();
	}

	return $res;
}


function getStartId($currentTime, $lastRunFilePath)
{
	$startFromId = trim(file_get_contents($lastRunFilePath));
	if($startFromId)
	{
		return $startFromId;
	}

	$lastRunTime = $currentTime - dateUtils::HOUR;
	$c = new Criteria ();
	$c->add(kuserPeer::UPDATED_AT, $lastRunTime, Criteria::GREATER_EQUAL);
	$c->add(kuserPeer::CREATED_AT, $lastRunTime , Criteria::GREATER_EQUAL);
	$c->addAnd(kuserPeer::CREATED_AT, $currentTime, Criteria::LESS_THAN);
	$c->addAscendingOrderByColumn(kuserPeer::ID);
	$startFromKuser = kuserPeer::doSelectOne($c);

	if(!$startFromKuser)
	{
		return null;
	}

	return $startFromKuser->getId();
}

function getLastId($currentTime, $startId)
{
	if(!$startId)
	{
		return null;
	}
	$maxUserCreationTime = $currentTime - (dateUtils::HOUR * 0.5);

	$c = new Criteria ();
	$c->add(kuserPeer::ID, $startId, Criteria::GREATER_THAN);
	$c->add(kuserPeer::CREATED_AT, $maxUserCreationTime, Criteria::LESS_THAN);
	$c->add(kuserPeer::UPDATED_AT, $maxUserCreationTime, Criteria::LESS_THAN);
	$c->addDescendingOrderByColumn(kuserPeer::ID);
	$lastKuser = kuserPeer::doSelectOne($c);
	if(!$lastKuser)
	{
		return null;
	}
	return $lastKuser->getId();
}

<?php
if ($argc < 3){
	die ($argv[0]. " <last_run_file_path> <sent_to> <dryrun>.\n");
}

require_once(__DIR__ . '/../bootstrap.php');
require_once(__DIR__ . '/mergeDuplicateUsersUtils.php');

define('MAX_RECORDS', 100);
define('K1_KUSER', 'k1');
define('K2_KUSER', 'k2');
define ('MAX_USERS_TO_HANDLE', 10000);

try
{
	$lastRunFilePath = $argv[1];

	$dryrun = false;
	if($argc == 4 && $argv[3] == 'dryrun')
	{
		$dryrun = true;
	}
	$address = $argv[2];
	KalturaStatement::setDryRun($dryrun);
	KalturaLog::debug('dryrun value: ['.$dryrun.']');

	$fp = fopen(__DIR__ . '/mergeNewlyCreatedDuplicatedUsers.php', "r+");
	if (!flock($fp, LOCK_EX|LOCK_NB))
	{
		throw new Exception ( "Could not lock file. Merge duplicates script already running" );
	}

	mergeNewDuplicatedUsers($lastRunFilePath);

	flock($fp, LOCK_UN);
}
catch(Exception $e)
{
	KalturaLog::err($e);
	if($address)
	{
		sendMail(array($address), "Error in mergeNewlyCreatedDuplicatedUsers.php script", $e, 'Kaltura');
	}
}

function mergeNewDuplicatedUsers($lastRunFilePath)
{
	$usersHandled = 0;
	KalturaLog::debug("Start merging duplicated users");

	$currentTime = time();
	$startId = getStartId($lastRunFilePath);
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

			if(!$currentPartnerId || !$currentPuserId)
			{
				continue;
			}

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
		if(isset($currentKuserId))
		{
			$newPusers = getNewDuplicatedUsersCreated($currentKuserId, $lastId, $currentTime);
		}
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


function getStartId($lastRunFilePath)
{
	$startFromId = trim(file_get_contents($lastRunFilePath));
	if(!$startFromId)
	{
		throw new Exception ("Missing file with last synced kuser id");
	}
	return $startFromId;
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
	$c->add(kuserPeer::ID, $startId+100000, Criteria::LESS_THAN);
	$c->add(kuserPeer::CREATED_AT, $maxUserCreationTime, Criteria::LESS_THAN);
	$c->add(kuserPeer::UPDATED_AT, $maxUserCreationTime, Criteria::LESS_THAN);
	$c->addDescendingOrderByColumn(kuserPeer::ID);
	$lastKuser = kuserPeer::doSelectOne($c);
	if(!$lastKuser)
	{
		KalturaLog::debug("no new users created since last run");
		return null;
	}
	return $lastKuser->getId();
}

function sendMail($toArray, $subject, $body, $sender = null)
{
	$mailer = new PHPMailer();
	$mailer->CharSet = 'utf-8';
	$mailer->Mailer = 'smtp';
	$mailer->SMTPKeepAlive = true;

	if (!$toArray || count($toArray) < 1 || strlen($toArray[0]) == 0)
		return true;

	foreach ($toArray as $to)
		$mailer->AddAddress($to);

	$mailer->Subject = $subject;
	$mailer->Body = $body;
	$mailer->Sender = kConf::get('batch_notification_sender_email');
	$mailer->From = 'Kaltura Notification Service';
	$mailer->FromName = $sender;

	KalturaLog::info("sending mail to " . implode(",",$toArray) . ", from: [$sender]. subject: [$subject] with body: [$body]");
	try
	{
		return $mailer->Send();
	}
	catch ( Exception $e )
	{
		KalturaLog::err( $e );
		return false;
	}
}

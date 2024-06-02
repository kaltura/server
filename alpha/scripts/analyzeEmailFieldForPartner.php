<?php

require_once(__DIR__ . '/bootstrap.php');

// parse the command line
if($argc<3)
{
	die("Usage: php " . $argv[0] . " <partner id> <user type: admin | user> <realRun | dryRun>\n");
}

$partnerId = $argv[1];
$userType = $argv[2];
$dryRun = $argv[3] ? $argv[3] : 'dryRun';
var_dump($partnerId, $userType, $dryRun);

if (!PartnerPeer::retrieveByPK($partnerId))
{
	die("Please enter a valid partner Id!\n");
}

if (is_null($userType) || ($userType != 'admin' && $userType != 'user'))
{
	die("Please specify if looking for admin users (admin) or regular users (user)\n");
}

$isAdmin = 0;
if ($userType === 'admin')
{
	$isAdmin = 1;
}



function countUsers($partnerId, $isAdmin, $emailFieldValue)
{
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::ACTIVE);
	$emailCriteria->add(kuserPeer::IS_ADMIN, $isAdmin);
	if($emailFieldValue == Criteria::ISNULL) // counting users without email value
	{
		$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNULL);
	}
	else
	{
		$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNOTNULL);
	}

	return kuserPeer::doCount($emailCriteria);
}

function noEmailPercentage($noEmailUsersCount, $allUsersCount)
{
	return (int)(($noEmailUsersCount * 100)/$allUsersCount);
}

function getUsersWithEmail($partnerId, $isAdmin)
{
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::ACTIVE);
	$emailCriteria->add(kuserPeer::IS_ADMIN, $isAdmin);
	$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNOTNULL);

	return kuserPeer::doSelect($emailCriteria);
}

function countUsersWithDuplicatedEmail($partnerId, $isAdmin)
{
	$emailCriteria = new Criteria();
	$dbMap = Propel::getDatabaseMap($emailCriteria->getDbName());
	$db = Propel::getDB($emailCriteria->getDbName());
	$params = array();
	$con = Propel::getConnection($emailCriteria->getDbName(), Propel::CONNECTION_READ);
	$sql = 'SELECT email, COUNT(*) cnt FROM kuser WHERE partner_id=' . $partnerId . ' AND is_admin=' . $isAdmin . ' AND status=1 GROUP BY email' ;
	$stmt = $con->prepare($sql);
	BasePeer::populateStmtValues($stmt, $params, $dbMap, $db);
	$stmt->execute();

	if ($emailCriteria->isUseTransaction()) $con->commit();

	foreach ($stmt as $item)
	{
		if ($item['cnt'] > 1)
		{
			KalturaLog::log("email [". $item['email']. "] is duplicated [". $item['cnt']. "] times");
		}
	}
}

function copyEmailToExternalId($partnerId, $isAdmin)
{
	$usersWithEmail = getUsersWithEmail($partnerId, $isAdmin);
	if (sizeof($usersWithEmail) > 0)
	{
		/* @var $user kuser */
		foreach ($usersWithEmail as $user)
		{
			if (!$user->getExternalId())
			{
				KalturaLog::log('Copying email [' . $user->getEmail() . '] for puser|kuser [' . $user->getPuserId() . ' | ' . $user->getId() . ']');
				$user->setExternalId($user->getEmail());
				$user->save();
			}
		}
	}
}


$noEmailUsersCount = countUsers($partnerId, $isAdmin, Criteria::ISNULL);
$withEmailUsersCount = countUsers($partnerId, $isAdmin, Criteria::ISNOTNULL);
$allUsersCount = $noEmailUsersCount + $withEmailUsersCount;
$noUserPercentage = noEmailPercentage($noEmailUsersCount, $allUsersCount);
KalturaLog::log("[$noUserPercentage%] of the users of partner [$partnerId] do not have an email address. exact numbers: [$noEmailUsersCount/$allUsersCount]");
KalturaLog::log("[$withEmailUsersCount] users out of a total of [$allUsersCount] users have email");
KalturaLog::log("[$noEmailUsersCount] users out of a total of [$allUsersCount] users dont have email");
countUsersWithDuplicatedEmail($partnerId, $isAdmin);

if ($dryRun === 'realRun')
{
	copyEmailToExternalId($partnerId, $isAdmin);
}
else
{
	KalturaLog::log('Dry run. not copying');
}

KalturaLog::log('Done.');
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

function countDuplicatedUsersByEmail($users, $email)
{
	$duplicatedUserCounter = 0;
	foreach ($users as $user)
	{
		if ($user->getEmail() === $email)
		{
			$duplicatedUserCounter++;
		}
	}
	return $duplicatedUserCounter;
}

function countUsersWithDuplicatedEmail($partnerId, $isAdmin)
{
	$emailCriteria = new Criteria();
	$emailCriteria->clearSelectColumns()->addSelectColumn(kuserPeer::EMAIL);
	$emailCriteria->clearSelectColumns()->addSelectColumn("COUNT(*) cnt");
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::ACTIVE);
	$emailCriteria->add(kuserPeer::IS_ADMIN, $isAdmin);
	$emailCriteria->addGroupByColumn(kuserPeer::EMAIL);
	$arr = kuserPeer::doSelect($emailCriteria);
	foreach ($arr as $item)
	{
		KalturaLog::log(print_r($item));
	}

//	$duplicatedUserCounter = 0;
//	$usersWithEmail = getUsersWithEmail($partnerId, $isAdmin);
//	/* @var $user kuser */
//	foreach ($usersWithEmail as $user)
//	{
//		$usersWithSameEmail = countDuplicatedUsersByEmail($usersWithEmail, $user->getEmail());
//		if ($usersWithSameEmail > 1)
//		{
//			KalturaLog::log('User with duplicated email [' . $user->getId() . ']');
//			$duplicatedUserCounter++;
//		}
//	}
//	return $duplicatedUserCounter;
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
//$usersWithDuplicateEmail =
	countUsersWithDuplicatedEmail($partnerId, $isAdmin);
//KalturaLog::log("[$usersWithDuplicateEmail] users out of a total of [$allUsersCount] users with duplicated email");

if ($dryRun === 'realRun')
{
	copyEmailToExternalId($partnerId, $isAdmin);
}
else
{
	KalturaLog::log('Dry run. not copying');
}

KalturaLog::log('Done.');
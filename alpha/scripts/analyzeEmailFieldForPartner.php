<?php

require_once(__DIR__ . '/bootstrap.php');

// parse the command line
if($argc<2)
{
	die("Usage: php $argv[0] <partner id>");
}

$partnerId = $argv[1];
$userType = $argv[2];
$dryRun = $argv[3];
var_dump($partnerId, $userType, $dryRun);

if (!PartnerPeer::retrieveByPK($partnerId))
{
	die("Please enter a valid partner Id!\n");
}

if (!$userType || ($userType != "admin" && $userType != "user"))
{
	die("Please specify if looking for admin users (admin) or regular users (user)\n");
}

if (!$dryRun || ($dryRun != "dryRun" && $dryRun != "wetRun"))
{
	die("Please specify the desired mode of operation: dryRun or wetRun\n");
}



function countUsers($partnerId, $isAdmin, $hasEmail = null, $email = null)
{
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::ACTIVE);
	$emailCriteria->add(kuserPeer::IS_ADMIN, $isAdmin);
	if($hasEmail == Criteria::ISNULL)
	{
		$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNULL);
	}
	else if ($hasEmail == Criteria::ISNOTNULL)
	{
		if ($email)
		{
			$emailCriteria->add(kuserPeer::EMAIL, $email, Criteria::EQUAL);
		}
		else
		{
			$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNOTNULL);
		}
	}

	return kuserPeer::doCount($emailCriteria);
}

function noEmailPercentage($noEmailUsersCount, $allUsersCount)
{
	return (int)(($noEmailUsersCount * 100) /$allUsersCount);
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
	$duplicatedUserCounter = 0;
	$usersWithEmail = getUsersWithEmail($partnerId, $isAdmin);
	/* @var $user kuser */
	foreach ($usersWithEmail as $key => $user)
	{
		$usersWithSameEmail = countUsers($partnerId, $isAdmin, Criteria::ISNOTNULL, $user->getEmail());
		if ($usersWithSameEmail > 1)
		{
			$duplicatedUserCounter++;
		}
	}
	return $duplicatedUserCounter;
}

function copyEmailToExternalId($partnerId, $isAdmin)
{
	KalturaLog::log('copying email to externalId.');
    $usersWithEmail = getUsersWithEmail($partnerId, $isAdmin);
    if (sizeof($usersWithEmail) > 0)
    {
	    /* @var $user kuser */
	    foreach ($usersWithEmail as $user)
	    {
			if (!$user->getExternalId())
			{
				$user->setExternalId($user->getEmail());
				$user->save();
			}
	    }
    }
}

$isAdmin = 0;
if ($userType === "admin")
{
	$isAdmin = 1;
}
$noEmailUsersCount = countUsers($partnerId, $isAdmin, Criteria::ISNULL);
$withEmailUsersCount = countUsers($partnerId, $isAdmin, Criteria::ISNOTNULL);
$allUsersCount = countUsers($partnerId, $isAdmin);
$noUserPercentage = noEmailPercentage($noEmailUsersCount, $allUsersCount);
KalturaLog::log("[$noUserPercentage%] of the users of partner [$partnerId] do not have an email address. exact numbers: [$noEmailUsersCount/$allUsersCount]");
KalturaLog::log("[$withEmailUsersCount] users out of a total of [$allUsersCount] users have email");
KalturaLog::log("[$noEmailUsersCount] users out of a total of [$allUsersCount] users dont have email");
$usersWithDuplicateEmail = countUsersWithDuplicatedEmail($partnerId, $isAdmin);
KalturaLog::log("[$usersWithDuplicateEmail] users out of a total of [$allUsersCount] users with duplicated email");

if ($dryRun !== 'dryRun')
{
	copyEmailToExternalId($partnerId, $isAdmin);
}
else
{
	KalturaLog::log('Dry run. not copying');
}

KalturaLog::log('Done.');
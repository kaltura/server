<?php

require_once(__DIR__ . '/bootstrap.php');

// parse the command line
if($argc<3)
{
	die("Usage: php " . $argv[0] . " <partner id> <realRun | dryRun>\n");
}
$partnerId = $argv[1];
$dryRun = $argv[2];
var_dump($partnerId, $dryRun);

if (!PartnerPeer::retrieveByPK($partnerId))
{
	die("Please enter a valid partner Id!\n");
}

$noEmailUsers = getUsers($partnerId, false);
$withEmailUsers = getUsers($partnerId, true);
$totalUsers = count($noEmailUsers) + count($withEmailUsers);
$noUserPercentage = noEmailPercentage(count($noEmailUsers), $totalUsers);
KalturaLog::log("[$noUserPercentage%] of the users of partner [$partnerId] do not have an email address. exact numbers: [" . count($noEmailUsers) ." /$totalUsers]");
KalturaLog::log("[" . count($withEmailUsers) . "] users out of a total of [$totalUsers] users have email");
KalturaLog::log("[" . count($noEmailUsers) . "] users out of a total of [$totalUsers] users dont have email");
countUsersWithDuplicatedEmail($partnerId);

if ($dryRun === 'realRun')
{
	copyEmailToExternalId($withEmailUsers);
}
else
{
	KalturaLog::log('Dry run. not copying');
}
KalturaLog::log('Done.');


function noEmailPercentage($noEmailUsersCount, $totalUsers)
{
	return (int)(($noEmailUsersCount * 100)/$totalUsers);
}

function getUsers($partnerId, $hasEmail)
{
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::ACTIVE);
	$emailCriteria->add(kuserPeer::TYPE, 0);
	$emailCriteria->add(kuserPeer::IS_ADMIN, array(0,1), Criteria::IN);
	if($hasEmail)
	{
		$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNOTNULL);
	}
	else
	{
		$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNULL);
	}

	return kuserPeer::doSelect($emailCriteria);
}

function countUsersWithDuplicatedEmail($partnerId)
{
	$countField = 'COUNT(kuser.EMAIL)';
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId);
	$emailCriteria->add(kuserPeer::IS_ADMIN, array(0,1), Criteria::IN);
	$emailCriteria->add(kuserPeer::STATUS, 1);
	$emailCriteria->add(kuserPeer::TYPE, 0);
	$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNOTNULL);
	$emailCriteria->addGroupByColumn(kuserPeer::EMAIL);
	$emailCriteria->addSelectColumn($countField);
	$emailCriteria->addSelectColumn(kuserPeer::EMAIL);
	$emailCriteria->addHaving($emailCriteria->getNewCriterion(kuserPeer::EMAIL, $countField . '>' . 1, Criteria::CUSTOM));
	$stmt = kuserPeer::doSelectStmt($emailCriteria);
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	foreach ($rows as $row)
	{
		KalturaLog::log("email [". $row['EMAIL']. "] is duplicated [". $row['COUNT(kuser.EMAIL)']  . "] times");
	}
}

function copyEmailToExternalId($usersWithEmail)
{
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



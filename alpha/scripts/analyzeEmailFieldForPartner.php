<?php

require_once(__DIR__ . '/bootstrap.php');

// parse the command line
if($argc<2)
{
	die("Usage: php $argv[0] <partner id>");
}
	
$partnerId = $argv[1];
var_dump($partnerId);

if (!PartnerPeer::retrieveByPK($partnerId))
{
	die("Please enter a valid partner Id!");
}

function countUsersMissingEmail($partnerId)
{
	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::ACTIVE);
	$emailCriteria->add(kuserPeer::EMAIL, null, Criteria::ISNULL);
	return kuserPeer::doCount($emailCriteria);
}

function countTotalUsers($partnerId)
{
	$allUsersCriteria = new Criteria();
	$allUsersCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	return kuserPeer::doCount($allUsersCriteria);
}

function noEmailPercentage($noEmailUsersCount, $allUsersCount)
{
	return (($noEmailUsersCount * 100) /$allUsersCount);
}

$noEmailUsersCount = countUsersMissingEmail($partnerId);
$allUsersCount = countTotalUsers($partnerId);
$noUserPercentage = noEmailPercentage($noEmailUsersCount, $allUsersCount);
KalturaLog::log("$noUserPercentage% of the users of partner $partnerId do not have an email address");
KalturaLog::log("$noEmailUsersCount users out of a total of $allUsersCount users");

KalturaLog::log('Done.');

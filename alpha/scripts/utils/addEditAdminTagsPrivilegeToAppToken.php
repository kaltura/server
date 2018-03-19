<?php

require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 3)
	die("Usage: " . basename(__FILE__) . " <partner ids, comma separated or all> <privilege> [realrun / dryrun]\n");

// input parameters
$partnerIds = $argv[1];
$privilege = trim($argv[2]);
$dryRun = (!isset($argv[3]) || $argv[3] != 'realrun');
KalturaLog::debug("Dry Run [" . $dryRun . "]");
KalturaStatement::setDryRun($dryRun);


function createDefaultCriteria($partnerIds, $privilege)
{
	$c = new Criteria();
	if ($partnerIds != 'all')
	{
		$partnerIdsArr = explode(",", $partnerIds);
		$c->add(AppTokenPeer::PARTNER_ID, $partnerIdsArr, Criteria::IN);
	}
	$c->add(AppTokenPeer::STATUS, AppTokenStatus::ACTIVE);
	$c->add(AppTokenPeer::SESSION_PRIVILEGES, null, Criteria::ISNULL);
	$c->addOr(AppTokenPeer::SESSION_PRIVILEGES, "%$privilege%", Criteria::NOT_LIKE);
	return $c;
}


$c1 = createDefaultCriteria($partnerIds, $privilege);
$c1->addAnd(AppTokenPeer::SESSION_PRIVILEGES, "%CAPTURE_DEVICE_ROLE%", Criteria::LIKE);
$appTokens1 = AppTokenPeer::doSelect($c1);

$c2 = createDefaultCriteria($partnerIds, $privilege);
$c2->add(AppTokenPeer::CUSTOM_DATA, "%kalturaCaptureAppToken%", Criteria::LIKE);
$appTokens2 = AppTokenPeer::doSelect($c2);

$appTokens = array_merge($appTokens1, $appTokens2);

$doneAppTokens = array();

foreach ($appTokens as $appToken)
{
	/** @var AppToken $appToken */
	if (isset($doneAppTokens[$appToken->getId()]))
		continue;
	$doneAppTokens[$appToken->getId()] = true;
	KalturaLog::debug("Editing appToken [".$appToken->getId()."]");
	$sessionPrivileges  = $appToken->getSessionPrivileges();
	if ($sessionPrivileges)
		$sessionPrivileges .= ",";
	$sessionPrivileges .= $privilege;
	$appToken->setSessionPrivileges($sessionPrivileges);
	$appToken->save();
}




?>
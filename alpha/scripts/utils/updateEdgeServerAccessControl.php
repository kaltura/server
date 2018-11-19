<?php

require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 2)
	die("Usage: " . basename(__FILE__) . " <partner ids, comma separated or file name> \n");

// input parameters
$partnerIds = $argv[1];

$dryRun = (!isset($argv[2]) || $argv[2] != 'realrun');
KalturaStatement::setDryRun($dryRun);

if(file_exists($partnerIds))
	$partnerIds = file($partnerIds);
else
	$partnerIds = explode(',', $partnerIds);

foreach($partnerIds as $partnerId)
{
	$partnerId = trim($partnerId);
	if (!$partnerId || !is_numeric($partnerId))
		continue;

	$criteria = new Criteria();
	$criteria->add(accessControlPeer::PARTNER_ID, $partnerId);
	$acps = accessControlPeer::doSelect($criteria);
	foreach ($acps as $accessControl)
	{
		/* @var accessControl $accessControl */
		foreach ($accessControl->getRulesArray() as $rule)
		{
			/* @var $rule kRule */
			if ($rule->hasActionType(array(RuleActionType::SERVE_FROM_REMOTE_SERVER)))
			{
				KalturaLog::debug("Found access control with id [" . $accessControl->getId() . "] and changing it");
				$accessControl->setSpecialProperty(accessControl::SERVE_FROM_SERVER_NODE_RULE, true);
				$accessControl->save();
				break;
			}
		}
	}
}


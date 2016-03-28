<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

if (count($argv) !== 3)
{
        die('wrong usage, please provide  <partnerId> <mailingList>');
}
$partnerId = $argv[1];
$mailingList = $argv[2];

$partner = PartnerPeer::retrieveByPK($partnerId);
if(!$partner)
	die('Partner id not found: ' . $partnerId . PHP_EOL);


$additionalParams = $partner->getAdditionalParams();
$additionalParams['mailingList'] = $mailingList;
$partner->setAdditionalParams($additionalParams);
$partner->save();

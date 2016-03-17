<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

if (count($argv) !== 3)
{
        die('please provide a partner id and a mailing list ' . PHP_EOL .
                'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL .
                'whereas X is partner id' . PHP_EOL);
}
$partnerId = @$argv[1];
$mailingList = @$argv[2];

$partner = PartnerPeer::retrieveByPK($partnerId);
if(!$partner)
	die('Partner id not found: ' . $partnerId . PHP_EOL);


$additionalParams = $partner->getAdditionalParams();
$additionalParams['mailingList'] = $mailingList;
$partner->setAdditionalParams($additionalParams);
$partner->save();

// validating that the mailing list was updated
$partner = PartnerPeer::retrieveByPK($partnerId);
$additionalParams = $partner->getAdditionalParams();
if (array_key_exists('mailingList', $additionalParams))
{
	echo $additionalParams['mailingList'];
} else {
	echo "Failed";
} 



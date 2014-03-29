<?php

$partnerIds = array();
$partnerIdsStr = count($argv) > 1 ? $argv[1] : '';
if (trim($partnerIdsStr))
{
	$partnerIds = explode(',', $partnerIdsStr);
	array_walk($partnerIds, 'trim');
}

if (!count($partnerIds))
{
	echo 'Missing list of partner ids'.PHP_EOL;
	echo 'Usage: php '.$argv[0].' pid1,pid2,pid3'.PHP_EOL;
	die;
}

chdir(__DIR__);
require_once(__DIR__ . '/../bootstrap.php');

foreach($partnerIds as $partnerId)
{
	$partner = PartnerPeer::retrieveByPK($partnerId);
	$disabledDeliverTypes = $partner->getFromCustomData('disabledDeliveryTypes');
	if (!$disabledDeliverTypes)
	{
		KalturaLog::info('No disabled delivery types for partner '.$partnerId);
		continue;
	}

	KalturaLog::info('Disabled delivery types for partner id '.$partnerId.': '.implode(', ',$disabledDeliverTypes));

	$customDeliveryTypes = $partner->getCustomDeliveryTypes();
	// will clear the old field and save to the new field
	$partner->setCustomDeliveryTypes($customDeliveryTypes);
	$partner->save();
}
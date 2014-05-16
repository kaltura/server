<?php

if ($argc < 3)
{
	echo "Required parameter [conversionProfileId] missing. Required form php addRequiredCopyPermissionConversionProfile.php <conversionProfileId> <comma-separated permissions>";
	die;
}

require_once(__DIR__ . '/../bootstrap.php');

$conversionProfileId = $argv[1];
$conversionProfile = conversionProfile2Peer::retrieveByPK($conversionProfileId);

$partner = PartnerPeer::retrieveByPK($conversionProfile->getPartnerId());

if ($partner->getPartnerGroupType() != PartnerGroupType::TEMPLATE)
{
	die ("Conversion profile with id [$conversionProfileId] does not belong to a template partner. Aborting.");
}

$conversionProfile->setRequiredCopyTemplatePermissions(explode(',', $argv[2]));
$conversionProfile->save();

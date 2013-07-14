<?php
$config = null;
$client = null;
/* @var $client KalturaClient */

require_once __DIR__ . '/lib/init.php';




/**
 * Start a new session
 */
$partnerId = $config['session']['partnerId'];
$adminSecretForSigning = $config['session']['adminSecret'];
$client->setKs($client->generateSessionV2($adminSecretForSigning, 'sanity-user', KalturaSessionType::ADMIN, $partnerId, 86400, ''));




/**
 * Get all the FLV flavor params
 */
$flavorParamsfilter = new KalturaFlavorParamsFilter();
$flavorParamsfilter->formatEqual = KalturaContainerFormat::MP4;
$flavorParamsList = $client->flavorParams->listAction($flavorParamsfilter);
/* @var $flavorParamsList KalturaFlavorParamsListResponse */



/**
 * Find the flavor params with the lowest bitrate
 */
$flavorParamsId = null;
$flavorParamsBitrate = null;
foreach($flavorParamsList->objects as $flavorParams)
{
	/* @var $flavorParams KalturaFlavorParams */
	if($flavorParams->id > 0 && (is_null($flavorParamsBitrate) || $flavorParamsBitrate > $flavorParams->videoBitrate))
	{
		$flavorParamsId = $flavorParams->id;
		$flavorParamsBitrate = $flavorParams->videoBitrate;
	}
}




/**
 * Create default conversion profile
 */
$conversionProfile = new KalturaConversionProfile();
$conversionProfile->isDefault = KalturaNullableBoolean::TRUE_VALUE;
$conversionProfile->name = 'sanity-test';
$conversionProfile->systemName = 'SANITY_TEST';
$conversionProfile->description = 'sanity-test';
$conversionProfile->flavorParamsIds = "0,$flavorParamsId";

$createdConversionProfile = $client->conversionProfile->add($conversionProfile);
/* @var $createdConversionProfile KalturaConversionProfile */

if(!$createdConversionProfile || !$createdConversionProfile->id)
{
	echo "Conversion profile not created\n";
	exit(-1);
}
if(!$createdConversionProfile->isDefault)
{
	echo "Conversion profile is not default\n";
	exit(-1);
}

exit(0);

<?php
/**
 * @package deployment
 * @subpackage live.liveParams
 *
 * Add live params
 *
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/04.liveParams.ini';
passthru("php $script $config");


chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$conversionProfile = new conversionProfile2();
$conversionProfile->setPartnerId(99);
$conversionProfile->setName('Default - Live');
$conversionProfile->setType(ConversionProfileType::LIVE_STREAM);
$conversionProfile->setSystemName('Default_Live');
$conversionProfile->setDescription('The default set of live renditions');
$conversionProfile->setIsDefault(true);
$conversionProfile->save();

$flavorParamsIds = array(32, 33, 34, 35);

foreach($flavorParamsIds as $flavorParamsId)
{
	$flavorParams = assetParamsPeer::retrieveByPK($flavorParamsId);
	
	$flavorParamsConversionProfile = new flavorParamsConversionProfile();
	$flavorParamsConversionProfile->setConversionProfileId($conversionProfile->getId());
	$flavorParamsConversionProfile->setFlavorParamsId($flavorParams->getId());
	$flavorParamsConversionProfile->setSystemName($flavorParams->getSystemName());
	$flavorParamsConversionProfile->setOrigin($flavorParams->getTags() == 'source' ? assetParamsOrigin::INGEST : assetParamsOrigin::CONVERT);
	$flavorParamsConversionProfile->setReadyBehavior($flavorParams->getReadyBehavior());
	$flavorParamsConversionProfile->save();
}


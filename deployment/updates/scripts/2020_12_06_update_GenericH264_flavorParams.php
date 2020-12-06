<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$filePath = realpath(dirname(__FILE__) . '/../../../') . '/deployment/base/scripts/init_data/04.flavorParams.ini';
$ini_item = 'Generic-H264';

$fileName = basename($filePath);
KalturaLog::info("Handling file [$fileName]");
$objectConfigurations = parse_ini_file($filePath, true);

$id = $objectConfigurations[$ini_item]['id'];

$c = new Criteria();
$c->addAnd(assetParamsPeer::ID, $id);
$assetParams = assetParamsPeer::doSelect($c);

foreach ($assetParams as $assetParam)
{
    KalturaLog::info("Updating id [{$assetParam->getId()}] of partenr [{$assetParam->getPartnerId()}]");
    $assetParam->setConversionEnginesExtraParams($objectConfigurations[$ini_item]['conversionEnginesExtraParams']);
    $assetParam->save();
}

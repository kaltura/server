<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

$filePath = realpath(dirname(__FILE__) . '/../../../') . '/deployment/base/scripts/init_data/04.flavorParams.ini';

$fileName = basename($filePath);
KalturaLog::info("Handling file [$fileName]");
$objectConfigurations = parse_ini_file($filePath, true);

foreach($objectConfigurations as $ini_item)
{
	$id = $ini_item['id'];
	
	$c = new Criteria();
	$c->addAnd(assetParamsPeer::ID, $id);
	$assetParams = assetParamsPeer::doSelect($c);
	
	foreach ($assetParams as $assetParam)
	{
		if(isset($ini_item['conversionEnginesExtraParams']))
		{
			KalturaLog::info("Updating id [{$assetParam->getId()}] of partenr [{$assetParam->getPartnerId()}]");
			$assetParam->setConversionEnginesExtraParams($ini_item['conversionEnginesExtraParams']);
			$assetParam->save();
		}
	}
}
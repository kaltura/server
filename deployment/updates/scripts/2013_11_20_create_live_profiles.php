<?php
/**
 * @package deployment
 * @subpackage live.liveParams
 *
 * Add live conversion profile to existing partners
 *
 * No need to re-run after server code deploy
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

function isLiveConversionProfileExists($partnerId)
{
	$c = new Criteria();
	$c->add(conversionProfile2Peer::PARTNER_ID, $partnerId);
	$c->add(conversionProfile2Peer::TYPE, ConversionProfileType::LIVE_STREAM);
	$c->add(conversionProfile2Peer::STATUS, ConversionProfileStatus::ENABLED);
	
	return conversionProfile2Peer::doCount($c);
}

function createLiveConversionProfile($partnerId)
{
	$conversionProfile = new conversionProfile2();
	$conversionProfile->setPartnerId($partnerId);
	$conversionProfile->setName('Cloud transcode');
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
}

$c = new Criteria();
$c->add(PermissionPeer::STATUS, PermissionStatus::ACTIVE);
$c->add(PermissionPeer::NAME, PermissionName::FEATURE_LIVE_STREAM);
$c->addAscendingOrderByColumn(PermissionPeer::ID);
$c->setLimit(100);

$offset = 0;
$permissions = PermissionPeer::doSelect($c);
while(count($permissions))
{
	foreach($permissions as $permission)
	{
		/* @var $permission Permission */
		$partnerId = $permission->getPartnerId();
		if(!isLiveConversionProfileExists($partnerId))
			createLiveConversionProfile($partnerId);
	} 
	
	$offset += count($permissions);
	$c->setOffset($offset);
	$permissions = PermissionPeer::doSelect($c);
}

<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Enable live to all paid partners
 *
 * No need to re-run after server code deploy
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$realRun = isset($argv[1]) && $argv[1] == 'realrun';
KalturaStatement::setDryRun(!$realRun);

function isLivePassThruConversionProfileExists($partnerId)
{
	$c = new Criteria();
	$c->add(conversionProfile2Peer::PARTNER_ID, $partnerId);
	$c->add(conversionProfile2Peer::TYPE, ConversionProfileType::LIVE_STREAM);
	$c->add(conversionProfile2Peer::STATUS, ConversionProfileStatus::ENABLED);
	
	return conversionProfile2Peer::doCount($c) > 0;
}

function createLivePassThruConversionProfile($partnerId)
{
	if(isLivePassThruConversionProfileExists($partnerId))
		return;
		
	$conversionProfile = new conversionProfile2();
	$conversionProfile->setPartnerId($partnerId);
	$conversionProfile->setName('Passthrough');
	$conversionProfile->setType(ConversionProfileType::LIVE_STREAM);
	$conversionProfile->setSystemName('Passthrough_Live');
	$conversionProfile->setDescription('Publish only the broadcasted stream');
	$conversionProfile->save();
	
	$flavorParamsIds = array(32);
	
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
$c->add(PartnerPeer::STATUS, Partner::PARTNER_STATUS_ACTIVE);
$c->add(PartnerPeer::PARTNER_PACKAGE, 1, Criteria::GREATER_THAN);
$c->add(PartnerPeer::ID, 100, Criteria::GREATER_THAN);
$c->addAscendingOrderByColumn(PartnerPeer::ID);
$c->setLimit(100);

$offset = 0;
$partners = PartnerPeer::doSelect($c);
while(count($partners))
{
	foreach($partners as $partner)
	{
		/* @var $partner Partner */
		PermissionPeer::enableForPartner(PermissionName::FEATURE_LIVE_STREAM, PermissionType::SPECIAL_FEATURE, $partner->getId(), 'Live Streaming', 'Live Streaming');
		PermissionPeer::enableForPartner(PermissionName::FEATURE_KALTURA_LIVE_STREAM, PermissionType::SPECIAL_FEATURE, $partner->getId(), 'Kaltura Live Streams', 'Kaltura Live Streams');
		createLivePassThruConversionProfile($partner->getId());
	}
	
	$offset += count($partners);
	$c->setOffset($offset);
	$partners = PartnerPeer::doSelect($c);
}

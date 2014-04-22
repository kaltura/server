<?php
/**
 * @package deployment
 * @subpackage live.liveParams
 *
 * Create dependency between cloud transcoding and FEATURE_KALTURA_LIVE_STREAM_TRANSCODE permission
 *
 * No need to re-run after server code deploy
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$realRun = isset($argv[1]) && $argv[1] == 'realrun';
KalturaStatement::setDryRun(!$realRun);

function isLiveConversionProfileExists($partnerId)
{
	$c = new Criteria();
	$c->add(conversionProfile2Peer::PARTNER_ID, $partnerId);
	$c->add(conversionProfile2Peer::TYPE, ConversionProfileType::LIVE_STREAM);
	$c->add(conversionProfile2Peer::STATUS, ConversionProfileStatus::ENABLED);
	
	return conversionProfile2Peer::doCount($c);
}

function createLivePassThruConversionProfile($partnerId)
{		
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

function handleCloudTranscodeProfiles($partnerId, $isTemplate)
{
	$c = new Criteria();
	$c->add(conversionProfile2Peer::STATUS, ConversionProfileStatus::ENABLED);
	$c->add(conversionProfile2Peer::PARTNER_ID, $partnerId);
	$c->add(conversionProfile2Peer::SYSTEM_NAME, 'Default_Live');
	
	$conversionProfiles = conversionProfile2Peer::doSelect($c);
	foreach($conversionProfiles as $conversionProfile)
	{
		/* @var $conversionProfile conversionProfile2 */
		if($isTemplate)
		{
			$conversionProfile->setRequiredCopyTemplatePermissions(PermissionName::FEATURE_KALTURA_LIVE_STREAM_TRANSCODE);
		}
		elseif(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_KALTURA_LIVE_STREAM_TRANSCODE, $partnerId))
		{
			$conversionProfile->setStatus(ConversionProfileStatus::DELETED);
		}
		$conversionProfile->save();
	}
}

function handleLivePartners($partnerIds)
{
	$c = new Criteria();
	$c->add(PartnerPeer::STATUS, Partner::PARTNER_STATUS_ACTIVE);
	$c->add(PartnerPeer::ID, $partnerIds, Criteria::IN);
	
	$partners = PartnerPeer::doSelect($c);
	foreach($partners as $partner)
	{
		/* @var $partner Partner */
		if(isLiveConversionProfileExists($partner->getId()))
		{
			handleCloudTranscodeProfiles($partner->getId(), $partner->getPartnerGroupType() == PartnerGroupType::TEMPLATE);
		}
		else 
		{
			createLivePassThruConversionProfile($partner->getId());
		}
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
	$partnerIds = array();
	foreach($permissions as $permission)
	{
		/* @var $permission Permission */
		PermissionPeer::enableForPartner(PermissionName::FEATURE_KALTURA_LIVE_STREAM, PermissionType::SPECIAL_FEATURE, $permission->getPartnerId(), 'Kaltura Live Streams', 'Kaltura Live Streams');
		$partnerIds[] = $permission->getPartnerId();
	}
	
	handleLivePartners($partnerIds);
	
	$offset += count($permissions);
	$c->setOffset($offset);
	$permissions = PermissionPeer::doSelect($c);
}

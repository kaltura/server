<?php

require_once (__DIR__ . '/../../bootstrap.php');

//Debug mode iis set for testing only will be removed for final version
if(count($argv) < 2)
	die("Usage: 2016_08_30_migrate_partner_media_server_configurations.php @execution_mode(debug|execute)@\n");

$executionMode = $argv[1];
if(!in_array($executionMode, array("debug", "execute")))
	die("Usage: 2016_08_30_migrate_partner_media_server_configurations.php @execution_mode(debug|execute)@, invalid execution mode\n");

function getPartnersToWorkOn()
{
	$c = new Criteria();
	$c->add(PartnerPeer::STATUS, Partner::PARTNER_STATUS_ACTIVE);
	$c->add(PartnerPeer::CUSTOM_DATA, "%mediaServersConfiguration%", Criteria::LIKE);
	
	return PartnerPeer::doSelect($c);
}

function getDeliveryProfileByHostNameAndStreamType($partnerId, $hostname, $streamType)
{
	$c = new Criteria();
	$c->add(DeliveryProfilePeer::HOST_NAME, $hostname);
	$c->add(DeliveryProfilePeer::STREAMER_TYPE, $streamType);
	$c->add(DeliveryProfilePeer::PARTNER_ID, array($partnerId, PartnerPeer::GLOBAL_PARTNER), Criteria::IN);
	$c->addDescendingOrderByColumn(DeliveryProfilePeer::PARTNER_ID);
	
	return DeliveryProfilePeer::doSelectOne($c);
}

function getValueByField($config, $filedValue)
{
	$value = null;
	
	if(isset($config[$filedValue]))
		$value = $config[$filedValue];
	if(isset($config['dc-0'][$filedValue]))
		$value = $config['dc-0'][$filedValue];
	
	return $value;
}

KalturaLog::debug("Starting partner media server config migration");

$partnerToWorkOn = getPartnersToWorkOn();
if(!count($partnerToWorkOn))
{
	KalturaLog::debug("No partners found to work on, Done!!!");
	return;	
}

foreach ($partnerToWorkOn as $partner) 
{
	/* @var $partner Partner */
	$mediaServerConfig = $partner->getMediaServersConfiguration();
	$liveDeliveryProfileConfig = array();
	$hdsDeliveryProfileId = null;
	$hlsDeliveryProfileId = null;
	
	$hdsDomain = getValueByField($mediaServerConfig, "domain");
	if($hdsDomain)
	{
		KalturaLog::debug("Found hds domain config for partner [{$partner->getId()}] value [$hdsDomain]");
		$hdsDeliveryProfile = getDeliveryProfileByHostNameAndStreamType($partner->getId(), $hdsDomain, 'hds');
		if($hdsDeliveryProfile)
			$hdsDeliveryProfileId = $hdsDeliveryProfile->getId();
	}
	
	$hlsDomain = getValueByField($mediaServerConfig, "domain-hls");
	if($hlsDomain)
	{
		KalturaLog::debug("Found hls domain config for partner [{$partner->getId()}] value [$hlsDomain]");
		$hlsDeliveryProfile = getDeliveryProfileByHostNameAndStreamType($partner->getId(), $hlsDomain, 'applehttp');
		if($hlsDeliveryProfile)
			$hlsDeliveryProfileId = $hlsDeliveryProfile->getId();
	}
	
	if(!$hdsDeliveryProfileId && !$hlsDeliveryProfileId)
	{
		KalturaLog::debug("Could not locate both HLS & HDS delivery profile ids continue to next partner");
		continue;
	}
	
	if($hdsDeliveryProfileId)
		$liveDeliveryProfileConfig["hds"] =  array($hdsDeliveryProfileId);
	
	if($hlsDeliveryProfileId)
		$liveDeliveryProfileConfig["applehttp"] =  array($hlsDeliveryProfileId);
	
	KalturaLog::debug("Setting live delivery profile config: " . print_r($liveDeliveryProfileConfig, true) . " for partnerId [{$partner->getId()}]");
	
	if($executionMode === "debug")
	{
		KalturaLog::debug("Debug mode is enabled skipping Save");
		continue;
	}
	
	$partner->setLiveDeliveryProfileIds($liveDeliveryProfileConfig);
	$partner->save();
}

KalturaLog::debug("Done partner media server config migration");

<?php

require_once (__DIR__ . '/../../bootstrap.php');

//Debug mode iis set for testing only will be removed for final version
if(count($argv) < 2)
	die("Usage: 2016_08_25_updateExternalServerNodes.php @execution_mode(debug|execute)@\n");

$executionMode = $argv[1];
if(!in_array($executionMode, array("debug", "execute")))
	die("Usage: 2016_08_25_updateExternalServerNodes.php @execution_mode(debug|execute)@, invalid execution mode");

function getWowzaServerNodeEnumValue()
{
	$c = new Criteria();
	$c->add(DynamicEnumPeer::PLUGIN_NAME, "wowza");
	$c->add(DynamicEnumPeer::ENUM_NAME, "serverNodeType");
	
	$enum = DynamicEnumPeer::doSelectOne($c);
	if(!$enum)
	{
		KalturaLog::warning("Could not locate serverNodeType of type wowza");
		die();
	}
	
	return $enum->getId();
}

function getAppleHttpDefaultDeliveryProfileId()
{
	$c = new Criteria();
	$c->add(DeliveryProfilePeer::TYPE, DeliveryProfileType::LIVE_HLS);
	$c->add(DeliveryProfilePeer::STATUS, DeliveryStatus::ACTIVE);
	$c->add(DeliveryProfilePeer::HOST_NAME, null);
	
	$deliveryProfile = DeliveryProfilePeer::doSelectOne($c);
	if(!$deliveryProfile)
	{
		KalturaLog::warning("Could not locate default empty liveHLS delivery profile");
		die();
	}
	
	return $deliveryProfile->getId();
}

function getHdsDefaultDeliveryProfileId()
{
	$c = new Criteria();
	$c->add(DeliveryProfilePeer::TYPE, DeliveryProfileType::LIVE_HDS);
	$c->add(DeliveryProfilePeer::STATUS, DeliveryStatus::ACTIVE);
	$c->add(DeliveryProfilePeer::HOST_NAME, null);
	
	$deliveryProfile = DeliveryProfilePeer::doSelectOne($c);
	if(!$deliveryProfile)
	{
		KalturaLog::warning("Could not locate default empty liveHLS delivery profile");
		die();
	}
	
	return $deliveryProfile->getId();
}

function getActiveExternalWowzaServerNode()
{
	$wowzaServerNodeType = getWowzaServerNodeEnumValue();
	
	$c = new Criteria();
	$c->add(ServerNodePeer::TYPE, $wowzaServerNodeType);
	$c->add(ServerNodePeer::STATUS, array(ServerNodeStatus::ACTIVE, ServerNodeStatus::DISABLED), Criteria::IN);
	$c->add(ServerNodePeer::PARTNER_ID, Partner::MEDIA_SERVER_PARTNER_ID, Criteria::NOT_EQUAL);
	
	return ServerNodePeer::doSelect($c);
}

$defaultHlsDeliveryProfile = getAppleHttpDefaultDeliveryProfileId();
$defaultHdsDeliveryProfile = getHdsDefaultDeliveryProfileId();
$defaultDeliveryProfileArray = array(
	"applehttp" => $defaultHlsDeliveryProfile,
	"hds" => $defaultHdsDeliveryProfile
);

KalturaLog::debug("Starting external Wowza delivery profiles update with defaultHlsDeliveryProfile [$defaultHlsDeliveryProfile] and [defaultHdsDeliveryProfile] $defaultHdsDeliveryProfile");

$externalWowzaServerNodes = getActiveExternalWowzaServerNode();
if(!count($externalWowzaServerNodes))
{
	KalturaLog::debug("No external Wowza server node found, Done!!!");
	return;
}

foreach ($externalWowzaServerNodes as $externalWowzaServerNode) 
{
	/* @var $externalWowzaServerNode WowzaMediaServerNode */
	KalturaLog::debug("updating wowza server node with id [{$externalWowzaServerNode->getId()}] with default delivery profile ids");
	$externalWowzaServerNode->setDeliveryProfileIds($defaultDeliveryProfileArray);
	
	if($executionMode === "debug")
	{
		KalturaLog::debug("Debug mode is enabled skipping Save");
		continue;
	}
	$externalWowzaServerNode->save();
}

KalturaLog::debug("Done external Wowza delivery profiles update with defaultHlsDeliveryProfile [$defaultHlsDeliveryProfile] and [defaultHdsDeliveryProfile] $defaultHdsDeliveryProfile");

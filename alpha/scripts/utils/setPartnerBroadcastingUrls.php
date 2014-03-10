<?php
require_once(dirname(__FILE__).'/../bootstrap.php');


/*partner Id*/
$partnerId = null;
$primaryBroadcastUrl = "";
$secondaryBroadcastUrl = "";

if (!$partnerId)
{
	die ("Missing parameter");
}

$partner = PartnerPeer::retrieveByPK($partnerId);
$partner->setBroadcastUrlManager('kPartnerBroadcastUrlManager');
$partner->setPrimaryBroadcastUrl($primaryBroadcastUrl);
$partner->setSecondaryBroadcastUrl($secondaryBroadcastUrl);

$liveStreamConfigurations = array ('{playback format}' => '{url}');
$partner->setLiveStreamPlaybackUrlConfigurations($liveStreamConfigurations);
$partner->save();

<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

if ($argc < 4)
	die ("Required parameters not received. Run script in the following format: php setPartnerBroadcastingUrls.php {partnerId} {parimary broadcast URL} {playback URL} [{secondary broadcast URL}]");

$partnerId = $argv[1];
$primaryBroadcatUrl =  $argv[2];
$playbackUrl = $argv[3];

$secondaryBroadcastUrl = null;
if (isset($argv[4]))
	$secondaryBroadcastUrl = $argv[4];

$partner = PartnerPeer::retrieveByPK($partnerId);
if (!$partner)
{
	die ("Partner with id {$partnerId} not found.");
}

$partner->setBroadcastUrlManager('kPartnerBroadcastUrlManager');
$partner->setPrimaryBroadcastUrl($primaryBroadcatUrl);
$partner->setSecondaryBroadcastUrl($secondaryBroadcastUrl);

$liveStreamConfigurations = array ('http' => $playbackUrl);
$partner->setLiveStreamPlaybackUrlConfigurations($liveStreamConfigurations);
$partner->save();

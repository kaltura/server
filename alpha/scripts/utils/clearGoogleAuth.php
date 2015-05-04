<?php
/**
 * This script lists all distribution profiles and check if they have a valid authentication info
 */


chdir('/opt/kaltura/Jupiter-10.9.0/alpha/scripts/utils');

require_once('/opt/kaltura/Jupiter-10.9.0/alpha/scripts/bootstrap.php');

$realRun = in_array('realrun', $argv);
KalturaStatement::setDryRun(!$realRun);

if (count($argv) < 2)
{
	die ("Partner id is required input.\n");
}

$partnerId = intval($argv[1]);
if($partnerId <= 0)
{
	die ("Partner id must be a real partner id.\n");
}

$customDataKey = null;
if(!isset($argv[2]))
{
	$distributionProfile = DistributionProfilePeer::retrieveByPK(intval($argv[2]));
	$objectIdentifier = null;
	if($distributionProfile instanceof YoutubeApiDistributionProfile)
	{
		$appId = YoutubeApiDistributionPlugin::GOOGLE_APP_ID;
		$objectIdentifier = md5(get_class($distributionProfile) . $distributionProfile->getUsername());
	}
	elseif($distributionProfile instanceof YouTubeDistributionProfile)
	{
		$appId = YoutubeApiDistributionPlugin::GOOGLE_APP_ID;
		$objectIdentifier = $distributionProfile->getId();
	}
	else 
	{
		die ("Distribution-profile [" . $argv[2] . "] not found.\n");
	}
	$customDataKey = $appId . '_' . $objectIdentifier;
}
$partner = PartnerPeer::retrieveByPK($partnerId);
$partner->removeFromCustomData($customDataKey, 'googleAuth');
$partner->save();

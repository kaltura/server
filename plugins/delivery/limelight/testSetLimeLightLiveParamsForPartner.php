<?php
error_reporting(E_ALL);

require_once(dirname(__FILE__).'/../../../alpha/scripts/bootstrap.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "limelight", "*"));
//KAutoloader::setClassMapFilePath(KALTURA_ROOT_PATH.'/cache/scripts/limelight/classMap.cache');
KAutoloader::setClassMapFilePath(KALTURA_ROOT_PATH.'/cache/scripts/' . basename(__FILE__) . '.cache');
KAutoloader::register();

$partnerId = 101;

$partner = PartnerPeer::retrieveByPK($partnerId);

if(!$partner)
{
    die("No such partner with id [$partnerId]");
}


//$limeLightLiveParams = LimeLightPlugin::getLimeLightLiveParams($partner);
//$defaultLiveSourceType = $partner->getDefaultLiveStreamEntrySourceType();
//$liveStreamEnabled = $partner->getLiveStreamEnabled();
//$customData = unserialize($partner->getCustomData());
//$customData = $partner->getCustomDataObj();


$ST = $partner->getDefaultLiveStreamEntrySourceType();
$apiVal = kPluginableEnumsManager::coreToApi('EntrySourceType', $ST);

//$entryId = '0_y3sxhrol';
//$entry = entryPeer::retrieveByPK($entryId);

//$purl = $entry->getPrimaryBroadcastingUrl();
//$surl = $entry->getSecondaryBroadcastingUrl();

//$primary = $limeLightLiveParams->getLimeLightLivePrimaryBroadcastingURL();


var_dump($ST);


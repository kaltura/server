<?php

require_once(dirname(__FILE__).'/../bootstrap.php');


/*partner Id*/
$partnerId = null;

/*partner's akamai live params*/
$akamaiLiveUniversalUsername = null;
$akamaiLiveUniversalPassword = null;
$akamaiLiveUniversalDomain = null;
$akamaiLiveUniversalStreamType = null;
$akamaiLiveUniversalEmailId = null;
$akamaiLiveUniversalPrimaryContact = null;
$akamaiLiveUniversalSecondaryContact = null;
/*optional - set manually the hls/hds url. MAKE SURE TO INCLUDE the /i/ or /z/*/
$akamaiLiveUniversalUrls = array( PlaybackProtocol::APPLE_HTTP =>null, PlaybackProtocol::AKAMAI_HDS =>null);

// don't add to database if one of the parameters is missing or is an empty string
if (!($partnerId) || !($akamaiLiveUniversalUsername) || !($akamaiLiveUniversalPassword) || !($akamaiLiveUniversalDomain) || !($akamaiLiveUniversalStreamType)
				  || !($akamaiLiveUniversalEmailId) || !($akamaiLiveUniversalPrimaryContact) || !($akamaiLiveUniversalSecondaryContact))
{
	die ('Missing parameter');
}

$partner = PartnerPeer::retrieveByPK($partnerId);

if(!$partner)
{
    die("No such partner with id [$partnerId].".PHP_EOL);
}
//setting custom data fields of the partner
$liveParams = array();
$liveParams["systemUserName"] = $akamaiLiveUniversalUsername;
$liveParams["systemPassword"] = $akamaiLiveUniversalPassword;
$liveParams["domainName"] = $akamaiLiveUniversalDomain; 
$liveParams["streamType"] = $akamaiLiveUniversalStreamType;
$liveParams["primaryContact"] = $akamaiLiveUniversalPrimaryContact;
$liveParams["secondaryContact"] = $akamaiLiveUniversalSecondaryContact;
$liveParams["notificationEmail"] = $akamaiLiveUniversalEmailId;
$liveParams["basePlaybackUrls"] = $akamaiLiveUniversalUrls;

$partner->setAkamaiUniversalStreamingLiveParams($liveParams);
$partner->save();	

echo "Done.";

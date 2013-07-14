<?php

require_once(dirname(__FILE__).'/../bootstrap.php');


/*partner Id*/
$partnerId = null;

/*partner's akamai live params*/
$akamaiLiveWsdlUsername = null;
$akamaiLiveWsdlPassword = null;
$akamaiLiveCpCode = null;
$akamaiLiveEmailId = null;
$akamaiLivePrimaryContact = null;
$akamaiLiveSecondaryContact = null;


// don't add to database if one of the parameters is missing or is an empty string
if (!($partnerId) || !($akamaiLiveWsdlUsername) || !($akamaiLiveWsdlPassword) || !($akamaiLiveCpCode)
				  || !($akamaiLiveEmailId) || !($akamaiLivePrimaryContact) || !($akamaiLiveSecondaryContact))
{
	die ('Missing parameter');
}

$partner = PartnerPeer::retrieveByPK($partnerId);

if(!$partner)
{
    die("No such partner with id [$partnerId].".PHP_EOL);
}
//setting custom data fields of the partner
$akamaiLiveParams = new kAkamaiLiveParams();
$akamaiLiveParams->setAkamaiLiveWsdlUsername($akamaiLiveWsdlUsername);
$akamaiLiveParams->setAkamaiLiveWsdlPassword($akamaiLiveWsdlPassword);
$akamaiLiveParams->setAkamaiLiveCpcode($akamaiLiveCpCode);
$akamaiLiveParams->setAkamaiLiveEmailId($akamaiLiveEmailId);
$akamaiLiveParams->setAkamaiLivePrimaryContact($akamaiLivePrimaryContact);
$akamaiLiveParams->setAkamaiLiveSecondaryContact($akamaiLiveSecondaryContact);
$partner->setAkamaiLiveParams($akamaiLiveParams);
$partner->save();	

echo "Done.";

<?php

require_once(dirname(__FILE__).'/../bootstrap.php');


/*partner Id*/
$partnerId = '100';

/*partner's akamai live params*/
$akamaiLiveWsdlUsername = 'jonathan.kanarek@kaltura.com';
$akamaiLiveWsdlPassword = 'uri1226';
$akamaiLiveCpCode = '77659';
$akamaiLiveEmailId = 'jonathan.kanarek@kaltura.com';
$akamaiLivePrimaryContact = 'jonathan.kanarek@kaltura.com';
$akamaiLiveSecondaryContact = 'jonathan.kanarek@kaltura.com';

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
$partner->setAkamaiLiveCpcode($akamaiLiveCpCode);
$partner->setAkamaiLiveEmailId($akamaiLiveEmailId);
$partner->setAkamaiLivePrimaryContact($akamaiLivePrimaryContact);
$partner->setAkamaiLiveSecondaryContact($akamaiLiveSecondaryContact);
$partner->setAkamaiLiveWsdlPassword($akamaiLiveWsdlPassword);
$partner->setAkamaiLiveWsdlUsername($akamaiLiveWsdlUsername);
$partner->save();	

echo "Done.";

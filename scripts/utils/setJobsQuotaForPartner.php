<?php

require_once(dirname(__FILE__).'/../bootstrap.php');


/*partner Id*/
$partnerId = 100;

/*partner's akamai live params*/
$quota =  array ('*' => 6, '4-*' =>2 , '5-2' => 3); 

// don't add to database if one of the parameters is missing or is an empty string
if (!($partnerId) || !($quota) )
{
	die ('Missing parameter');
}

$partner = PartnerPeer::retrieveByPK($partnerId);

if(!$partner)
{
    die("No such partner with id [$partnerId].".PHP_EOL);
}

//setting custom data fields of the partner
$partner->setJobTypeQuota($quota);
$partner->save();	

echo "Done.";


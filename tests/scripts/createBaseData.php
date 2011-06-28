<?php
require_once dirname(__FILE__) . '\..\bootstrap.php';

$config = new KalturaConfiguration();
$config->serviceUrl = 'http://hudsontest2.kaltura.dev/';
$client = new KalturaClient($config);
$cmsPassword = 'Roni123!';
$partner = new KalturaPartner();
$partner->name = 'Test Partner';
$partner->adminName = 'Test admin name'; 
$partner->adminEmail = "test@mailinator.com";
$partner->description = "partner for tests";
$results = $client->partner->register($partner, $cmsPassword);

KalturaGlobalData::setData("@TEST_PARTNER_ID@", $results->id);
KalturaGlobalData::setData("@TEST_PARTNER_ADMIN_SECRET@", $results->adminSecret);
KalturaGlobalData::setData("@TEST_PARTNER_SECRET@", $results->secret);

print("Results are: " . print_r($results,true));
<?php
require_once(__DIR__ . '/../bootstrap.php');

//Debug mode iis set for testing only will be removed for final version
if(count($argv) < 7)
	die("Usage: @name@ @description@ @system_name@ @storage_url@ @bucket_name@ @partner_id@");

$name = $argv[1];
$description = $argv[2];
$system_name = $argv[3];
$storage_url = $argv[4];
$bucket_name = $argv[5];
$partnerId = $argv[6];

$storageProfile = new StorageProfile();
$storageProfile->setName($name);
$storageProfile->setDesciption($description);
$storageProfile->setSystemName($system_name);
$storageProfile->setStorageUrl($storage_url);
$storageProfile->setStorageBaseDir("/$bucket_name/");
$storageProfile->setProtocol(StorageProfileProtocol::KALTURA_DC);

$storageProfile->setPartnerId($partnerId);
$storageProfile->setStatus(1);
$storageProfile->setStorageFtpPassiveMode(0);
$storageProfile->setPathPrefix("/s3/bucket/$bucket_name");
$storageProfile->setPathManagerClass("kS3PathManager");
$storageProfile->setDeliveryStatus(1);

$storageProfile->save();
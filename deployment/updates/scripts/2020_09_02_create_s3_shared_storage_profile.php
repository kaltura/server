<?php
/**
 * @package deployment
 */

define('DEPLOYMENT_DIR', realpath(__DIR__ . '/../..'));
require_once (DEPLOYMENT_DIR . '/bootstrap.php');


//Debug mode iis set for testing only will be removed for final version
if(count($argv) < 7)
	die("Usage: @name@ @description@ @system_name@ @storage_url@ @bucket_name@ @packager_url@ @ID (optional)@");

$name = $argv[1];
$description = $argv[2];
$system_name = $argv[3];
$storage_url = $argv[4];
$bucket_name = $argv[5];
$packager_url = $argv[6];

$id = isset($argv[7]) ? $argv[7] : null;

$storageProfile = new StorageProfile();
$storageProfile->setName($name);
$storageProfile->setDesciption($description);
$storageProfile->setSystemName($system_name);
$storageProfile->setStorageUrl($storage_url);
$storageProfile->setStorageBaseDir($bucket_name);
$storageProfile->setRegularPackagerUrl($packager_url);

$storageProfile->setPartnerId(0);
$storageProfile->setStatus(1);
$storageProfile->setProtocol(0);
$storageProfile->setStorageFtpPassiveMode(0);
$storageProfile->setPathPrefix("/s3");
$storageProfile->setPathManagerClass("kS3PathManager");
$storageProfile->setDeliveryStatus(1);


if(isset($id))
{
	$storageProfile->setId();
}

$storageProfile->save();

<?php
if($argc != 3)
	die ("Usage: drmProfileCopyBetweenAccounts <srcDrmProfileId> <targetPartnerId>" . PHP_EOL);
require_once(__DIR__ . '/../bootstrap.php');
$srcDrmProfileId = $argv[1];
$targetPartnerId = $argv[2];

echo 'Copy drmProfileId ' . $srcDrmProfileId . ' to partner ' . $targetPartnerId . PHP_EOL;
$dbDrmProfile = DrmProfilePeer::retrieveByPK($srcDrmProfileId);
if (!$dbDrmProfile)
	die('Did not find DRM profile with id of ' . $srcDrmProfileId);

$newProfile = $dbDrmProfile->copy();
$newProfile->setPartnerId($targetPartnerId);
$newProfile->save();
KalturaLog::debug('Done');
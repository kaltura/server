<?php

// ---------------------------------------------------------------------------
$xsltFilePath = '';         //TODO: change to valid xslt file path
$metadataProfileId = null;    //TODO: change to a valid metadata profile id
// ---------------------------------------------------------------------------

if (!$xsltFilePath) {
    die('ERROR - Missing parameter [$xsltFilePath]'.PHP_EOL);
}
if (!$metadataProfileId) {
    die('ERROR - Missing parameter [$metadataProfileId]'.PHP_EOL);
}
if (!file_exists($xsltFilePath)) {
    die('ERROR - Cannot find file at ['.$xsltFilePath.']'.PHP_EOL);
}


require_once(dirname(__FILE__).'/../bootstrap.php');

KalturaPluginManager::addPlugin('MetadataPlugin');
$dbMetadataProfile = MetadataProfilePeer::retrieveById($metadataProfileId);
if (!$dbMetadataProfile) {
    die('ERROR - Cannot find metadata profile with id ['.$metadataProfileId.']'.PHP_EOL);
}

$dbMetadataProfile->incrementXsltVersion();
$dbMetadataProfile->save();

$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_XSLT);
kFileSyncUtils::moveFromFile($xsltFilePath, $key, true, true);

echo 'Done'.PHP_EOL;

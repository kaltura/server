<?php

require_once(dirname(__FILE__).'/../bootstrap.php');
require_once(dirname(__FILE__).'/../../api_v3/bootstrap.php');

// ---------------------------------------------------------------------------
$xsltFilePath = null;         //TODO: change to valid xslt file path
$metadataProfileId = null;    //TODO: change to a valid metadata profile id
// ---------------------------------------------------------------------------

if (!$xsltFilePath) {
    die('Missing parameter [$xsltFilePath]');
}
if (!$metadataProfileId) {
    die('Missing parameter [$metadataProfileId]');
}
if (!file_exists($xsltFilePath)) {
    die('Cannot find file at ['.$xsltFilePath.']');
}

$dbMetadataProfile = MetadataProfilePeer::retrieveById($metadataProfileId);
if (!$dbMetadataProfile) {
    die('Cannot find metadata profile with id ['.$metadataProfileId.']');
}

$dbMetadataProfile->incrementXsltVersion();
$dbMetadataProfile->save();

$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_XSLT);
kFileSyncUtils::moveFromFile($xsltFilePath, $key);

echo 'Done'.PHP_EOL;

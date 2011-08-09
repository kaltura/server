<?php

// ---------------------------------------------------------------------------
$xsltFilePath = null;         //TODO: change to valid xslt file path
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
require_once(dirname(__FILE__).'/../../api_v3/bootstrap.php');


KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::setClassMapFilePath(KALTURA_ROOT_PATH.'/cache/scripts/classMap'.uniqid().'.cache');
KAutoloader::register();


$dbMetadataProfile = MetadataProfilePeer::retrieveById($metadataProfileId);
if (!$dbMetadataProfile) {
    die('ERROR - Cannot find metadata profile with id ['.$metadataProfileId.']'.PHP_EOL);
}

$dbMetadataProfile->incrementXsltVersion();
$dbMetadataProfile->save();

$key = $dbMetadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_XSLT);
kFileSyncUtils::moveFromFile($xsltFilePath, $key, true, true);

echo 'Done'.PHP_EOL;

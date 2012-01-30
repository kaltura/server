<?php

if($argc != 3)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php exportToNetStorage.php {partner id} {storage profile id} {max number of entries}(optional)\n";
	exit;
} 
$partnerId = $argv[1];
$storageProfileId = $argv[2];
$maxEntriesToExport = isset($argv[3]) ? $argv[3] : -1;

if (empty($maxEntriesToExport) || $maxEntriesToExport <= 0) {
    $maxEntriesToExport = -1;
}

set_time_limit(0);
ini_set("memory_limit","1024M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "sphinx_search", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
if(!$storageProfile)
{
	echo "Invalid storage profile id [$storageProfileId].\n\n";
	echo "Usage: php exportToNetStorage.php {partner id} {storage profile id}\n";
	exit;
}

$moreEntries = true;
$limitPerLoop = 500;
$lastIntId = -1;
$totalExported = 0;

while ($moreEntries)
{
    $currentExported = 0;
    $c = new Criteria();
    $c->add(entryPeer::PARTNER_ID, $partnerId);
    $c->add(entryPeer::INT_ID, $lastIntId, Criteria::GREATER_THAN);
    $c->addAscendingOrderByColumn(entryPeer::INT_ID);
    $c->setLimit($limitPerLoop);
    $entries = entryPeer::doSelect($c);
    foreach($entries as $entry)
    {
        $lastIntId = $entry->getIntId();
    	$keys = array();
    	$keys[] = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
    	$keys[] = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
    	
    	$flavors = assetPeer::retrieveReadyByEntryId($entry->getId());
    	foreach($flavors as $flavor)
    		$keys[] = $flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
    	
    	foreach($keys as $index => $key)
    	{
    		if(!kFileSyncUtils::fileSync_exists($key))
    		{
    			unset($keys[$index]);			
    			continue;
    		}
    	
    		if(kFileSyncUtils::getReadyExternalFileSyncForKey($key, $storageProfileId))
    			unset($keys[$index]);
    	}
    	
    	if(!count($keys))
    	{
    		echo $entry->getId() . " - has no keys to export\n";
    		continue;
    	}
    	
    	foreach($keys as $key)
    	{
    		$fileSync = kFileSyncUtils::createPendingExternalSyncFileForKey($key, $storageProfile);
    		$srcFileSyncLocalPath = kFileSyncUtils::getLocalFilePathForKey($key, true);
    		kJobsManager::addStorageExportJob(null, $entry->getId(), $partnerId, $storageProfile, $fileSync, $srcFileSyncLocalPath);
    	}
    		
    	echo $entry->getId() . " - " . count($keys) . " keys exported\n\n";
    	
    	$totalExported++;
    	if ($maxEntriesToExport > 0 && $totalExported >= $maxEntriesToExport) {
    	    echo 'Max entries limit of ['.$maxEntriesToExport.'] reached - stopping executin';
    	    $moreEntries = false;
    	    break;
    	}
    	
    	usleep(100);
    }
    
    if (count($entries) < $limitPerLoop) {
        $moreEntries = false;
    }
    $entries = null;
    entryPeer::clearInstancePool();
}

echo "Done\n";

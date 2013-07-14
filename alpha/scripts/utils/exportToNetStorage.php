<?php
require_once (dirname ( __FILE__ ) . '/../bootstrap.php');

if($argc < 3)
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

$storageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
if(!$storageProfile)
{
	echo "Invalid storage profile id [$storageProfileId].\n\n";
	echo "Usage: php exportToNetStorage.php {partner id} {storage profile id}\n";
	exit;
}

$flavorParamsIds = $storageProfile->getFlavorParamsIds();
KalturaLog::log("flavorParamsIds [$flavorParamsIds]");

$flavorParamsArr = null;
if(!is_null($flavorParamsIds) && strlen(trim($flavorParamsIds)))
	$flavorParamsArr = explode(',', $flavorParamsIds);

$moreEntries = true;
$maxConcurrentJobs = 20;
$totalExported = 0;
$lastCreatedAt = null;
$processedIds = array();

$nonFinalBatchStatuses = array(
    BatchJob::BATCHJOB_STATUS_PENDING,
	BatchJob::BATCHJOB_STATUS_QUEUED,
	BatchJob::BATCHJOB_STATUS_PROCESSING,
	BatchJob::BATCHJOB_STATUS_PROCESSED,
	BatchJob::BATCHJOB_STATUS_MOVEFILE,
    );

while ($moreEntries)
{
    $c = new Criteria();
    $c->addAnd(BatchJobPeer::PARTNER_ID, $partnerId);
    $c->addAnd(BatchJobPeer::JOB_TYPE, BatchJobType::STORAGE_EXPORT);
    $c->addAnd(BatchJobPeer::STATUS, $nonFinalBatchStatuses, Criteria::IN);
    $batchCount = BatchJobPeer::doCount($c, false, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3));
    
    if ($batchCount >= $maxConcurrentJobs)
    {
    	sleep(30);
    	continue;
    }
    
    $curLimit = $maxConcurrentJobs - $batchCount;
    
    $currentExported = 0;
    $c = new Criteria();
    $c->add(entryPeer::PARTNER_ID, $partnerId);
	if ($lastCreatedAt)
		$c->addAnd(entryPeer::CREATED_AT, $lastCreatedAt, Criteria::LESS_EQUAL);
	$c->addDescendingOrderByColumn(entryPeer::CREATED_AT);
    $c->setLimit($curLimit);
    $entries = entryPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3));
	
    foreach($entries as $entry)
    {
		if (in_array($entry->getId(), $processedIds))
			continue;
		$processedIds[] = $entry->getId();
		
		$lastCreatedAt = $entry->getCreatedAt(null);
		
    	$keys = array();
    	$keys[] = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM);
    	$keys[] = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
    	
    	$flavors = assetPeer::retrieveReadyFlavorsByEntryId($entry->getId());
    	foreach($flavors as $flavor)
    	{
			if(!$flavorParamsArr || in_array($flavor->getFlavorParamsId(), $flavorParamsArr))
				$keys[] = $flavor->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
    	}
    	
    	foreach($keys as $index => $key)
    	{
    		if(!kFileSyncUtils::fileSync_exists($key))
    		{
    			unset($keys[$index]);			
    			continue;
    		}
    	
    		if(kFileSyncUtils::getReadyExternalFileSyncForKey($key, $storageProfileId))
    			unset($keys[$index]);
    			
    	    if (!kFileSyncUtils::getReadyInternalFileSyncForKey($key)) {
    	        echo 'file sync key does not have an internal file -'.serialize($key).PHP_EOL;
    	        unset($keys[$index]);
    	    }
    			
    	}
    	
    	if(!count($keys))
    	{
    		echo $entry->getId() . " - has no keys to export\n";
    		continue;
    	}
    	
    	foreach($keys as $key)
    	{
    		$fileSync = kFileSyncUtils::createPendingExternalSyncFileForKey($key, $storageProfile);

    		$dcFileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($key);
    		
    		/* @var $dcFileSync FileSync */
    		$srcFileSyncLocalPath = $dcFileSync->getFileRoot() . $dcFileSync->getFilePath();
    		kJobsManager::addStorageExportJob(null, $entry->getId(), $partnerId, $storageProfile, $fileSync, $srcFileSyncLocalPath, true, $dcFileSync->getDc());
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
    
    if (count($entries) < $curLimit) {
        $moreEntries = false;
    }
    $entries = null;
    kMemoryManager::clearMemory();
}

echo "Done\n";

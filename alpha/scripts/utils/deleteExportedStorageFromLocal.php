<?php

/**
 * This script deletes local DC filesyncs that were exported succefuly to a given remote storage location.
 * Before execution, please make sure that the partner is configured to stream files from the remote storage only!!
 */


require_once(__DIR__.'/../bootstrap.php');

// -----------------------------
//  get command line parameters
// -----------------------------

if($argc < 3)
{
	echo 'Arguments missing.'.PHP_EOL;
	echo 'Usage: php '.__FILE__.' {partner id} {storage profile id}'.PHP_EOL;
	exit;
} 

$partnerId = $argv[1];
$storageProfileId = $argv[2];


// -------------------
//  verify parameters
// -------------------

if (!$partnerId || !PartnerPeer::retrieveByPK($partnerId))
{
    echo 'Invalid partner id ['.$partnerId.']'.PHP_EOL;
    die();
}

if (!$storageProfileId || !StorageProfilePeer::retrieveByPK($storageProfileId))
{
    echo 'Invalid storage profile id ['.$storageProfileId.']'.PHP_EOL;
    die();
}

// -----------------------------
//  loop through all file syncs
// -----------------------------

$moreFileSyncs = true;
$lastFileSyncId = null;
$loopLimit = 500;

while ($moreFileSyncs)
{
    
    // ----------------------------
    //  get next remote file syncs
    // ----------------------------
    
    $exportedFileSyncsCriteria = new Criteria();
    $exportedFileSyncsCriteria->setLimit($loopLimit);
    $exportedFileSyncsCriteria->addAscendingOrderByColumn(FileSyncPeer::ID);
    $exportedFileSyncsCriteria->addAnd(FileSyncPeer::DC, $storageProfileId, Criteria::EQUAL);
    $exportedFileSyncsCriteria->addAnd(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY, Criteria::EQUAL);
    $exportedFileSyncsCriteria->addAnd(FileSyncPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
    if ($lastFileSyncId) {
        $exportedFileSyncsCriteria->addAnd(FileSyncPeer::ID, $lastFileSyncId, Criteria::GREATER_THAN);
    }
    
    $exportedFileSyncs = FileSyncPeer::doSelect($exportedFileSyncsCriteria, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3));
        
    
    // -----------------------------------------------
    //  delete the local dcs copies of each file sync
    // -----------------------------------------------
    
    foreach ($exportedFileSyncs as $exportedFileSync)
    {
        $lastFileSyncId = $exportedFileSync->getId();
        
        $syncKey = kFileSyncUtils::getKeyForFileSync($exportedFileSync);
        echo 'Deleting file sync key - '.serialize($syncKey).PHP_EOL;
        kFileSyncUtils::deleteSyncFileForKey($syncKey, false, true); // 3rd param = true -> only delete from local dcs
    }
    
    
    // --------------------------
    //  check if loop should end
    // --------------------------
    
    if (count($exportedFileSyncs) < $loopLimit)
    {
        $moreFileSyncs = false;
    }
    
    $exportedFileSyncs = null;
    kMemoryManager::clearMemory();
    
}

echo '-- Done --'.PHP_EOL;

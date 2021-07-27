<?php

require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 3)
{
    die("Usage: php $argv[0] <batchJobLocksFile> <batchVersion> [realrun | dryrun]"."\n");
}

$dryrun = true;
if($argc == 4 && strtolower($argv[3]) == "realrun")
{
    $dryrun = false;
}
KalturaStatement::setDryRun($dryrun);
KalturaLog::debug("dryrun value: [$dryrun]");


$batchVersion = $argv[2];
$count=0;

$f = fopen($argv[1], 'r');

while($jobLockId = trim(fgets($f)))
{
    $batchLock = BatchJobLockPeer::retrieveByPK($jobLockId);
    if(!$batchLock)
    {
        continue;
    }

    $count++;
    if($count % 100 == 0)
    {
        KalturaLog::debug("Sleep for 5 sec, Total batch job locks processed [$count]");
        kMemoryManager::clearMemory();
        sleep(5);
    }

    try
    {
        $batchLock->setBatchVersion($batchVersion);
        $batchLock->save();
    }
    catch(Exception $e)
    {
        KalturaLog::debug("Batch Job Lock Id: $jobLockId  save error");
        KalturaLog::debug($e->getMessage());
    }
}
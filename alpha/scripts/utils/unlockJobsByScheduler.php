<?php
require_once(__DIR__ . '/../bootstrap.php');

if($argc != 2)
 	die ('Usage: php unlockJobsByScheduler.php {hostname}.' . PHP_EOL);

$host = argv[1];
echo "Running for $host\n";

$id = SchedulerPeer::getConfiguredIdByHostName($host);

if (!$id)
{
        echo "Could not find scheduler id for scheduler.\n";
        exit(0);
}

echo "Found Scheduler configured Id: $id \n";

exit(0);
$c = new Criteria();
$c->add(BatchJobLockPeer::SCHEDULER_ID, $scheduler->getConfiguredId());
$batchLocks = BatchJobLockPeer::doSelect( $c);
echo "Got " . count($batchLocks) . " Job to reset from machine ID: " . $argv[1] . PHP_EOL;

foreach ($batchLocks as $batchLock){
        echo "resetting jobLock " .$batchLock->getId() . PHP_EOL;
        $batchLock->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
        $batchLock->setSchedulerId(null);
        $batchLock->setWorkerId(null);
        $batchLock->setBatchIndex(null);
        $batchLock->setExpiration(time());
        $batchLock->save();
}

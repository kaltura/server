<?php
require_once(__DIR__ . '/../bootstrap.php');

$host = gethostname();
echo "Running for $host";

$c = new Criteria();
$c->add(SchedulerPeer::HOST, $host);
$scheduler = SchedulerPeer::doSelectOne( $c);
if (!$scheduler)
{
	echo "Could not find scheduler for host.\n";
	exit(0);
}

if (!$scheduler->getConfiguredId())
{
	echo "Could not find scheduler id for scheduler.\n";
	exit(0);
}

echo "Found Scheduler configured Id: ". $scheduler->getConfiguredId(). "\n";

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

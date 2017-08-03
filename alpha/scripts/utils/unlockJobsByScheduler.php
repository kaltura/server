<?php
if($argc != 2)
	die ('Usage: php unlockJobsByScheduler.php {scheduler id}.' . PHP_EOL);
require_once(__DIR__ . '/../bootstrap.php');

$c = new Criteria();
$c->add(BatchJobLockPeer::SCHEDULER_ID, $argv[1]);
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
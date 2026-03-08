<?php

require_once(__DIR__ . '/../bootstrap.php');

// Disable instance pooling to reduce memory usage
Propel::disableInstancePooling();
kEventsManager::enableEvents(false);
kEventsManager::enableDeferredEvents(false);

// -----------------------------
//  Get command line parameters
// -----------------------------

if ($argc < 2) {
	echo 'Arguments missing.' . PHP_EOL;
	echo 'Usage: php ' . __FILE__ . ' {partner id}' . PHP_EOL;
	exit;
}

$partnerId = $argv[1];

// -------------------
//  Verify parameters
// -------------------

if (!$partnerId || !PartnerPeer::retrieveByPK($partnerId)) {
	echo 'Invalid partner id [' . $partnerId . ']' . PHP_EOL;
	exit;
}

// -----------------------------
//  Loop through all batch job log files
// -----------------------------

$lastBatchJobLogId = null; // Track the last processed log ID
$loopLimit = 1000; // Number of logs to process in each batch

do {
	$c = new Criteria();
	$c->addSelectColumn(BatchJobLogPeer::ID);
	$c->addAnd(BatchJobLogPeer::PARTNER_ID, $partnerId);
	$c->addAscendingOrderByColumn(BatchJobLogPeer::ID);
	$c->setLimit($loopLimit);

	// Fetch logs greater than the last processed ID
	if ($lastBatchJobLogId) {
		$c->addAnd(BatchJobLogPeer::ID, $lastBatchJobLogId, Criteria::GREATER_THAN);
	}

	// Retrieve logs
	$batchJobLogs = BatchJobLogPeer::doSelect($c);
	$count = count($batchJobLogs);

	// Delete each log object
	foreach ($batchJobLogs as $log) {
		BatchJobLogPeer::doDelete($log);
		echo 'Deleted Batch Job Log with ID: ' . $log->getId() . PHP_EOL; // Log the deletion
	}

	// Update the last processed log ID to the last object in the batch
	if ($count > 0) {
		$lastBatchJobLogId = end($batchJobLogs)->getId();
	}

	// Clear memory to avoid performance issues
	kMemoryManager::clearMemory();

	// Sleep for 10 seconds after processing 1000 logs
	sleep(10);

} while ($count == $loopLimit); // Continue while batches are still full (i.e., 1000 logs)

echo '-- Done --' . PHP_EOL;

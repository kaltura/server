<?php
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Will run the KAsyncFileSyncImportCloser
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
 */

$instance = new KAsyncFileSyncImportCloser();
$instance->run();
$instance->done();
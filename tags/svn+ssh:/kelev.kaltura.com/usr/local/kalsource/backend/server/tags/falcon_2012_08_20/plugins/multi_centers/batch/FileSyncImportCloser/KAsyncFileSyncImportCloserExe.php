<?php
require_once('bootstrap.php');

/**
 * Will run the KAsyncFileSyncImportCloser
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
 */

$instance = new KAsyncFileSyncImportCloser();
$instance->run();
$instance->done();
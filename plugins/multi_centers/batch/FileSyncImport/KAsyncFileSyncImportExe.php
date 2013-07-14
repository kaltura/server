<?php
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Will run the KAsyncFileSyncImport
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
 */

$instance = new KAsyncFileSyncImport();
$instance->run();
$instance->done();
<?php
require_once('bootstrap.php');

/**
 * Will run the KAsyncFileSyncImport
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
 */

$instance = new KAsyncFileSyncImport();
$instance->run();
$instance->done();
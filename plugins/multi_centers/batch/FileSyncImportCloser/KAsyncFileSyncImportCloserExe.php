<?php
require_once('bootstrap.php');

/**
 * Will run the KAsyncFileSyncImportCloser
 *
 * @package Scheduler
 * @subpackage FileSyncImportCloser
 */

$instance = new KAsyncFileSyncImportCloser();
$instance->run();
$instance->done();
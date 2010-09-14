<?php
require_once('bootstrap.php');

/**
 * Will run the KAsyncFileSyncImport
 *
 * @package Scheduler
 * @subpackage FileSyncImport
 */

$instance = new KAsyncFileSyncImport();
$instance->run();
$instance->done();
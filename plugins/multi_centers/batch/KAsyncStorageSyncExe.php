<?php
require_once(__DIR__ . "/../../../batch/bootstrap.php");
/**
 * Will run KAsyncStorageSync.class.php 
 * 
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
 */


$instance = new KAsyncStorageSync();
$instance->run(); 
$instance->done();
?>
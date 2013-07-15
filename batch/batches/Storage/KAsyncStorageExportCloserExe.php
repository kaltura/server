<?php
/**
 * Will run KAsyncStorageDelete.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */

chdir(dirname( __FILE__ ) . "/../../");
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncStorageExportCloser();
$instance->run(); 
$instance->done();

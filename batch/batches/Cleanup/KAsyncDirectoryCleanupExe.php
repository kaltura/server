<?php
require_once("bootstrap.php");

/**
 * Will run the KAsyncDirectoryCleanup 
 *
 * @package Scheduler
 * @subpackage Cleanup
 */

$instance = new KAsyncDirectoryCleanup ( );
$instance->run(); 
$instance->done();
?>
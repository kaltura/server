<?php
/**
 * Will run the KAsyncDirectoryCleanup 
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
require_once("bootstrap.php");

$instance = new KAsyncTagIndex ();
$instance->run(); 
$instance->done();
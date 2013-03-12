<?php
/**
 * Will run the KAsyncDirectoryCleanup 
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
require_once("bootstrap.php");

$instance = new KAsyncTagResolve ();
$instance->run(); 
$instance->done();
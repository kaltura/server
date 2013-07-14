<?php
/**
 * Will run the KAsyncDirectoryCleanup 
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
require_once(__DIR__ . "/../../../../../batch/bootstrap.php");

$instance = new KAsyncTagResolve ();
$instance->run(); 
$instance->done();
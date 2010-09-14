<?php
require_once("bootstrap.php");

/**
 * Will run the KAsyncDbCleanup 
 *
 * @package Scheduler
 * @subpackage Cleanup
 */

$instance = new KAsyncDbCleanup ( );
$instance->run(); 
$instance->done();
?>
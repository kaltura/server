<?php
/**
 * Will run the KAsyncDbCleanup 
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncServerNodeMonitor();
$instance->run(); 
$instance->done();

<?php
/**
 * Will run the KAsyncBalancer 
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
require_once("bootstrap.php");

$instance = new KAsyncJobSuspender();
$instance->run(); 
$instance->done();

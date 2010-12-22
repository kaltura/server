<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeFetchReport
 * 
 * @package Scheduler
 * @subpackage Distribute
 */

$instance = new KAsyncDistributeFetchReport();
$instance->run(); 
$instance->done();

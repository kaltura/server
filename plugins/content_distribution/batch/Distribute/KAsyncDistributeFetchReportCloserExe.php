<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeFetchReportCloser
 * 
 * @package Scheduler
 * @subpackage Distribute
 */

$instance = new KAsyncDistributeFetchReportCloser();
$instance->run(); 
$instance->done();

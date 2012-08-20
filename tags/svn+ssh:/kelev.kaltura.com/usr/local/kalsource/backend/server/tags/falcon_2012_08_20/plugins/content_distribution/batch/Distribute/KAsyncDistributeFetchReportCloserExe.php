<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeFetchReportCloser
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeFetchReportCloser();
$instance->run(); 
$instance->done();

<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeFetchReport
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeFetchReport();
$instance->run(); 
$instance->done();

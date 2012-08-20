<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncSynchronizeDistribution
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncSynchronizeDistribution();
$instance->run(); 
$instance->done();

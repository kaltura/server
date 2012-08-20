<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeSubmit
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeSubmit();
$instance->run(); 
$instance->done();

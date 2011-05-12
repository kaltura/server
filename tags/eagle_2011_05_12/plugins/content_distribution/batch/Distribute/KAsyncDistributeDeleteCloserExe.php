<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeDeleteCloser
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeDeleteCloser();
$instance->run(); 
$instance->done();

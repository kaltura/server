<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeUpdateCloser
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeUpdateCloser();
$instance->run(); 
$instance->done();

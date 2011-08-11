<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeDisableCloser
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeDisableCloser();
$instance->run(); 
$instance->done();

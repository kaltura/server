<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeEnableCloser
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeEnableCloser();
$instance->run(); 
$instance->done();

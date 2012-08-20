<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeDisable
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeDisable();
$instance->run(); 
$instance->done();

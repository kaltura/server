<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeUpdate
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeUpdate();
$instance->run(); 
$instance->done();

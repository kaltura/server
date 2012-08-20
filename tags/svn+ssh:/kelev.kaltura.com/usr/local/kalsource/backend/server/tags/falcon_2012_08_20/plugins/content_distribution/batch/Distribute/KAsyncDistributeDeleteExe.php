<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeDelete
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeDelete();
$instance->run(); 
$instance->done();

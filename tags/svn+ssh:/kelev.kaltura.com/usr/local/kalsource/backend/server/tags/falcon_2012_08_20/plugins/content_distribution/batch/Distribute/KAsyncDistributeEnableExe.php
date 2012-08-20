<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeEnable
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeEnable();
$instance->run(); 
$instance->done();

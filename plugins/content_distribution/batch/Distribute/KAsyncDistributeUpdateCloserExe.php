<?php
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncDistributeUpdateCloser
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeUpdateCloser();
$instance->run(); 
$instance->done();

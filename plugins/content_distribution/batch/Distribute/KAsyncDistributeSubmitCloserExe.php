<?php
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncDistributeSubmitCloser
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeSubmitCloser();
$instance->run(); 
$instance->done();

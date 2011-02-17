<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeSubmitCloser
 * 
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */

$instance = new KAsyncDistributeSubmitCloser();
$instance->run(); 
$instance->done();

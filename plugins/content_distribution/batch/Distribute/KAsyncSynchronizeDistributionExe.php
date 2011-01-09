<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncSynchronizeDistribution
 * 
 * @package Scheduler
 * @subpackage Distribute
 */

$instance = new KAsyncSynchronizeDistribution();
$instance->run(); 
$instance->done();

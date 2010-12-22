<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeDeleteCloser
 * 
 * @package Scheduler
 * @subpackage Distribute
 */

$instance = new KAsyncDistributeDeleteCloser();
$instance->run(); 
$instance->done();

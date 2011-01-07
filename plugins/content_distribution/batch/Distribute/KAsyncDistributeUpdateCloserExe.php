<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeUpdateCloser
 * 
 * @package Scheduler
 * @subpackage Distribute
 */

$instance = new KAsyncDistributeUpdateCloser();
$instance->run(); 
$instance->done();

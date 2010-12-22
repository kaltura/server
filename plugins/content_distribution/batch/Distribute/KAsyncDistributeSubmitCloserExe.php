<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeSubmitCloser
 * 
 * @package Scheduler
 * @subpackage Distribute
 */

$instance = new KAsyncDistributeSubmitCloser();
$instance->run(); 
$instance->done();

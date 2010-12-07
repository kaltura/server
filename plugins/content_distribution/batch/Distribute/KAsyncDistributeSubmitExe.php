<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeSubmit
 * 
 * @package Scheduler
 * @subpackage Distribute
 */

$instance = new KAsyncDistributeSubmit();
$instance->run(); 
$instance->done();
?>
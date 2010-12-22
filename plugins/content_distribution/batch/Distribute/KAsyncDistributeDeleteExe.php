<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeDelete
 * 
 * @package Scheduler
 * @subpackage Distribute
 */

$instance = new KAsyncDistributeDelete();
$instance->run(); 
$instance->done();

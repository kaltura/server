<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncDistributeUpdate
 * 
 * @package Scheduler
 * @subpackage Distribute
 */

$instance = new KAsyncDistributeUpdate();
$instance->run(); 
$instance->done();

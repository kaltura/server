<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncVirusScan
 * 
 * @package Scheduler
 * @subpackage VirusScan
 */

$instance = new KAsyncVirusScan();
$instance->run(); 
$instance->done();
?>
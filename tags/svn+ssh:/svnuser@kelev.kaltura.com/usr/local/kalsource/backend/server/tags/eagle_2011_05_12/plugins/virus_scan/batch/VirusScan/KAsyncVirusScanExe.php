<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncVirusScan
 * 
 * @package plugins.virusScan
 * @subpackage Scheduler
 */

$instance = new KAsyncVirusScan();
$instance->run(); 
$instance->done();
?>
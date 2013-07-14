<?php
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

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
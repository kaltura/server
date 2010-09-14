<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncImport
 * 
 * @package Scheduler
 * @subpackage Import
 */

$instance = new KAsyncImport();
$instance->run(); 
$instance->done();
?>
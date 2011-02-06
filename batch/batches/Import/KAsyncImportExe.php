<?php
/**
 * Executes the KAsyncImport
 * 
 * @package Scheduler
 * @subpackage Import
 */
require_once("bootstrap.php");

$instance = new KAsyncImport();
$instance->run(); 
$instance->done();

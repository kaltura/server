<?php
/**
 * Executes the KAsyncImport
 * 
 * @package Scheduler
 * @subpackage Import
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncImport();
$instance->run(); 
$instance->done();

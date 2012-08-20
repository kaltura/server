<?php
/**
 * Executes the KAsyncIndex
 * 
 * @package Scheduler
 * @subpackage Index
 */
require_once("bootstrap.php");

$instance = new KAsyncIndex();
$instance->run(); 
$instance->done();

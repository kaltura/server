<?php
/**
 * Executes the KAsyncDelete
 * 
 * @package Scheduler
 * @subpackage Delete
 */
require_once("bootstrap.php");

$instance = new KAsyncDelete();
$instance->run(); 
$instance->done();

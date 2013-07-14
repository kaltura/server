<?php
/**
 * Executes the KAsyncDelete
 * 
 * @package Scheduler
 * @subpackage Delete
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncDelete();
$instance->run(); 
$instance->done();

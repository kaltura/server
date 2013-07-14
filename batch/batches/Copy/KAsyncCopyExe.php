<?php
/**
 * Executes the KAsyncCopy
 * 
 * @package Scheduler
 * @subpackage Copy
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncCopy();
$instance->run(); 
$instance->done();

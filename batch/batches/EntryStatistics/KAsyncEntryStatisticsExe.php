<?php
/**
 * Will run KAsyncEntryStatistics 
 *
 * @package Scheduler
 * @subpackage Statistics
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncEntryStatistics();
$instance->run(); 
$instance->done();

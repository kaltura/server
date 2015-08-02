<?php
/**
 * Executes the KAsyncRecalculateCache
 * 
 * @package Scheduler
 * @subpackage RecalculateCache
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncRecalculateCache();
$instance->run(); 
$instance->done();

<?php
/**
 * Executes the KAsyncMoveCategoryEntries
 * 
 * @package Scheduler
 * @subpackage MoveCategoryEntries
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncMoveCategoryEntries();
$instance->run(); 
$instance->done();

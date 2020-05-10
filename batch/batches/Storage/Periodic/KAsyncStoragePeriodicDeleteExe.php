<?php
/**
 * Will run KAsyncStoragePeriodicDelete.class.php
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */
require_once(__DIR__ . "/../../../bootstrap.php");

$instance = new KAsyncStoragePeriodicDelete();
$instance->run(); 
$instance->done();

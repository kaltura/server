<?php
/**
 * Will run KAsyncStoragePeriodicExport.class.php
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */
require_once(__DIR__ . "/../../../bootstrap.php");

$instance = new KAsyncStoragePeriodicExport();
$instance->run(); 
$instance->done();

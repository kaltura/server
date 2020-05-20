<?php
/**
 * Will run KAsyncStoragePeriodicPurge.class.php
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */
require_once(__DIR__ . "/../../../bootstrap.php");

$instance = new KAsyncStoragePeriodicPurge();
$instance->run(); 
$instance->done();

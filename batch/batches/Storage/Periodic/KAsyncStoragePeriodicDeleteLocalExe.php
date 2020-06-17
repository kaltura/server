<?php
/**
 * Will run KAsyncStoragePeriodicDeleteLocal.class.php
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */
require_once(__DIR__ . "/../../../bootstrap.php");

$instance = new KAsyncStoragePeriodicDeleteLocal();
$instance->run(); 
$instance->done();

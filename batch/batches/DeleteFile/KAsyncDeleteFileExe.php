<?php
/**
 * Will run KAsyncStorageDelete.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncDeleteFile();
$instance->run(); 
$instance->done();
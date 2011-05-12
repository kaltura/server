<?php
/**
 * Will run KAsyncStorageExport.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */
require_once("bootstrap.php");

$instance = new KAsyncStorageExport();
$instance->run(); 
$instance->done();

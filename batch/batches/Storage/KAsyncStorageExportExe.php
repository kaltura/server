<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncStorageExport.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */


$instance = new KAsyncStorageExport();
$instance->run(); 
$instance->done();
?>
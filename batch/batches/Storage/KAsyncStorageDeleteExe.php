<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncStorageDelete.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */


$instance = new KAsyncStorageDelete();
$instance->run(); 
$instance->done();
?>
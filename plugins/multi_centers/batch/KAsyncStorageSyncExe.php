<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncStorageSync.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */


$instance = new KAsyncStorageSync();
$instance->run(); 
$instance->done();
?>
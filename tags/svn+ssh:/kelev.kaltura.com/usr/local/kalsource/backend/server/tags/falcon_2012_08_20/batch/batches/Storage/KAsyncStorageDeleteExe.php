<?php
/**
 * Will run KAsyncStorageDelete.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Storage
 */

chdir(dirname( __FILE__ ) . "/../../");
require_once("bootstrap.php");

$instance = new KAsyncStorageDelete();
$instance->run(); 
$instance->done();

<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncPull 
 *
 * 
 * @package Scheduler
 * @subpackage Pull
 */

$instance = new KAsyncPull();
$instance->run(); 
$instance->done();
?>
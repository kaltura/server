<?php
/**
 * Will run KAsyncRemoteConvertCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once("bootstrap.php");

$instance = new KAsyncRemoteConvertCloser();
$instance->run(); 
$instance->done();

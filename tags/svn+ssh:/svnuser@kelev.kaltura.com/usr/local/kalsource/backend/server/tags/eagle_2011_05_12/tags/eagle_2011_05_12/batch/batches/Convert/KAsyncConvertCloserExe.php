<?php
/**
 * Will run KAsyncConvertCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once("bootstrap.php");

$instance = new KAsyncConvertCloser();
$instance->run(); 
$instance->done();

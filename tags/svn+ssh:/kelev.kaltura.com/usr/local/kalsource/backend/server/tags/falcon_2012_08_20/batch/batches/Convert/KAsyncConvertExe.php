<?php
/**
 * Will run KAsyncConvert
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once("bootstrap.php");

$instance = new KAsyncConvert();
$instance->run(); 
$instance->done();

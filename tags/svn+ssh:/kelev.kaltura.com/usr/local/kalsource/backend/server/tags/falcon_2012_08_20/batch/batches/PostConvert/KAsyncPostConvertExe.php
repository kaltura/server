<?php
/**
 * Will run KAsyncPostConvert
 *
 * 
 * @package Scheduler
 * @subpackage Post-Convert
 */
require_once("bootstrap.php");

$instance = new KAsyncPostConvert();
$instance->run(); 
$instance->done();

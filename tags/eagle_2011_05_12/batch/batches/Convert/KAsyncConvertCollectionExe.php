<?php
/**
 * Will run KAsyncConvertCollection
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once("bootstrap.php");

$instance = new KAsyncConvertCollection();
$instance->run(); 
$instance->done();

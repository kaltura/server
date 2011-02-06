<?php
/**
 * Will run KAsyncConvertCollectionCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once("bootstrap.php");

$instance = new KAsyncConvertCollectionCloser();
$instance->run(); 
$instance->done();

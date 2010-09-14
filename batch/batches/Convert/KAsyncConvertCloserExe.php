<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncConvertCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */

$instance = new KAsyncConvertCloser();
$instance->run(); 
$instance->done();
?>
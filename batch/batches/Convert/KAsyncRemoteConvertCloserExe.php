<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncRemoteConvertCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */

$instance = new KAsyncRemoteConvertCloser();
$instance->run(); 
$instance->done();
?>
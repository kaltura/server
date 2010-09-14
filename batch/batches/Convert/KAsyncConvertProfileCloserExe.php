<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncConvertProfileCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */

$instance = new KAsyncConvertProfileCloser();
$instance->run(); 
$instance->done();
?>
<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncPostConvert
 *
 * 
 * @package Scheduler
 * @subpackage Post-Convert
 */

$instance = new KAsyncPostConvert();
$instance->run(); 
$instance->done();
?>
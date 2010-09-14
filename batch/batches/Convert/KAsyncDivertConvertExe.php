<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncConvert
 *
 * @package Scheduler
 * @subpackage Conversion
 */

$instance = new KAsyncDivertConvert();
$instance->run(); 
$instance->done();
?>
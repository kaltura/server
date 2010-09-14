<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncConvertCollection
 *
 * @package Scheduler
 * @subpackage Conversion
 */

$instance = new KAsyncConvertCollection();
$instance->run(); 
$instance->done();
?>
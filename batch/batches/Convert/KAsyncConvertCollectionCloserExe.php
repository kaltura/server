<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncConvertCollectionCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */

$instance = new KAsyncConvertCollectionCloser();
$instance->run(); 
$instance->done();
?>
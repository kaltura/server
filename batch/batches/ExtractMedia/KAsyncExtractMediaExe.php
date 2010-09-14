<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncExtractMedia.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Extract-Media
 */


$instance = new KAsyncExtractMedia();
$instance->run(); 
$instance->done();
?>
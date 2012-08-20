<?php
/**
 * Will run KAsyncExtractMedia.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Extract-Media
 */
require_once("bootstrap.php");

$instance = new KAsyncExtractMedia();
$instance->run(); 
$instance->done();

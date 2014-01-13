<?php
/**
 * Will run KAsyncConvertLiveSegment.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncConvertLiveSegment();
$instance->run(); 
$instance->done();
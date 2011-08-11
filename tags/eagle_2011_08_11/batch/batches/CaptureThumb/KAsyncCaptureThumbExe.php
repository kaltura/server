<?php
/**
 * Will run KAsyncCaptureThumb
 *
 * 
 * @package Scheduler
 * @subpackage Capture-Thumbnail
 */
chdir(dirname(__FILE__) . '/../../');
require_once("bootstrap.php");

$instance = new KAsyncCaptureThumb();
$instance->run(); 
$instance->done();

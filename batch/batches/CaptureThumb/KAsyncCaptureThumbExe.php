<?php
chdir(dirname(__FILE__) . '/../../');
require_once("bootstrap.php");
/**
 * Will run KAsyncCaptureThumb
 *
 * 
 * @package Scheduler
 * @subpackage Capture-Thumbnail
 */

$instance = new KAsyncCaptureThumb();
$instance->run(); 
$instance->done();

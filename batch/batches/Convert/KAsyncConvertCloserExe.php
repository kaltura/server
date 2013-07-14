<?php
/**
 * Will run KAsyncConvertCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncConvertCloser();
$instance->run(); 
$instance->done();

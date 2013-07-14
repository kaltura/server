<?php
/**
 * Will run KAsyncConvertProfileCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncConvertProfileCloser();
$instance->run(); 
$instance->done();

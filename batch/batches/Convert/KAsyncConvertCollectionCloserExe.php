<?php
/**
 * Will run KAsyncConvertCollectionCloser
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncConvertCollectionCloser();
$instance->run(); 
$instance->done();

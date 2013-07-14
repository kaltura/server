<?php
/**
 * Will run KAsyncConvertCollection
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncConvertCollection();
$instance->run(); 
$instance->done();

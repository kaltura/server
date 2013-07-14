<?php
/**
 * Will run KAsyncPostConvert
 *
 * 
 * @package Scheduler
 * @subpackage Post-Convert
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncPostConvert();
$instance->run(); 
$instance->done();

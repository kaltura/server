<?php
/**
 * Will run KAsyncConcat.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Conversion
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncConcat();
$instance->run(); 
$instance->done();
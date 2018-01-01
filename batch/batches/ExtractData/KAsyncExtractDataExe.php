<?php
/**
 * Will run KAsyncExtractData.class.php
 * 
 *
 * @package Scheduler
 * @subpackage Extract-Data
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncExtractData();
$instance->run(); 
$instance->done();

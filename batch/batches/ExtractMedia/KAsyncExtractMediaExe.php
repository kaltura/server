<?php
/**
 * Will run KAsyncExtractMedia.class.php 
 * 
 *
 * @package Scheduler
 * @subpackage Extract-Media
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncExtractMedia();
$instance->run(); 
$instance->done();

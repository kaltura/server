<?php
/**
 * Will run the KAsyncEmailIngestion
 *
 * @package Scheduler
 * @subpackage Email-Ingestion
 */
require_once('bootstrap.php');

$instance = new KAsyncEmailIngestion();
$instance->run();
$instance->done();

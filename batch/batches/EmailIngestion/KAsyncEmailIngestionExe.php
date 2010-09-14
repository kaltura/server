<?php
require_once('bootstrap.php');

/**
 * Will run the KAsyncEmailIngestion
 *
 * @package Scheduler
 * @subpackage Email-Ingestion
 */

$instance = new KAsyncEmailIngestion();
$instance->run();
$instance->done();


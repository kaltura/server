<?php
/**
 * Will run the KAsyncEmailIngestion
 *
 * @package Scheduler
 * @subpackage Email-Ingestion
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncEmailIngestion();
$instance->run();
$instance->done();

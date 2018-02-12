<?php
/**
 * Executes the KAsyncUsersCsv
 *
 * @package Scheduler
 * @subpackage Users-Csv
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncUsersCsv();
$instance->run();
$instance->done();
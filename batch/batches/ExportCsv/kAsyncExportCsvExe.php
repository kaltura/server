<?php
/**
 * Executes the KAsyncUsersCsv
 *
 * @package Scheduler
 * @subpackage Users-Csv
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncExportCsv();
$instance->run();
$instance->done();
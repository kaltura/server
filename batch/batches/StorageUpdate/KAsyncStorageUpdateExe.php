<?php
/**
 * Will run the KAsyncStorageUpdate
 *
 * @package Scheduler
 * @subpackage StorageUpdate
 */
require_once(__DIR__ . '/../../bootstrap.php');

$instance = new KAsyncStorageUpdate();
$instance->run();
$instance->done();

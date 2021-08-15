<?php
/**
 * Will run the KAsyncSyncSubscriptions
 *
 * @package plugins.chargeBee
 * @subpackage syncSubscriptions
 */
require_once(__DIR__ . '/../../../../../batch/bootstrap.php');

$instance = new KAsyncSyncSubscriptions();
$instance->run();
$instance->done();

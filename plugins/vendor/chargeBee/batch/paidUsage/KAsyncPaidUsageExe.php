<?php
/**
 * Will run the KAsyncPaidUsage
 *
 * @package plugins.chargeBee
 * @subpackage paidUsage
 */
require_once(__DIR__ . '/../../../../../batch/bootstrap.php');

$instance = new KAsyncPaidUsage();
$instance->run();
$instance->done();

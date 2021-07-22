<?php
/**
 * Will run the KAsyncFreeTrialUsage
 *
 * @package plugins.chargeBee
 * @subpackage freeTrialUsage
 */
require_once(__DIR__ . '/../../../../../batch/bootstrap.php');

$instance = new KAsyncFreeTrialUsage();
$instance->run();
$instance->done();

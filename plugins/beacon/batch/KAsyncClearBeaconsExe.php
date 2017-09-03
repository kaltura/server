<?php

require_once(__DIR__ . "/../../../batch/bootstrap.php");

/**
 * @package plugins.beacons
 * @subpackage Scheduler
 */

$instance = new KAsyncClearBeacons();
$instance->run();
$instance->done();
<?php
/**
 * @package plugins.reach
 * @subpackage Scheduler
 */
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

$instance = new KSyncReachCreditTaskRunner();
$instance->run();
$instance->done();

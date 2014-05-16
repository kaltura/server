<?php
/**
 * @package plugins.scheduledTask
 * @subpackage Scheduler
 */
require_once(__DIR__ . "/../../../../batch/bootstrap.php");

$instance = new KScheduledTaskRunner();
$instance->run();
$instance->done();

<?php
/**
 * Will run KScheduleHelper 
 *
 * @package Scheduler
 */
require_once(__DIR__ . "/../bootstrap.php");

$instance = new KScheduleHelper();
$instance->run(); 
$instance->done();

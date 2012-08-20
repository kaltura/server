<?php
/**
 * Will run KScheduleHelper 
 *
 * @package Scheduler
 */
require_once("bootstrap.php");

$instance = new KScheduleHelper();
$instance->run(); 
$instance->done();

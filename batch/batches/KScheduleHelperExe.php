<?php
require_once("bootstrap.php");
/**
 * Will run KScheduleHelper 
 *
 * @package Scheduler
 */

$instance = new KScheduleHelper();
$instance->run(); 
$instance->done();
?>
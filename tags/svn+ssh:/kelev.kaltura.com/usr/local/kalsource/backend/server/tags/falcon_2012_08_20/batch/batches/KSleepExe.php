<?php
/**
 * Will run KSleep
 * 
 * @package Scheduler
 * @subpackage Debug
 */
require_once("bootstrap.php");

$instance = new KSleep();
$instance->run(); 
$instance->done();

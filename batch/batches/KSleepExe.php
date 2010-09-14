<?php
require_once("bootstrap.php");

/**
 * Will run KSleep
 * 
 * @package Scheduler
 * @subpackage Debug
 */

$instance = new KSleep();
$instance->run(); 
$instance->done();

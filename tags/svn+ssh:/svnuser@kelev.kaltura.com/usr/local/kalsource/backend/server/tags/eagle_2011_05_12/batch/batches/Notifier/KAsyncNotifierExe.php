<?php
/**
 * Will run KAsyncNotifier 
 * 
 * 
 * @package Scheduler
 * @subpackage Notifier
 */
require_once("bootstrap.php");

$instance = new KAsyncNotifier ( );
$instance->run(); 
$instance->done();

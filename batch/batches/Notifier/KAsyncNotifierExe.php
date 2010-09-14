<?php
require_once("bootstrap.php");

/**
 * Will run KAsyncNotifier 
 * 
 * 
 * @package Scheduler
 * @subpackage Notifier
 */

$instance = new KAsyncNotifier ( );
$instance->run(); 
$instance->done();
?>
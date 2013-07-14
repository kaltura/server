<?php
/**
 * Will run KAsyncNotifier 
 * 
 * 
 * @package Scheduler
 * @subpackage Notifier
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncNotifier ( );
$instance->run(); 
$instance->done();

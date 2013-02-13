<?php
/**
 * Will run KAsyncProvisionProvide
 *
 * @package Scheduler
 * @subpackage Provision
 */
require_once("bootstrap.php");

$instance = new KAsyncProvisionProvideCloser();
$instance->run(); 
$instance->done();

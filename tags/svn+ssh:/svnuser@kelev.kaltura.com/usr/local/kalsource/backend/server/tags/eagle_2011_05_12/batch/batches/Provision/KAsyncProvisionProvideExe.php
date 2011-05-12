<?php
/**
 * Will run KAsyncProvisionProvide
 *
 * @package Scheduler
 * @subpackage Provision
 */
require_once("bootstrap.php");

$instance = new KAsyncProvisionProvide();
$instance->run(); 
$instance->done();

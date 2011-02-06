<?php
/**
 * Will run KAsyncProvisionDelete
 *
 * @package Scheduler
 * @subpackage Provision
 */
require_once("bootstrap.php");

$instance = new KAsyncProvisionDelete();
$instance->run(); 
$instance->done();

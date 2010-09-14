<?php
require_once("bootstrap.php");
/**
 * Will run KAsyncProvisionProvide
 *
 * @package Scheduler
 * @subpackage Provision
 */

$instance = new KAsyncProvisionProvide();
$instance->run(); 
$instance->done();
?>
<?php
require_once("bootstrap.php");

/**
 * Executes the KAsyncMailer
 *
 * @package Scheduler
 * @subpackage Mailer
 */

$instance = new KAsyncMailer();
$instance->run(); 
$instance->done();
?>
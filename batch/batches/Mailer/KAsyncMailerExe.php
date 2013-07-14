<?php
/**
 * Executes the KAsyncMailer
 *
 * @package Scheduler
 * @subpackage Mailer
 */
require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncMailer();
$instance->run(); 
$instance->done();

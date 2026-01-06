<?php
/**
 * Run KAsyncUpdateUserEntries
 *
 * @package Scheduler
 * @subpackage UpdateUserEntries
 */

require_once(__DIR__ . "/../../bootstrap.php");

$instance = new KAsyncUpdateUserEntries();
$instance->run();
$instance->done();

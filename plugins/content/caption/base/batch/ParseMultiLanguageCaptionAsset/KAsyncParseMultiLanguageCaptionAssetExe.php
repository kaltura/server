<?php
require_once(__DIR__ . "/../../../../../../batch/bootstrap.php");

/**
 * Executes the KAsyncParseMultiLanguageCaptionAsset
 * 
 * @package plugins.caption
 * @subpackage Scheduler
 */

$instance = new KAsyncParseMultiLanguageCaptionAsset();
$instance->run(); 
$instance->done();

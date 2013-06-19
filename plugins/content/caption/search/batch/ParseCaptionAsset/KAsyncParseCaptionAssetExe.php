<?php
require_once(__DIR__ . "/../../../../../bootstrap.php");

/**
 * Executes the KAsyncParseCaptionAsset
 * 
 * @package plugins.captionSearch
 * @subpackage Scheduler
 */

$instance = new KAsyncParseCaptionAsset();
$instance->run(); 
$instance->done();

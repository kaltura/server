<?php
/**
 * @package deployment
 *
 * Deploy new language (italian) flavor for live
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');
$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/ini_files/2022_05_31_additional_live_languages_ita.liveParams.ini';
passthru("php $script $config");

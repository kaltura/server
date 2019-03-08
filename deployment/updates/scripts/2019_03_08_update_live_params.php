<?php
/**
 * @package deployment
 * @subpackage live.liveParams
 *
 * Add live params
 *
 * No need to re-run after server code deploy
 */

$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/init_data/04.liveParams.ini';
passthru("php $script $config");

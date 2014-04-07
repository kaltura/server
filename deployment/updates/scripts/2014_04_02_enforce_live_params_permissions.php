<?php
/**
 * @package deployment
 * @subpackage live.liveParams
 *
 * Enforce live-params permissions
 *
 * No need to re-run after server code deploy
 */

$script = realpath(__DIR__ . '/../../') . '/base/scripts/insertDefaults.php';
$config = realpath(__DIR__ . '/../../') . '/base/scripts/init_data/04.liveParams.ini';
passthru("php $script $config");

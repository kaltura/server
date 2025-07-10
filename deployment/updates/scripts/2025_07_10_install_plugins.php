<?php
/**
 * @package deployment
 */

$script = realpath(dirname(__FILE__) . '/../../') . '/base/scripts/installPlugins.php';
passthru("php $script");

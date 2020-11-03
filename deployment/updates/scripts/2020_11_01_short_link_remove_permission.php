<?php

/**
* @package deployment
* @subpackage plugins.shortLink
* Remove shortLink plugin permissions
*/

$script = realpath(dirname(__FILE__) . '/../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/deployment/updates/scripts/ini_files/2020_11_01_shortlink_update_permission.ini';
passthru("php $script $config");
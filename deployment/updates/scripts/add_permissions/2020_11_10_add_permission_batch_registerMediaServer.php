<?php


/**
 * @package deployment
 *
 * Add permissions to batch client to registerMediaServer
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.livestream.ini';
passthru("php $script $config");
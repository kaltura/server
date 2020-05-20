<?php
/**
 * @package deployment
 * Add permissions to lockFileSyncs
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.filesync.filesync.ini';
passthru("php $script $config");
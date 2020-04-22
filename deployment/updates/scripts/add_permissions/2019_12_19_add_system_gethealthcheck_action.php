<?php
/**
 * @package deployment
 * Add permissions to partner get
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.system.ini';
passthru("php $script $config");
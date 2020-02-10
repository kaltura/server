<?php
/**
 * @package deployment
 * @subpackage orion.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/init_data/01.Partner.ini';
passthru("php $script $config");

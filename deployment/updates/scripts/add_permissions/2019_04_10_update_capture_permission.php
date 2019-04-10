<?php
/**
 * @package deployment
 * @subpackage naos.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.partner.ini';
passthru("php $addPermissionsScript $addConfig");

$insertDefaultsScript = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/init_data/03.UserRole.ini';
passthru("php $insertDefaultsScript $config");
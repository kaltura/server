<?php
/**
 * @package deployment
 * @subpackage orion.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/removePermissionsAndItems.php';

$removeConfig = realpath(dirname(__FILE__)) . '/../ini_files/2019_06_16_sip_update_permissions.ini';
passthru("php $addPermissionsScript $removeConfig");

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.sip.pexip.ini';
passthru("php $addPermissionsScript $addConfig");   
<?php
/**
 * @package deployment
 * @subpackage scorpius.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.virtualevent.virtualevent.ini';
passthru("php $addPermissionsScript $addConfig");

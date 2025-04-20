<?php
/**
 * @package deployment
 * @subpackage ursa.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.baseentry.ini';
passthru("php $addPermissionsScript $config");

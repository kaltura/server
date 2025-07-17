<?php
/**
 * @package deployment
 * @subpackage ursa.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.session.ini';
passthru("php $script $addConfig");

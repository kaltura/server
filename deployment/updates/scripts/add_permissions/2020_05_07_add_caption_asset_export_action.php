<?php
/**
 * @package deployment
 * @subpackage propus.roles_and_permissions
 */
$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$addConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.caption.captionasset.ini';
passthru("php $addPermissionsScript $addConfig");
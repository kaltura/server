<?php
/**
 * @package deployment
 * @subpackage Rigel.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionToRole.php';
passthru("php $script 0 'EP user analytics role' 'LIVE_STREAM_UPDATE' realrun");
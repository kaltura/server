<?php
/**
 * @package deployment
 * @subpackage Quasar.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionToRole.php';
passthru("php $script null 'PLAYBACK BASE ROLE' 'WIDGET_SESSION_PERMISSION' realrun");
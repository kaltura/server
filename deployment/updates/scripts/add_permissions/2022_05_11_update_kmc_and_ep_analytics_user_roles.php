<?php
/**
 * @package deployment
 * @subpackage Quasar.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionToRole.php';
passthru("php $script 0 'Kmc analytics role' 'STUDIO_BASE' realrun");
passthru("php $script 0 'EP user analytics role' 'TRANSCODING_BASE,BASE_USER_SESSION_PERMISSION' realrun");
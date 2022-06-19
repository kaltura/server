<?php
/**
 * @package deployment
 * @subpackage Rigel.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionToRole.php';
passthru("php $script 0 'Kmc analytics role' 'STUDIO_BASE' realrun");
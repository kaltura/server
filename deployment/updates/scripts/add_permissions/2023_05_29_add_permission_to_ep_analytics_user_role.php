<?php
/**
 * @package deployment
 * @subpackage Scorpius.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionToRole.php';
passthru("php $script 0 'EP user analytics role' 'SCHEDULE_EVENT_BASE' realrun");
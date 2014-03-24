<?php
/**
 * @package deployment
 * @subpackage gemini.roles_and_permissions
 *
 * Adds SYSTEM_ADMIN_PUBLISHER_CONFIG to service.audit.audittrail get/list
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/service.audit.audittrail.ini';
passthru("php $script $config");


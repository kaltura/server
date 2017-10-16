<?php
/**
 * @package deployment
 * @subpackage jupiter.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.schedule.scheduleResource.ini';
passthru("php $script $config");

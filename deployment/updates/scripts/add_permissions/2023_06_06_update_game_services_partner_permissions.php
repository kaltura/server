<?php
/**
 * @package deployment
 * @subpackage Scorpius.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.report.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.schedule.scheduleEvent.ini';
passthru("php $script $config");

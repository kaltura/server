<?php
/**
 * @package deployment
 * @subpackage naos.roles_and_permissions
 */

$addPermissionsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$scheduleResourceConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.schedule.scheduleEventResource.ini';
$liveStreamConfig = realpath(dirname(__FILE__)) . '/../../../permissions/service.livestream.ini';
passthru("php $addPermissionsScript $scheduleResourceConfig");
passthru("php $addPermissionsScript $liveStreamConfig");

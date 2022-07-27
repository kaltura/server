<?php
/**
 * @package deployment
 * Add permissions for updateLiveFeature action
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$insertDefaultsScript = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__) . '/../../../') . '/permissions/partner.0.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/init_data/03.UserRole.ini';
passthru("php $insertDefaultsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.schedule.scheduleEvent.ini';
passthru("php $addPermissionsAndItemsScript $config");
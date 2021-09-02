<?php
/**
 * @package deployment
 * Add permission -2>PARTNER_-2_GROUP_*_PERMISSION to service: reach entry vendor task action: update
 * Add permission -2>REACH_ADMIN_PERMISSION to object: reach entry vendor task parameter: status
 * Add permission -2>REACH_ADMIN_PERMISSION to object: reach entry vendor task parameter: errDescription
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.reach.entryVendorTask.ini';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaEntryVendorTask.ini';
passthru("php $script $config");
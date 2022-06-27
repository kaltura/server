<?php
/**
 * @package deployment
 * Add permission REACH_VENDOR_PARTNER to new prop externalTaskId (insert and update)
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaEntryVendorTask.ini';
passthru("php $script $config");
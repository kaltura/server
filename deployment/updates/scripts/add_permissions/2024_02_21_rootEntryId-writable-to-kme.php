<?php
/**
 * @package deployment
 *
 * Add permissions to update rootEntryId */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaBaseEntry.ini';
passthru("php $script $config");
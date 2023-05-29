<?php
/**
 * @package deployment
 * Add permissions to add sessionCuePoint
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaSessionCuePoint.ini';
passthru("php $script $config");
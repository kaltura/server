<?php
/**
 * @package deployment
 * Add permissions to privileges field on widget object
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaWidget.ini';
passthru("php $script $config");
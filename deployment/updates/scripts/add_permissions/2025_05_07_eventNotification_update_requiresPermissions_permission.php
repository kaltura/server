<?php
/**
 * @package deployment
 * @subpackage scorpius.roles_and_permissions
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaKafkaNotificationTemplate.ini';
passthru("php $script $config");

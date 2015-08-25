<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 * 
 * Added permission to attachment asset
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.attachment.attachmentasset.ini';
passthru("php $script $config");
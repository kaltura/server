<?php
/**
 * @package deployment
 * @subpackage falcon.roles_and_permissions
 * 
 * Add externalmedia->add permission-item to BASE_USER_SESSION_PERMISSION and CONTENT_INGEST_UPLOAD permissions
 */

 
$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.groupuser.ini';
passthru("php $script $config");
 

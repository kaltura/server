<?php
/**
 * @package deployment
 * @subpackage qna.roles_and_permissions
 *
 * Add qna user role and permission
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.metadata.metadata.ini';
passthru("php $addPermissionsAndItemsScript $config");
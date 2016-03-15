<?php
/**
 * @package deployment
 * @subpackage qna.roles_and_permissions
 *
 * Add qna user role and permission
 */

$addPermissionsAndItemsScript = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$insertDefaultsScript = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/insertDefaults.php';

$config = realpath(dirname(__FILE__)) . '/../../../permissions/partner.0.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__) . '/../../../') . '/base/scripts/init_data/03.UserRole.ini';
passthru("php $insertDefaultsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaAnnotationCuePoint.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/object.KalturaWidget.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.metadata.metadata.ini';
passthru("php $addPermissionsAndItemsScript $config");

$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.metadata.metadataprofile.ini';
passthru("php $addPermissionsAndItemsScript $config");